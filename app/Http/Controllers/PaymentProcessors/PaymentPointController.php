<?php

namespace App\Http\Controllers\PaymentProcessors;


use App\Models\KycData;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Carbon;
use App\Scopes\MerchantIdScope;
use App\Http\Controllers\Controller;
use App\Models\ReservedAccountNumber;
use App\Services\MerchantPrefixService;
use App\Http\Controllers\PaymentController;

class PaymentPointController extends Controller
{
    private $api;

    public function __construct($provider = null)
    {
        if (!empty($provider)) {
            $this->api = $provider;
        }
    }

    public function verifyTransaction($reference)
    {
        $url = 'https://api.paygateplus.ng/v2/transact/query';
        
        $payload = [
            "request_ref" => $reference,
            "request_type" => "disburse",
            "auth" => [
                "secure" => null,
                "auth_provider" => "Fidelity"
            ],
            "transaction" => [
                "transaction_ref" => $reference
            ]
        ];

        if (env('ENT') == 'local') {
            $response = '{
                "status": "Successful",
                "message": "Transaction processed successfully",
                "data": {
                    "provider_response_code": "00",
                    "provider": "Fidelity",
                    "errors": null,
                    "error": null,
                    "provider_response": {
                    "destination_institution_code": "076",
                    "beneficiary_account_name": "JAMES BLUE",
                    "beneficiary_account_number": "6698059290",
                    "beneficiary_kyc_level": "",
                    "originator_account_name": "",
                    "originator_account_number": "1100009909",
                    "originator_kyc_level": "",
                    "narration": "A random transaction",
                    "transaction_final_amount": 1000,
                    "reference": "C3DA541CA20740659031949CD3441EBE",
                    "payment_id": "382FTTP2005901LD"
                    },
                    "client_info": {
                    "name": null,
                    "id": null,
                    "bank_cbn_code": null,
                    "bank_name": null,
                    "console_url": null,
                    "js_background_image": null,
                    "css_url": null,
                    "logo_url": null,
                    "footer_text": null,
                    "show_options_icon": false,
                    "paginate": false,
                    "paginate_count": 0,
                    "options": null,
                    "merchant": null,
                    "colors": null,
                    "meta": null
                    }
                }
                }';
            $response = json_decode($response, true);
        } else {
            $response = $this->makeCall($url, $payload, 'GET');
        }
        
        if (isset($response['status']) && $response['status'] == 'Successful') {
            $real = [
                'status' => 'success',
                'data' => $response['data'],
            ];
        } else {
            $real = [
                'status' => 'failed',
                'data' => $response['message'] ?? 'Something went wrong',
            ];
        }
        
        return $real;
    }

    public function verifySignature($request){
        $secretKey = $this->api->secret_key;
        $inputData = file_get_contents('php://input');
        $signatureHeader = $_SERVER['HTTP_PAYMENTPOINT_SIGNATURE'];

        $calculatedSignature = hash_hmac('sha256', $inputData, $secretKey);
        \Log::info($calculatedSignature);
        \Log::info($inputData);
        \Log::info($signatureHeader);
        \Log::info($secretKey);
        \Log::info($secretKey);
        \Log::info($request);
        
        if (!hash_equals($calculatedSignature, $signatureHeader)) {
            return false;
        }

        return true;
    }
    public function createReservedAccount(array $data, $admin_id = null)
    {
        $url = $this->api->base_url . 'createVirtualAccount';
        // Get customer and kyc data
        $customer = Customer::with('multiplekycdata')->where('id', $data['customer_id'])->first();
        
        $kycData = $customer->multiplekycdata->toArray();
        $kycData = extractKeyValuesFromMultiDimensionalArray('key', 'value', $kycData);
        
        $payload = [
            "email" => $data["customerEmail"] ?? $customer->user->email,
            "name" => $data["customerFirstName"] ?? ($kycData['FIRST_NAME'] ?? $customer->user->firstname),
            "bankCode" =>  $data['preferredBanks'],
            "businessId" =>  $this->api->contract_id,
            "phoneNumber" => $data["customerPhone"] ?? ($kycData['PHONE_NUMBER'] ?? $customer->user->phone),
        ];

        $response = $this->makeCall($url, $payload);
        
        // if(env('ENT') == 'local'){
        //     $response = '{
        //         "status": "success",
        //         "message": "Customer account created successfully. Bank account(s) processed and ready for use.",
        //         "customer": {
        //             "customer_id": "fa6aa77cbc60ee04c67f5b7d56394733ed67b924",
        //             "customer_name": "a",
        //             "customer_email": "ad.com",
        //             "customer_phone_number": "07"
        //         },
        //         "business": {
        //             "business_name": "Av",
        //             "business_email": "a",
        //             "business_phone_number": "07018",
        //             "business_Id": null
        //         },
        //         "bankAccounts": [
        //             {
        //                 "bankCode": "20946",
        //                 "accountNumber": "6698059290",
        //                 "accountName": "A(paymentpoint)",
        //                 "bankName": "Palmpay",
        //                 "Reserved_Account_Id": "3a28cfe332ccf8596bd374bc4616ad2609454584"
        //             }
        //         ],
        //         "errors": []
        //     }';

        //     $response = json_decode($response, true);
        // }

        if (isset($response) && $response['status'] == 'success') {
            if (!empty($response['bankAccounts'])) {
                $accounts = $response['bankAccounts'];

                foreach($accounts as $account) {
                    ReservedAccountNumber::updateOrCreate([
                        'customer_id' => $data["customer_id"] ?? null,
                        'account_reference' => $account['Reserved_Account_Id'],
                        'account_number' => $account['accountNumber'] ?? null,
                    ], [
                        'customer_id' => $data["customer_id"] ?? null,
                        'admin_id' => $admin_id ?? null,
                        'account_reference' => $account['Reserved_Account_Id'],
                        'account_number' => $account['accountNumber'] ?? null,
                        'account_name' => $account['accountName'],
                        'bank_name' => $account['bankName'],
                        'bank_code' => $account['bankCode'],

                        'paymentgateway_id' => $this->api->id,
                        'status' => 'active',
                        'purpose' => 'WALLET-FUNDING',
                        'bvn' =>  $kycData['BVN'],
                        'response' => json_encode($response),
                    ]);
                }

                $res = [
                    'status' => 'success',
                    'data' => $response['data'] ?? '',
                ];
            } else {
                $res = [
                    'status' => 'failed',
                    'data' => $response['message'] ?? 'No Accounts set by Provider',
                ];
            }
        } else {
            $res = [
                'status' => 'failed',
                'data' => $response['message'] ?? 'no-response',
            ];
        }
        
        return $res;
    }


    public function makeCall($url, $payload, $method = 'POST')
    {
        $curl = curl_init();
        
        if ($method == 'POST') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',

                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key,
                    "api-key: " . $this->api->api_key
                ],
            ));
        }
        
        if ($method == 'GET') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key,
                    "api-key: " . $this->api->api_key
                ],
            ));
        }

        if ($method == 'DELETE') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key,
                    "api-key: " . $this->api->api_key
                ],
            ));

        }

        $response = curl_exec($curl);
        
        \Log::info($url);
        \Log::info(json_encode($payload));
        \Log::info($response);

        curl_close($curl);
        
        return json_decode($response, true);
    }


    public function dummyWebhookResponse(){
        return '{
            "notification_status": "payment_successful",
            "transaction_id": "xxx",
            "amount_paid": 100,
            "settlement_amount": 99.5,
            "settlement_fee": 0.5,
            "transaction_status": "success",
            "sender": {
                "name": "AGH ONLINE ACADEMY TUTORS LIMITED",
                "account_number": "****4290",
                "bank": "HYDROGEN"
            },
            "receiver": {
                "name": "ALBARKADATASUB-Abd(Paymentpoint)",
                "account_number": "6679854996",
                "bank": "PalmPay"
            },
            "customer": {
                "name": "Abdulismail",
                "email": "albarkadatasub@gmail.com",
                "phone": null,
                "customer_id": "xxx"
            },
            "description": "Your payment has been successfully processed.",
            "timestamp": "2024-11-22T13:00:04.256092Z"
            }';            
    }


}
