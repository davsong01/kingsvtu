<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
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
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Settings $settings)
    {
        $settings = Settings::first();
        if(!$settings){
            $settings = Settings::create([
                'logo' => '',
                'favicon' => '',
                'currency' => '&#8358;',
                'official_email' => '',
                'whatsapp_number' => '',
                'google_ad_code' => '',
                'seo_title' => '',
                'seo_description' => '',
            ]);
        }

        $currencies = [
            '&#8358;',
            '$'
        ];

        return view('admin.settings', compact('settings', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Settings $settings)
    {
        $settings->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Settings $settings)
    {
        //
    }
}
