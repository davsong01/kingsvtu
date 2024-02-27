<?php

namespace App\Http\Controllers;

use Image;
use App\Models\GeneralSetting;
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
        } elseif (!empty($timeout)) {
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

    public function getIpAddress(){

    }

    public function getDomainName(){

    }

    public function getAppVersion(){
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

    public function sendTransactionEmail($transaction)
    {
        if (getSettings()->transaction_email_notification == 'yes') {
            $variation_name =  isset($transaction->variation) ? ' | ' . $transaction->variation->system_name : '';
            $product =  $transaction->product->name ?? '' .  $variation_name;
            $extras = isset($transaction->extras) ? $transaction->extras : '';
            $subject = "Transaction Alert";
            $body = '<p>Hello! ' . auth()->user()->firstname . '</p>';
            $body .= '<p style="line-height: 2.0;">A transaction has just occured on your account on ' . config('app.name') . ' Please find below the details of the transaction: <br>
            <strong>Transaction Purpose:</strong> ' . $transaction->reason . '<br>
            <strong>Transaction Id:</strong> ' . $transaction->transaction_id . '<br>
            <strong>Transaction Date:</strong> ' . date("M jS, Y g:iA", strtotime($transaction->created_at)) . '<br>
            <strong>Transaction Status:</strong> ' . ucfirst($transaction->descr);

            if (!empty($transaction->extras)) {
                $body .= '<br> <strong>Extras:</strong> ' . $extras . '<br>';
            }

            if (!empty($transaction->unique_element)) {
                $body .= '<strong>Biller:</strong> ' . $transaction->unique_element . '<br>';
            }

            if (!empty($product)) {
                $body .= '<strong>Product:</strong> ' . $product . '<br>';
            }

            if (!empty($transaction->extra_info)) {
                foreach (json_decode($transaction->extra_info) as $key => $info) {
                    $body .= '<strong>' . $key . '</strong> ' . $product . '<br>';
                }
            }

            $body .= '<strong>Unit Price:</strong> ' . getSettings()->currency . $transaction->unit_price . '<br>
            <strong>Quantity:</strong> ' . $transaction->quantity . '<br>
            <strong>Discount Applied:</strong> ' . getSettings()->currency . $transaction->discount . '<br>
            <strong>Total Amount Paid:</strong> ' . getSettings()->currency . $transaction->total_amount . '<br>
            <strong>Initial Balance:</strong> ' . getSettings()->currency . $transaction->balance_before . '<br>
            <strong>Final Balance: </strong>' . getSettings()->currency . $transaction->balance_after . '<br>
            <br>Warm Regards. (' . config('app.name') . ')<br/>
            </p>';

            logEmails(auth()->user()->email, $subject, $body);
        }
    }


}
