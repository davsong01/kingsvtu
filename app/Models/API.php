<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class API extends Model
{
    use HasFactory;

    protected $table = 'a_p_is';

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }
}
