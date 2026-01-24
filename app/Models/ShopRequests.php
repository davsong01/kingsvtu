<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopRequests extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['request_details' => 'array'];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
