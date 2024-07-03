<?php

namespace App\Models;

use App\Models\API;
use App\Models\Category;
use App\Models\Variation;
use App\Models\TransactionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function variations()
    {
        return $this->hasMany(Variation::class, 'product_id')->orderBy('created_at', 'DESC')->where('api_id', $this->api_id);
    }

    public function getVariationCountAttribute()
    {
        return $this->variations()->count();
    }

    public function api()
    {
        return $this->belongsTo(API::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionLog::class, 'product_id')->whereIn('status', ['delivered', 'success']);
    }

    public function customer_level_price($level)
    {
        $price = null;
        $price = Discount::where(['product_id' => $this->id, 'customer_level' => $level])->value('price');
        return $price;
    }
}
