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

class SquadController extends Controller
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
        $url = $this->api->base_url . 'virtual-account/merchant/transactions/all?transactionReference=' . $reference;

        if (env('ENT') == 'local') {
            $response = [
                "status" => 200,
                "success" => true,
                "message" => "Success",
                "data" => [
                    "count" => 15,
                    "rows" => [
                        [
                            "transaction_reference" => $reference,
                            "virtual_account_number" => "6725149329",
                            "principal_amount" => "50.00",
                            "settled_amount" => "50.00",
                            "fee_charged" => "0.00",
                            "transaction_date" => "2022-10-07T00:00:00.000Z",
                            "transaction_indicator" => "C",
                            "remarks" => "Transfer FROM Sample | [CCC1234334] TO Sample Name",
                            "currency" => "NGN",
                            "alerted_merchant" => false,
                            "merchant_settlement_date" => "2022-10-07T12:04:11.635Z",
                            "frozen_transaction" => null,
                            "customer" => [
                                "customer_identifier" => "5UMKKK3R"
                            ]
                        ],
                        [
                            "transaction_reference" => $reference,
                            "virtual_account_number" => "6725149329",
                            "principal_amount" => "50.00",
                            "settled_amount" => "50.00",
                            "fee_charged" => "0.00",
                            "transaction_date" => "2022-10-07T00:00:00.000Z",
                            "transaction_indicator" => "C",
                            "remarks" => "Transfer FROM Sample | [CCC1234334] TO Sample Name",
                            "currency" => "NGN",
                            "alerted_merchant" => false,
                            "merchant_settlement_date" => "2022-10-07T12:04:11.635Z",
                            "frozen_transaction" => null,
                            "customer" => [
                                "customer_identifier" => "5UMKKK3R"
                            ]
                        ],
                    ],
                    "query" => []
                ]
            ];
        } else {
            $response = $this->makeCall($url, [], 'GET');
        }

        if (isset($response['success']) && isset($response['message']) && $response['success'] == true && $response['message'] == 'Success') {
            $real = [
                'status' => 'success',
                'data' => $response['data']['rows'][0],
            ];
        } else {
            $real = [
                'status' => 'failed',
                'data' => $response['message'] ?? 'Something went wrong',
            ];
        }

        return $real;
    }

    public function createReservedAccount(array $data, $admin_id = null)
    {
        $url = $this->api->base_url . 'virtual-account';
        // Get customer and kyc data
        $customer = Customer::with('multiplekycdata')->where('id', $data['customer_id'])->first();
        
        $kycData = $customer->multiplekycdata->toArray();
        $kycData = extractKeyValuesFromMultiDimensionalArray('key', 'value', $kycData);
        $gender = "1";
        if (isset($kycData['GENDER'])) {
            if ($kycData['GENDER'] == 'male') {
                $gender = "1";
            } else {
                $gender = "2";
            }
        }
    
        $payload = [
            "customer_identifier" =>  'KGSVTU-'.$customer->id,
            "first_name" => $data["customerFirstName"] ?? ($kycData['FIRST_NAME'] ?? $customer->user->firstname),
            "last_name" => $data["customerLastName"] ?? ($kycData['LAST_NAME'] ?? $customer->user->lastname),
            "mobile_num" => $data["customerPhone"] ?? ($kycData['PHONE_NUMBER'] ?? $customer->user->phone),
            "email" => $data["customerEmail"] ?? $customer->user->email,
            "bvn" => $kycData['BVN'] ?? ($customer->$kycData['BVN'] ?? ''), 
            "dob" => $kycData['DOB'] ?? null,
            "address" => $kycData['DATE_OF_BIRTH'] ?? 'Lagos',
            "gender" => $gender,
            "beneficiary_account" => "0477196810"
        ];

        if (!empty($payload['dob'])) {
            $dob = trim($payload['dob']);
            $date = null;

            foreach (['Y-m-d', 'd-m-Y', 'd/m/Y'] as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $dob);
                    break;
                } catch (\Exception $e) {
                    continue;
                }
            }

            $payload['dob'] = $date ? $date->format('m/d/Y') : null;

        }

        $response = $this->makeCall($url, $payload);
        
        if (
            isset($response) && $response['success'] == true &&
            $response['message'] == 'Success'
        ) {
            if (isset($response['data'])) {
                $dataX = $response['data'];

                ReservedAccountNumber::updateOrCreate([
                    'customer_id' => $data["customer_id"] ?? null,
                    'account_reference' => $dataX['customer_identifier'],
                    'account_number' => $dataX['virtual_account_number'] ?? null,
                ], [
                    'customer_id' => $data["customer_id"] ?? null,
                    'admin_id' => $admin_id ?? null,
                    'account_reference' => $dataX['customer_identifier'],
                    'account_number' => $dataX['virtual_account_number'] ?? null,
                    'account_name' => $dataX['first_name'],
                    'bank_name' => 'GT Bank',
                    'bank_code' => $dataX['bank_code'],

                    'paymentgateway_id' => $this->api->id,
                    'status' => 'active',
                    'purpose' => 'WALLET-FUNDING',
                    'bvn' =>  $kycData['BVN'],
                    'response' => json_encode($response),
                ]);

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

    public function createBusinessReservedAccount(array $data, $admin_id = null)
    {
        $url = $this->api->base_url . 'virtual-account/business';
        // Get customer and kyc data
        $customer = Customer::with('multiplekycdata')->where('id', $data['customer_id'])->first();

        $kycData = $customer->multiplekycdata->toArray();
        $kycData = extractKeyValuesFromMultiDimensionalArray('key', 'value', $kycData);
        $gender = "1";
        if (isset($kycData['GENDER'])) {
            if ($kycData['GENDER'] == 'male') {
                $gender = "1";
            } else {
                $gender = "2";
            }
        }

        $payload = [
            "customer_identifier" =>  'KGSVTUB-' . $customer->id,
            "business_name" => 'BUYVTU WORLD',
            "mobile_num" => $data["customerPhone"] ?? ($kycData['PHONE_NUMBER'] ?? $customer->user->phone),
            "email" => $data["customerEmail"] ?? $customer->user->email,
            "bvn" => $kycData['BVN'] ?? ($customer->kycdata->BVN ?? ''),
            "beneficiary_account" => "0477196810"
        ];

        $response = $this->makeCall($url, $payload);

        if (
            isset($response) && $response['success'] == true &&
            $response['message'] == 'Success'
        ) {
            if (isset($response['data'])) {
                $dataX = $response['data'];

                ReservedAccountNumber::updateOrCreate([
                    'customer_id' => $data["customer_id"] ?? null,
                    'account_reference' => $dataX['customer_identifier'],
                    'account_number' => $dataX['virtual_account_number'] ?? null,
                ], [
                    'customer_id' => $data["customer_id"] ?? null,
                    'admin_id' => $admin_id ?? null,
                    'account_reference' => $dataX['customer_identifier'],
                    'account_number' => $dataX['virtual_account_number'] ?? null,
                    'account_name' => $dataX['first_name'],
                    'bank_name' => 'GT Bank',
                    'bank_code' => $dataX['bank_code'],

                    'paymentgateway_id' => $this->api->id,
                    'status' => 'active',
                    'purpose' => 'WALLET-FUNDING',
                    'bvn' =>  $kycData['BVN'],
                    'response' => json_encode($response),
                ]);

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

    public function getCallbackLogs(Request $request){
        // get it
        $provider = PaymentGateway::where('id', 2)->first();
        
        $this->api = $provider;
        $url = $this->api->base_url . 'virtual-account/webhook/logs';
        $response = $this->makeCall($url, [], 'GET');
        
        if(env('ENT') == 'local'){
            $response = $this->dummyWebhookError();
        }
        
        if (isset($response['status']) && isset($response['message']) && $response['success'] == true && $response['message'] == 'Success') {
            if(isset($response['data']['count']) && $response['data']['count'] > 0){
    
                foreach($response['data']['rows'] as $row){
                    if(isset($row['payload'])){
                        $data = $row['payload'];
                        $reqs = new Request($data);
                        $new = new PaymentController($this->api);
                        $res = $new->dumpCallback($reqs, $this->api->id, 'yes');

                        if(isset($res['message'])){
                            $url = $this->api->base_url . 'virtual-account/webhook/logs/'. $data['transaction_reference'];
                            $response2 = $this->makeCall($url, [], 'DELETE');
                        }
                    }
                }
            }   
        }

        return back()->with('message', 'Log pulled successfully');
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
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key . ""
                ),
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
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key . ""
                ),
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
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key . ""
                ),
            ));

        }

        $response = curl_exec($curl);
        \Log::info($url);
        \Log::info(json_encode($payload));
        \Log::info($response);

        curl_close($curl);
        
        return json_decode($response, true);
    }


    // Tests
    /**
     * Create Virtual Account (Individual)POST: https://sandbox-api-d.squadco.com/virtual-account
     * Create a Virtual Account (Business) POST: https://sandbox-api-d.squadco.com/virtual-account/business
     * POST: https://sandbox-api-d.squadco.com/virtual-account/simulate/payment Objective: Verify that you can simulate payments to the initiated accounts.
     * Query Customers transaction GET: https://sandbox-api-d.squadco.com/virtual- account/customer/transactions/{{customer_identifier}}
     * Query Merchants transaction GET: https://sandbox-api-d.squadco.com/virtual-account/merchant/transactions
     * Retrieve VA details GET: https://sandbox-api-d.squadco.com/virtual- account/customer/{{virtual_account_number}}
     * Retrieve VA details using the customer identifier
     * GET: https://sandbox-api-d.squadco.com/virtual-account/{{customer_identifier}}
     */
    public function dummyWebhookError(){
        return [
                "status" => 200,
                "success" => true,
                "message" => "Success",
                "data" => [
                    "count" => 2,
                    "rows" => [
                        [
                            "id" => "229f9f3d-53e4-450e-a9e9-164a8b882a60",
                            "payload" => [
                                "hash" => "659c24ba0b6c3ac324b587f2f079c8ee876c56609ff11b7106cd868f84674a5c37fcb088373859f8d900713f03c47d819de79623cde67e70bbca945fd20f3cb3",
                                "meta" => [
                                    "freeze_transaction_ref" => null,
                                    "reason_for_frozen_transaction" => null
                                ],
                                "channel" => "virtual-account",
                                "remarks" => "Transfer FROM OKOYE, CHIZOBA ANTHONY | [CCtyttytC] TO CHIZOBA ANTHONY OKOYE",
                                "currency" => "NGN",
                                "fee_charged" => "0.05",
                                "sender_name" => "OKOYE, CHIZOBA ANTHONY",
                                "encrypted_body" => "DiPEa8Z4Cbfiqulhs3Q8lVJXGjMIFzbWwI2g7utVGbhXihbtK3H2xsA/+ZnjOpFA0AU8vAN5LUTEH6elfrK58ub2wydaRk0ngvQXWUFz3iB19qWBcdGQRnppKAT/AB5xyy1iQZvEHP7zq3Y7na5zcx9ttkU1mZIeAIoisM9k+ghVLxkTeql4UvfFcLyDdGzMd/BC4YgJFyrZxifhfhKi073od7xJnz4Hhz08UBE/FAwNYMWkwWD9izlbcaaJtfh1VIN6t9rl1gotlb5qmNq/UytgoSvuN5uaEXxegdB3VWvmsDMHqoYwDs4oEuv0lp8zUUG3cZ9zPQ6xH3shGQjVOWErkuIfCk62fRzkwxya4Gu/x2KHMSQjutbvD4vNDjVGfuCIoHuZEXPThWrq1jpTy7cNMLc8ZZ8IowJnfwWHL+O6fuepxXxfrJHlswMCI35ZHSvef1AEXgbUlx2O7yzytceCogpUkY+QJ1yLddl1FeE1u2JKOM+casP3pfiT+t3Mv55aSCVQO7hUy46gd6H/bIHaSIp2K3CcjfdflZ/bxCZaZoe/sRqfVdVIzpSpTc0Lq5sOXM2gijOdeg+zex/CgnMIKGJdzUT9YUJtaaVrMmhk0EcM0rHRrqs0iM7xaSTdZ7K8hnzl0RPJhDXIhu5a/Y2NxS3ZTC2lYRVZd6I3lerpoMQG69VfmqvaVgW2k03f",
                                "settled_amount" => "49.95",
                                "principal_amount" => "50.00",
                                "transaction_date" => "2023-09-01T00:00:00.000Z",
                                "customer_identifier" => "CCtyttytC",
                                "transaction_indicator" => "C",
                                "transaction_reference" => "REF20230901162737156459_1",
                                "virtual_account_number" => "0760640237"
                            ],
                            "transaction_ref" => "REF20230901162737156459_1"
                        ]
                    ]
                ],
            ];            
    }
}
