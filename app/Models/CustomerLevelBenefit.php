<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLevelBenefit extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = ['customer_levels' => 'array'];

}
