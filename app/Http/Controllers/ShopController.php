<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Support\Str;
use App\Models\ShopRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ShopController extends Controller
{
    public function create(){
        $currencies = [
            '₦',
            // '$'
        ];

        return view('customer.create_shop',compact('currencies'));
    }

    public function store(Request $request){

        $data = $this->validate($request, [
            "first_name" => "required", // Admin name
            "last_name" =>  "required", // Admin las name
            "password" =>  "nullable",
            "phone" =>  "required",
            "email" =>  "required",
            "shop_name" => "required|string", // Company name
            "shop_slug" => "required|string",
            'currency' => 'required',
            'official_email' => 'required',
            'whatsapp_number' => 'required'
        ]);

        if(empty(auth()->user()->secret_key || empty(auth()->user()->public_key))){
            return back()->with('error', 'Plese ensure that you have set up API keys');
        }
        $setting = Settings::first();
        $data['shop_slug'] = Str::slug($request->shop_slug);

        $body = '<p>Hello! Admin</p>';
        $body .= '<p style="line-height: 2.0;">Please login to approve a shop creation request </strong><br><br>Warm Regards. (' . config('app.name') . ')<br/></p>';

        sendEmails($setting->official_email, 'New Shop Creation request', $body);
        ShopRequests::updateOrCreate([
            'customer_id' => auth()->user()->customer->id,
            'request_details' => $data,
        ]);
        
        return back()->with('message', 'Shop Creation request sent succesfully, an administrator is reviewing your request');
    }

    public function shopRequests()
    {
        $requests = ShopRequests::orderBy('updated_at','DESC')->get();
        return view('admin.customers.shop_creation_request', compact('requests'));
    }

    public function approveRequests(Request $request, ShopRequests $shoprequest)
    {
        $customer = $shoprequest->customer;
        $details = $shoprequest->request_details;
        $password = !empty($shoprequest->password) ? Hash::make($shoprequest->password) : $customer->user->password;
        $details['password'] = $password ;
        $details['store_name'] = $details['shop_name'];
        $details['store_slug'] = $details['shop_slug'];
        $details['merchant_name'] = $customer->user->firstname . ' '. $customer->user->lastname;

        $details['api_key'] = $customer->user->api_key;
        // $details['secret_key'] = $customer->user->secret_key;
        // $details['public_key'] = $customer->user->public_key;

        try {
            DB::beginTransaction();
            $url = env('MULTI_SHOP_BASE_URL'). 'create-new-shop';
            $details['json'] = Hash::make(env('MULTI_SHOP_KEY'));
            // Send details
            $response = $this->basicApiCall($url, $details, []);
            
            // Update status 
            if(!empty($response['status']) && $response['status'] == 'success'){
                $shoprequest->update([
                    'merchant_id' => $response['merchant']['id'],
                    'status' => 'approved',
                    'admin_id' => auth()->user()->admin->id,
                ]);
            }else{
                $message = !empty($response['message']) ? implode(',', $response['message']) : 'Could not approve. Something went wrong!';
                return back()->with('error', $message);
            }
            
            // Send email
            $subject = 'Shop Request Approved!';
            $body = 'Hello ' . $customer->user->firstname . '<br>Your request to create a new shop affiliated to '.config('app.name'). ' has been approved, please find your shop details below: <br>
            Shop Name: ' . $details['shop_name'] . '<br>
            Shop Slug: '. $details[ 'shop_slug'] . '<br>
            Shop URL: ' . env('SHOPS_BASE_URL') . $details['shop_slug'] . '<br>
            Admin Name: ' . $details['first_name'] .' '. $details['last_name'] . '<br>
            Admin Email: ' . $details['email'] . '<br>
            Admin Password: Your chosen password<br><br>Warm Regards<br>
            ' .config('app.name');

            sendEmails($customer->user->email, $subject, $body);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            \Log::info(['Shop creation error' => $th->getMessage()]);

            return back()->with('error', $th->getMessage());
        }

        return back()->with('message', 'Shop Approved and created successfully');
    }

    public function declineRequests(Request $request, ShopRequests $shoprequest)
    {
        $shoprequest->update([
            'status' => 'declined',
            'admin_id' => auth()->user()->admin->id
        ]);
        $customer = $shoprequest->customer;
        
        // Send email
        $subject = 'Shop Request Approved!';
        $body = 'Hello ' . $customer->user->firstname . '<br>Your request to create a new shop affiliated to ' . config('app.name') . ' has been declined.<br><br>Warm Regards<br>
            ' . config('app.name');

        sendEmails($shoprequest->customer->user->email, $subject, $body);
        return back()->with('message', 'Operation successful');
    }

    public function deleteRequests(Request $request, ShopRequests $shoprequest)
    {
        if($shoprequest->status == 'approved'){
            return back()->with('error', 'Approved shot cannot be deleted');
        }

        $url = env('MULTI_SHOP_BASE_URL') . 'delete-shop/'.$shoprequest->merchant_id;
        $details['json'] = Hash::make(env('MULTI_SHOP_KEY'));
        
        // Send details
        $response = $this->basicApiCall($url, $details, []);
        
        // Update status 
        if (!empty($response['status']) && $response['status'] == 'success') {
            $shoprequest->delete();
        } else {
            return back()->with('error', $response['message'] ?? 'Could not delete. Something went wrong!');
        }

        return back()->with('message', 'Operation successful');
    }
}
