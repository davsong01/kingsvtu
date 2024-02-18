<?php

use App\Http\Controllers\WalletController;
use App\Mail\EmailMessages;
use App\Models\Category;
use Illuminate\Support\Facades\Mail;

if (!function_exists("logEmails")) {
    function logEmails($email_to, $subject, $body){
        $data = [
            'subject' => $subject,
            'body' => $body,
        ];

        try {
            Mail::to($email_to,)->send(new EmailMessages($data));
        } catch (\Exception $e) {
           
        }
    }
}

if (!function_exists("sendEmails")) {
    function sendEmails($email_to, $subject, $body)
    {
        $data = [
            'subject' => $subject,
            'body' => $body,
        ];

        try {
            Mail::to($email_to,)->send(new EmailMessages($data));
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}


if (!function_exists("getUniqueElements")) {
    function getUniqueElements()
    {
        return [
            'phone',
            'meter_number',
            'iuc_number',
            'account_id'
        ];
    }
}

if (!function_exists("verifiableUniqueElements")) {
    function verifiableUniqueElements()
    {
        return ['meter_number', 'iuc_number'];
    }
}

if (!function_exists("getCategories")) {
    function getCategories()
    {
       return Category::where('status', 'active')->get();
    }
}

if (!function_exists("walletBalance")) {
    function walletBalance($user)
    {
        $wallet = new WalletController();
        return $wallet->getWalletBalance($user);
    }
}