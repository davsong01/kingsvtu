<?php

use App\Mail\EmailMessages;
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
            dd($e->getMessage());
        }
    }
}