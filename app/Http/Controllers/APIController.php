<?php

namespace App\Http\Controllers;

use App\Models\API;
use App\Services\ApiAvailabilityMonitorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class APIController extends Controller
{
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
        return view(themeView('admin', 'api.form'), ['api' => null]);
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
        return view(themeView('admin', 'api.form'), compact('api'));
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
}
