<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\KycData;
use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // return back()->with('error', 'Registration is currently not available, please try again');
        $captchaSettings = getSettings()->captcha_settings;
        if (isset($captchaSettings['captcha_settings_status']) && $captchaSettings['captcha_settings_status'] == 'yes') {
            if (in_array($captchaSettings['captcha_settings_provider'], ['all', 'simple'])) {
                $request->validate([
                    '_answer' => ['required', 'simple_captcha'],
                ]);
            }

            if (in_array($captchaSettings['captcha_settings_provider'], ['all', 'google'])) {
                Config::set('captcha.secret', $captchaSettings['google']['RECAPTCHA_SECRET_KEY']);
                $request->validate([
                    'g-recaptcha-response' => ['captcha'],
                ]);
            }
        }
        
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'privacy' => ['required'],
        ]);

        $user = User::create([
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'phone' => $request->phone,
            'username' => $request->username,
            'referral' => $request->referral ?? '',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        $user->update([
            'api_key' =>  strrev(md5($user->username)),
        ]);
        event(new Registered($user));

        $customer = Customer::create([
            'user_id' => $user->id,
            'wallet' => 0,
            'referal_wallet' => 0,
            'customer_level' => env('DEFAULT_CUSTOMER_LEVEL_ID') ?? 1,
        ]);
        
        KycData::create([
            'key' => 'PHONE_NUMBER',
            'value' => $user->firstname,
            'status' => 'unverified',
            'customer_id' => $customer->id
        ]);

        Auth::login($user);

        try {
            //code...
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
