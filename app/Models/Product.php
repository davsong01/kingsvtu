<?php

namespace App\Models;

use App\Models\Api;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function variations(){
        return $this->hasMany(Variation::class);
    }

    public function api()
    {
        return $this->belongsTo(Api::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
