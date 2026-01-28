<?php
namespace App\Services;

use App\Models\API;
use App\Models\ProviderWebhook;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

define('LIMIT', 50000);

class WebhookService {

    public function analyzeWebhookResponse($pick){
        $webhooks = ProviderWebhook::with('provider')->where('status', 'pending')->tabke($pick)->get();
        if($webhooks){
            foreach($webhooks as $webhook){
                if(TransactionLog::where('reference_id')->whereIn('status',['attention-required','pending'])->first()){
                    $file_name = $webhook->provider->file_name;
                    $analyze = app("App\Http\Controllers\Providers\\" . $file_name)->analyzeWebhookResponse($webhook);
    
                    if($analyze['status']){
                        $webhook->update([
                            'status' => 'resolved'
                        ]);
                    }
                }else{
                    $webhook->update([
                        'status' => 'analyzed'
                    ]);
                    continue;
                }
            }
        }
    }

    public function logWebhookResponse($request, $provider_id)
    {
        try {
            $provider = API::find($provider_id);

            if (!$provider) {
                return false;
            }

            $class = "App\\Http\\Controllers\\Providers\\" . $provider->file_name;

            if (!class_exists($class)) {
                return false;
            }

            $providerService = app($class);

            if (!method_exists($providerService, 'verifyWebhookSignature')) {
                return false;
            }

            $verifySignature = $providerService->verifyWebhookSignature($request);

            if (
                empty($verifySignature['status']) ||
                empty($verifySignature['reference'])
            ) {
                return false;
            }

            // Prevent duplicate webhook
            if (ProviderWebhook::where('reference', $verifySignature['reference'])->exists()) {
                return true;
            }

            DB::beginTransaction();

            try {
                ProviderWebhook::create([
                    'api_id'          => $provider_id,
                    'reference'       => $verifySignature['reference'],
                    'status'          => 'pending',
                    'request_payload'=> json_encode($request->all()),
                    'type'            => $request->input('type', 'transaction'),
                ]);

                DB::commit();
                return true;

            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error('Webhook DB insert failed', [
                    'provider_id' => $provider_id,
                    'error'       => $e->getMessage(),
                    'payload'     => $request->all(),
                ]);

                return false;
            }

        } catch (\Throwable $e) {
            Log::error('Webhook processing failed', [
                'provider_id' => $provider_id,
                'error'       => $e->getMessage(),
                'payload'     => $request->all(),
            ]);

            return false;
        }
    }

}
