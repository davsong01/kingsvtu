<?php

namespace App\Models;

use App\Models\API;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderWebhook extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function provider(){
        return $this->belongsTo(API::class, 'api_id');
    }
}
