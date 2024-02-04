<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        if(auth()->user()->type == 'admin'){
            return view('admin.dashboard');
        }else{
            return view('customer.dashboard');
        }
    }
}
