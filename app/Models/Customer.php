<?php

namespace App\Models;

use App\Models\KycData;
use App\Models\CustomerLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function level(){
        return $this->belongsTo(CustomerLevel::class, 'customer_level');
    }

    public function kycdata()
    {
        return $this->belongsTo(KycData::class, 'customer_id');
    }
}
