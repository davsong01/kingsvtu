<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservedAccountCallback extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function transaction(){
        return $this->belongsTo(TransactionLog::class, 'transaction_reference','transaction_id');
    }
}
