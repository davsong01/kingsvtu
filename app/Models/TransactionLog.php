<?php

namespace App\Models;

use App\Models\Wallet;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionLog extends Model
{
    use HasFactory;
    protected $appends = ['type'];
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'transaction_id', 'transaction_id');
    }

    public function getTypeAttribute()
    {
        return $this->wallet->type ?? 'new';
    }
}
