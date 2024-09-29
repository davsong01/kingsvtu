<?php

namespace App\Models;

use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['captcha_settings' => 'array'];

    public function gateway(){
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway');
    }
}
