<?php

namespace App\Http\Controllers;

use App\Models\API;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variation;
use App\Services\ApiAvailabilityMonitorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class APIController extends Controller
{
    public function pullApiProducts(Request $request)
    {
        try {
            $validated = $request->validate([
                'api_id' => ['required', 'integer', 'exists:a_p_is,id'],
                'category_id' => ['nullable', 'integer', 'exists:categories,id'],
                'category_slug' => ['nullable', 'string'],
            ]);

            $api = API::query()->findOrFail($validated['api_id']);
            $controller = resolveProviderController($api);

            if (! $controller || ! method_exists($controller, 'pullProducts')) {
                return back()->with('error', 'This provider does not support product pulling.');
            }

            $category = null;
            $categorySlug = trim((string) $request->input('category_slug', ''));

            if ($categorySlug !== '') {
                $category = Category::query()->where('slug', $categorySlug)->first();
            }

            if (! $category && ! empty($validated['category_id'])) {
                $category = Category::query()->find($validated['category_id']);
            }

            if (! $category) {
                return back()->with('error', 'Please select a category or enter a valid category slug.');
            }

            $payload = array_merge($request->all(), [
                'api_id' => $api->id,
                'category_id' => $category->id,
                'categorySlug' => $categorySlug !== '' ? $categorySlug : $category->slug,
                'category_unique_element' => $category->unique_element ?? null,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'unique_element' => $category->unique_element ?? null,
                ],
            ]);

            $res = $controller->pullProducts($payload, $api);

            if (! is_array($res)) {
                return back()->with('error', 'The provider did not return a valid response.');
            }

            if (($res['status'] ?? null) !== 'success') {
                return back()->with('error', $res['message'] ?? 'Unable to pull products from the selected provider.');
            }

            $products = $res['products'] ?? [];

            if (! is_array($products) || empty($products)) {
                return back()->with('error', 'No normalized products were returned by the provider.');
            }

            $saved = 0;
            $updated = 0;

            foreach ($products as $productData) {
                if (! is_array($productData)) {
                    continue;
                }

                if (blank(data_get($productData, 'slug')) || blank(data_get($productData, 'name'))) {
                    continue;
                }

                $existingProduct = $this->findPulledProduct($api, $productData);
                $productAttributes = $this->mapPulledProductAttributes($productData, $api, $category);

                if ($existingProduct) {
                    $productAttributes['api_price'] = $existingProduct->api_price;
                    $productAttributes['system_price'] = $existingProduct->system_price;
                    $productAttributes['status'] = $existingProduct->status;

                    if ($this->shouldRaisePulledPrice($existingProduct->system_price, $productAttributes['api_price'])) {
                        $productAttributes['api_price'] = $this->normalizePulledPrice($productData['api_price'] ?? null);
                        $productAttributes['status'] = 'inactive';
                    }

                    $existingProduct->update($productAttributes);
                    $product = $existingProduct;
                } else {
                    $product = Product::create($productAttributes);
                }

                if ($product->wasRecentlyCreated) {
                    $saved++;
                } else {
                    $updated++;
                }

                foreach ($this->extractPulledVariations($productData) as $variationData) {
                    if (! is_array($variationData)) {
                        continue;
                    }

                    if (blank(data_get($variationData, 'slug')) || blank(data_get($variationData, 'system_name'))) {
                        continue;
                    }

                    $existingVariation = $this->findPulledVariation($api, $product, $variationData);
                    $variationAttributes = $this->mapPulledVariationAttributes($variationData, $api, $category, $product);

                    if ($existingVariation) {
                        $variationAttributes['api_price'] = $existingVariation->api_price;
                        $variationAttributes['system_price'] = $existingVariation->system_price;
                        $variationAttributes['status'] = $existingVariation->status;

                        if ($this->shouldRaisePulledPrice($existingVariation->system_price, $variationAttributes['api_price'])) {
                            $variationAttributes['api_price'] = $this->normalizePulledPrice($variationData['api_price'] ?? null);
                            $variationAttributes['status'] = 'inactive';
                        }

                        $existingVariation->update($variationAttributes);
                    } else {
                        Variation::create($variationAttributes);
                    }
                }
            }

            return back()->with('message', trim($saved . ' product(s) added' . ($updated > 0 ? ' and ' . $updated . ' updated' : '') . ' successfully.'));
        } catch (\Throwable $th) {
            return back()->with('error', 'No products found: '.$th->getMessage().' '.$th->getLine());
        }

    }

    public function index()
    {
        $apis = API::withCount(['products', 'transactions'])->orderBy('name')->get();
        $availabilityScores = $apis->pluck('availability_score')->filter(fn ($score) => $score !== null);
        $lastCheckedAt = $apis->pluck('availability_checked_at')->filter()->sortDesc()->first();
        $monitorUrl = route('cron.api-availability-monitor', [
            'windowMinutes' => 60,
            'sampleSize' => 20,
        ]);

        $monitorToken = trim((string) env('API_AVAILABILITY_MONITOR_TOKEN', ''));

        if ($monitorToken !== '') {
            $monitorUrl .= (str_contains($monitorUrl, '?') ? '&' : '?') . http_build_query([
                'token' => $monitorToken,
            ]);
        }

        $availabilitySummary = [
            'providers' => $apis->count(),
            'checked_providers' => $apis->whereNotNull('availability_checked_at')->count(),
            'healthy_providers' => $apis->filter(fn (API $api) => in_array($api->availability_status_class, ['stable', 'healthy'], true))->count(),
            'average_score' => $availabilityScores->isNotEmpty() ? (int) round($availabilityScores->avg()) : null,
            'availability_check_transactions_count' => $apis->sum(fn (API $api) => (int) ($api->availability_check_transactions_count ?? 0)),
            'successful_transactions' => $apis->sum(fn (API $api) => (int) ($api->successful_transactions ?? 0)),
            'failed_transactions' => $apis->sum(fn (API $api) => (int) ($api->failed_transactions ?? 0)),
            'last_checked_at' => $lastCheckedAt,
        ];

        return view(themeView('admin', 'api.index'), compact('apis', 'availabilitySummary', 'monitorUrl'));
    }

    public function create()
    {
        return view(themeView('admin', 'api.form'), [
            'api' => null,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'canPullProducts' => false,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "slug" => "required",
            "warning_threshold_status" => "nullable",
            "warning_threshold" => "nullable",
            "status" => "required",
            "file_name" => "required",
            "api_key" => "nullable",
            "sandbox_base_url" => "nullable",
            "live_base_url" => "nullable",
            "secret_key" => "nullable",
            "public_key" => "nullable"
        ]);

        API::updateOrCreate([
            "name" => $request->name,
            "slug" => $request->slug,
            "warning_threshold_status" => $request->warning_threshold_status,
            "warning_threshold" => $request->warning_threshold,
            "status" => $request->status,
            "file_name" => $request->file_name,
            "api_key" => $request->api_key,
            "secret_key" => $request->secret_key,
            "public_key" => $request->public_key,
            "sandbox_base_url" => $request->sandbox_base_url,
            "live_base_url" => $request->live_base_url,
        ]);

        return redirect(route('api.index'))->with('message', 'Added successfully');
    }

    public function edit(API $api)
    {
        return view(themeView('admin', 'api.form'), [
            'api' => $api,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'canPullProducts' => in_array(strtolower((string) $api->slug), ['autosync'], true),
        ]);
    }

    public function update(Request $request, API $api)
    {
        $this->validate($request, [
            "name" => "required",
            "slug" => "required",
            "warning_threshold_status" => "nullable",
            "warning_threshold" => "nullable",
            "status" => "required",
            "file_name" => "required",
            "api_key" => "nullable",
            "secret_key" => "nullable",
            "public_key" => "nullable",
            "sandbox_base_url" => "nullable",
            "live_base_url" => "nullable"
        ]);

        $api->update([
            "name" => $request->name,
            "slug" => $request->slug,
            "warning_threshold_status" => $request->warning_threshold_status,
            "warning_threshold" => $request->warning_threshold,
            "status" => $request->status,
            "file_name" => $request->file_name,
            "api_key" => $request->api_key,
            "secret_key" => $request->secret_key,
            "public_key" => $request->public_key,
            "sandbox_base_url" => $request->sandbox_base_url,
            "live_base_url" => $request->live_base_url,
        ]);

        return back()->with('message', 'Updated successfully');
    }

    public function getBalance(API $api)
    {
        $file_name = $api->file_name;

        try {
            $res = app("App\Http\Controllers\Providers\\" . $file_name)->balance($api);
            //code...
        } catch (\Throwable $th) {
            $res = [
                'status' => 'failed',
                'status_code' => 0,
                'balance' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
            //throw $th;
        }

        $rawBalance = $res['balance'] ?? null;
        $normalizedBalance = null;
        $balanceDisplay = null;

        if (is_numeric($rawBalance)) {
            $normalizedBalance = (float) $rawBalance;
        } elseif (is_string($rawBalance)) {
            $cleanBalance = trim(strip_tags($rawBalance));
            $cleanBalance = preg_replace('/[^\d\.\-]/', '', str_replace(',', '', $cleanBalance));

            if ($cleanBalance !== '' && is_numeric($cleanBalance)) {
                $normalizedBalance = (float) $cleanBalance;
            } else {
                $balanceDisplay = $rawBalance;
            }
        }

        if ($normalizedBalance !== null) {
            $currency = $api->slug === 'paystack' ? 'NGN' : (getSettings()->currency ?? '₦');
            $balanceDisplay = $currency . ' ' . number_format($normalizedBalance, 2);
        }

        $res['balance_value'] = $normalizedBalance;
        $res['balance_display'] = $balanceDisplay ?? ($rawBalance !== null ? (string) $rawBalance : null);
        $res['balance_raw'] = $rawBalance;

        return response()->json($res);
    }

    public function monitorAvailability(Request $request, ApiAvailabilityMonitorService $monitor)
    {
        // $expectedToken = (string) env('API_AVAILABILITY_MONITOR_TOKEN', '');
        // $providedToken = (string) $request->query('token', '');

        // if ($expectedToken !== '' && ! hash_equals($expectedToken, $providedToken)) {
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => 'Unauthorized.',
        //     ], 403);
        // }

        $windowMinutes = max(1, (int) $request->route('windowMinutes', 60));
        $sampleSize = max(1, (int) $request->route('sampleSize', 20));

        try {
            return response()->json($monitor->run($windowMinutes, $sampleSize));
        } catch (Throwable $e) {
            Log::error('API availability monitor failed.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => 'failed',
                'message' => 'Unable to complete availability monitor right now.',
            ], 500);
        }
    }

    private function extractPulledVariations(array $productData): array
    {
        return array_values((array) data_get($productData, 'variations', []));
    }
    
    private function mapPulledProductAttributes(array $productData, API $api, Category $category): array
    {
        return [
            'category_id' => $category->id,
            'status' => $productData['status'] ?? 'inactive',
            'name' => $productData['name'] ?? null,
            'slug' => $productData['slug'] ?? null,
            'seo_title' => $productData['seo_title'] ?? null,
            'seo_description' => $productData['seo_description'] ?? null,
            'seo_keywords' => $productData['seo_keywords'] ?? null,
            'display_name' => $productData['display_name'] ?? ($productData['name'] ?? null),
            'image' => $productData['image'] ?? null,
            'description' => $productData['description'] ?? null,
            'has_variations' => $productData['has_variations'] ?? 'no',
            'api_id' => $api->id,
            'allow_meter_validation' => $productData['allow_meter_validation'] ?? 'no',
            'allow_subscription_type' => $productData['allow_subscription_type'] ?? 'no',
            'fixed_price' => $productData['fixed_price'] ?? null,
            'api_price' => $productData['api_price'] ?? null,
            'system_price' => $productData['system_price'] ?? null,
            'allow_quantity' => $productData['allow_quantity'] ?? null,
            'min' => $productData['min'] ?? null,
            'max' => $productData['max'] ?? null,
            'servercode' => $productData['servercode'] ?? null,
            'ussd_string' => $productData['ussd_string'] ?? null,
            'multistep' => $productData['multistep'] ?? 'no',
            'referral_percentage' => $productData['referral_percentage'] ?? null,
            'show_in_menu' => $productData['show_in_menu'] ?? false,
        ];
    }

    private function mapPulledVariationAttributes(array $variationData, API $api, Category $category, Product $product): array
    {
        return [
            'product_id' => $product->id,
            'category_id' => $category->id,
            'api_id' => $api->id,
            'api_name' => $variationData['api_name'] ?? ($variationData['system_name'] ?? $variationData['slug'] ?? null),
            'api_code' => $variationData['api_code'] ?? $variationData['servercode'] ?? $variationData['slug'] ?? null,
            'status' => strtolower((string) ($variationData['status'] ?? 'inactive')) === 'active' ? 'active' : 'inactive',
            'slug' => $variationData['slug'] ?? null,
            'system_name' => $variationData['system_name'] ?? ($variationData['api_name'] ?? $variationData['slug'] ?? null),
            'fixed_price' => $variationData['fixed_price'] ?? 'Yes',
            'api_price' => $variationData['api_price'] ?? null,
            'system_price' => $variationData['system_price'] ?? null,
            'datasize' => $variationData['datasize'] ?? null,
            'network' => $variationData['network'] ?? $variationData['unique_element'] ?? null,
            'min' => $variationData['min'] ?? null,
            'max' => $variationData['max'] ?? null,
            'ussd_string' => $variationData['ussd_string'] ?? null,
            'multistep' => $variationData['multistep'] ?? 'no',
        ];
    }

    private function findPulledProduct(API $api, array $productData): ?Product
    {
        $name = trim((string) data_get($productData, 'name', ''));

        $query = Product::query()->where('api_id', $api->id);

        if ($name !== '') {
            return $query->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->first();
        }

        return null;
    }

    private function findPulledVariation(API $api, Product $product, array $variationData): ?Variation
    {
        $name = trim((string) data_get($variationData, 'system_name', data_get($variationData, 'api_name', data_get($variationData, 'name', ''))));

        $query = Variation::query()
            ->where('product_id', $product->id)
            ->where('api_id', $api->id);

        if ($name !== '') {
            return $query->where(function ($builder) use ($name) {
                $builder->whereRaw('LOWER(TRIM(system_name)) = ?', [mb_strtolower($name)])
                    ->orWhereRaw('LOWER(TRIM(api_name)) = ?', [mb_strtolower($name)]);
            })->first();
        }

        return null;
    }

    private function shouldRaisePulledPrice($existingSystemPrice, $incomingApiPrice): bool
    {
        $existing = $this->normalizePulledPrice($existingSystemPrice);
        $incoming = $this->normalizePulledPrice($incomingApiPrice);

        if ($incoming === null) {
            return false;
        }

        if ($existing === null) {
            return true;
        }

        return $incoming > $existing;
    }

    private function normalizePulledPrice($value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $clean = preg_replace('/[^\d\.\-]/', '', str_replace(',', '', trim($value)));

        if ($clean === '' || ! is_numeric($clean)) {
            return null;
        }

        return (float) $clean;
    }
}
