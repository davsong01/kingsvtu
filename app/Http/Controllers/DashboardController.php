<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CustomerLevel;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\TransactionPinResetToken;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->type == 'admin') {
            return view('admin.dashboard');
        } else {
            return view('customer.dashboard');
        }
    }

    public function resetTransactionPin()
    {
        return view('customer.reset_pin');
    }

    public function showUpgradeForm()
    {
        $levels = CustomerLevel::orderBy('order', 'ASC')->where('id', '>', auth()->user()->customer->level->id)->get();
        return view('customer.upgrade_level', compact('levels'));
    }

    public function upgradeAccount(Request $request){
        $level = CustomerLevel::where('id', $request->level)->first();
        if(!$level){
            return back()->with('error', 'Level not found');
        }

        $price = $level->upgrade_amount;

        // Check balance
        // Log wallet
        // Log Transaction
        // Log transaction email
    }

    public function processResetTransactionPin(Request $request)
    {
        $settings = $this->settings();

        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->messages()->first());
        }

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Incorrect password !!!');
        }

        $token = Str::random(60);
        $hashedToken = base64_encode(base64_encode($token));
        $expiry = Carbon::now()->addMinute(15);

        TransactionPinResetToken::updateOrCreate(['user_id' => auth()->user()->id, 'status' => 'pending'], [
            'user_id' => auth()->user()->id,
            'token' => $token,
            'expiry' => $expiry,
        ]);

        // Send email
        $subject = "You requested a Transaction PIN Reset Link";
        $body = '<p>Hello! ' . auth()->user()->firstname . '</p>';
        $reset_link =  url('/') . "/confirm_reset_pin?token=" . $hashedToken;
        $body .= '<p style="line-height: 2.0;">Please <strong>login</strong> to your account on ' . config('app.name') . ' and click the button below to reset your transaction pin. <br> <a class="btn btn-info" target="_blank" href="' . $reset_link . '" style="position: margin:5px; relative;margin-bottom:50px;color: #fff;font-weight: 500;padding: 8px 20px;font-size: 13px;line-height: 24px;letter-spacing: 0.01em;border-radius: 4px;border: 1px solid;transition: all .4s ease;background:#950eb3;text-align: center;white-space: nowrap;vertical-align: middle;text-decoration:none">RESET PIN</a><br/><br><strong>Please note that this link expires after ' . $expiry->format('jS F, Y, h:iA') . '.</strong><br><br>If you did not request a Transaction PIN change, Kindly notify us via WhatsApp us via whatsapp( ' . $settings->whatsapp_no . ') immediately.<b><hr/><br>Warm Regards. (' . config('app.name') . ')<br/></p>';

        logEmails(auth()->user()->email, $subject, $body);

        return back()->with('message', 'We have sent you a Transaction Pin reset email, please check your email and follow the instructions to reset Transaction PIN');
    }

    public function resetPin2(Request $request)
    {
        $decode = base64_decode(base64_decode($request->token));
        $row = TransactionPinResetToken::where(['user_id' => auth()->user()->id, 'status' => 'pending'])->first();

        if (!$row) {
            return redirect(route('customer.reset.pin'))->with('warning', 'Transaction PIN reset link invalid. Please try to reset Transaction PIN again!!!');
        }

        if ($row->expiry < Carbon::now()) {
            $row->status = 'completed';
            $row->save();
            return redirect(route('customer.reset.pin'))->with('error', 'PIN reset link has expired, please try to reset PIN again!!!');
        }

        if ($decode !== $row->token) {
            return redirect(route('customer.reset.pin'))->with('error', 'Invalid token link, please check your email and click the Transaction PIN reset link we sent you or resend a new reset link!!!');
        }

        $row->status = 'completed';
        $row->save();

        return view('auth.reset_pin');
    }

    public function finalProcessPin(Request $request)
    {
        $settings = $this->settings();

        $validator = Validator::make($request->all(), [
            'new_transaction_pin' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->messages()->first());
        }

        $new_pin = base64_encode(base64_encode(base64_encode($request->new_transaction_pin)));

        auth()->user()->transaction_pin = $new_pin;
        auth()->user()->save();

        // Send email
        $subject = "New Transaction Pin";
        $body = '<p>Hello! ' . auth()->user()->firstname . '</p>';
        $body .= '<p style="line-height: 2.0;">You have successfully changed your Transaction PIN on ' . config('app.name') . ' on ' . Carbon::now()->format('jS F, Y, h:iA') . '.</strong><br><br>If you did not request a Transaction PIN change, Kindly notify us via WhatsApp us via whatsapp( ' . $settings->whatsapp_no . ') immediately.<b><hr/><br>Warm Regards. (' . config('app.name') . ')<br/></p>';

        logEmails(auth()->user()->email, $subject, $body);

        return redirect(route('dashboard'))->with('message', 'Transaction PIN changed successfully');
    }
}
