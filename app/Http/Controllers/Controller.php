<?php

namespace App\Http\Controllers;

use Image;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function basicApiCall($url, $payload, $headers, $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($method == "GET") {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } elseif ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } elseif ($method == "PUT") {
            curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } elseif ($method == "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }elseif (!empty($timeout)) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        return json_decode($response, true);
    }

    public function uploadFile($file, $location = null, $width = null, $height = null)
    {
        $folder = $this->getPathToSaveFile($location);
        $name = uniqid(11) . '.' . $file->getClientOriginalExtension();

        if (substr($file->getMimeType(), 0, 5) == 'image') {
            $imageFile = Image::make($file);
            $imgWidth = $width ?? $imageFile->width();
            $imgHeight = $imgHeight ?? $imageFile->height();

            if (!is_null($imgWidth) && !is_null($imgHeight)) {
                if ($imgWidth < $imgHeight) {
                    $imageFile->resize($imgWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $imageFile->resize(null, $imgWidth, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }
        }

        $imageFile->save($folder . '/' . $name);

        return $folder . '/' . $name;
    }

    public function getPathToSaveFile($location = null)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        if ($location) {
            $location = $location . '/' . $year . '/' . $month . '/' . $day;
        } else {
            $location = $year . '/' . $month . '/' . $day;
        }

        if (!is_dir(public_path() . '/' . $location)) {
            mkdir(public_path() . '/' . $location, 0777, true);
        }

        return $location;
    }

    public function getIpAddress()
    {
        return Session::get('ip_address') ?? null;
    }

    public function getDomainName()
    {
        return Session::get('domain_name') ?? null;
    }

    public function getAppVersion()
    {
        return 1;
    }

    // public function settings(){
    //     return GeneralSetting::first();
    // }
    public function generateRequestId()
    {
        date_default_timezone_set("Africa/Lagos");
        $trx = date("YmdHi") . rand(1000000, 9999999);
        return $trx;
    }

    public function sendTransactionEmail($transaction, $user)
    {
        if (getSettings()->transaction_email_notification == 'yes') {
            $variation_name =  isset($transaction->variation) ? ' | ' . $transaction->variation->system_name : '';
            $product =  $transaction->product->name ?? '' .  $variation_name;
            $extras = isset($transaction->extras) ? $transaction->extras : '';
            $name = $user->firstname ?? 'Customer';
            $subject = "Transaction Alert";
            $body = '<p>Hello! ' . $name . '</p>';
            $body .= '<p style="line-height: 2.0;">A transaction has just occured on your account on ' . config('app.name') . ' Please find below the details of the transaction: <br>
            <strong>Transaction Purpose:</strong> ' . $transaction->reason . '<br>
            <strong>Transaction Id:</strong> ' . $transaction->transaction_id . '<br>
            <strong>Transaction Date:</strong> ' . date("M jS, Y g:iA", strtotime($transaction->created_at)) . '<br>
            <strong>Transaction Status:</strong> ' . ucfirst($transaction->descr);

            if (!empty($transaction->extras)) {
                $body .= '<br> <strong>Extras:</strong> ' . $extras . '<br>';
            }

            if (!empty($transaction->unique_element)) {
                $body .= '<br><strong>Biller:</strong> ' . $transaction->unique_element . '<br>';
            }

            if (!empty($product)) {
                $body .= '<strong>Product:</strong> ' . $product . '<br>';
            }

            if (!empty($transaction->extra_info)) {
                foreach (json_decode($transaction->extra_info) as $key => $info) {
                    $body .= '<strong>' . $key . '</strong> ' . $product . '<br>';
                }
            }

            $body .= '<strong>Unit Price:</strong> ' . getSettings()->currency . $transaction->unit_price . '<br>';

            if (!empty($transaction->provider_charge)) {
                $body .= '<strong>Convenience Fee:</strong> ' . getSettings()->currency.number_format($transaction->provider_charge,2) . '<br>';
            }

            $body .= '<strong>Quantity:</strong> ' . $transaction->quantity . '<br>
            <strong>Discount Applied:</strong> ' . getSettings()->currency . number_format($transaction->discount, 2) . '<br>
            <strong>Total Amount Paid:</strong> ' . getSettings()->currency . number_format($transaction->total_amount, 2) . '<br>
            <strong>Initial Balance:</strong> ' . getSettings()->currency . number_format($transaction->balance_before, 2) . '<br>
            <strong>Final Balance: </strong>' . getSettings()->currency . number_format($transaction->balance_after,2) . '<br>
            <br>Warm Regards. (' . config('app.name') . ')<br/>
            </p>';

            $email = $user->email ?? 'noreply@kingsvtu.com'; 
            logEmails($email, $subject, $body);
        }
    }
}
