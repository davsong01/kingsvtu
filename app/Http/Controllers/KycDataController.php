<?php

namespace App\Http\Controllers;

use App\Models\KycData;
use App\Models\Customer;
use Illuminate\Http\Request;

class KycDataController extends Controller
{

    public function adminKycIndex(Request $request)
    {
        $allCustomers = Customer::with('user')->get();

        $query = Customer::with('user')->orderByDesc('id');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('kyc_status', $request->status);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('firstname', 'like', '%' . $search . '%')
                    ->orWhere('lastname', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->paginate(paginationRecords())->withQueryString();
        $totalCustomers = $allCustomers->count();
        $verifiedCount = $allCustomers->filter(fn ($customer) => getFinalKycStatus($customer->id) === 'verified')->count();
        $awaitingCount = $allCustomers->filter(fn ($customer) => getFinalKycStatus($customer->id) === 'awaiting-approval')->count();
        $unverifiedCount = $allCustomers->filter(fn ($customer) => in_array(getFinalKycStatus($customer->id), ['unverified', 'pending'], true))->count();

        return view(themeView('admin', 'kyc_data'), compact(
            'customers',
            'totalCustomers',
            'verifiedCount',
            'awaitingCount',
            'unverifiedCount'
        ));
    }

    public function verifyBVN($bvn)
    {
        $verify = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->verifyBvn($bvn);

        return $verify;
    }

    public function getLgaByStateName($state, $value = null)
    {
        $lgas = getLgas($state);
        $res = '';
        
        if (!empty($lgas)) {
            foreach ($lgas as $lga) {
                $selected = !empty($value) && $value == $lga ? 'selected' : '';
                $res .= '<option value="' . $lga . '" ' . $selected . '>' . $lga . '</option>';
            }
        }
        
        return response()->json($res);
    }


    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(KycData $kycData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KycData $kycData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KycData $kycData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KycData $kycData)
    {
        //
    }
}
