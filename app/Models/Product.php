<?php

namespace App\Models;

use App\Models\Variation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Api;

class Product extends Model
{
    use HasFactory;

    public function variations(){
        return $this->hasMany(Variation::class);
    }

    public function api()
    {
        return $this->belongsTo(Api::class);
    }
}
