<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Variation;
use App\Models\TransactionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class API extends Model
{

    use HasFactory;
    protected $guarded = [];
    protected $table = 'a_p_is';
    protected $casts = [
        'balance' => 'float',
        'availability_score' => 'integer',
        'availability_check_transactions_count' => 'integer',
        'successful_transactions' => 'integer',
        'failed_transactions' => 'integer',
        'availability_checked_at' => 'datetime',
    ];

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => date("M jS, Y", strtotime($value)),
        );
    }

    protected function availabilityStatusClass(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => match ($attributes['availability_status'] ?? null) {
                'critical' => 'critical',
                'unstable' => 'unstable',
                'average' => 'average',
                'stable' => 'stable',
                'healthy' => 'healthy',
                default => null,
            },
        );
    }

    protected function availabilityStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => match ($attributes['availability_status'] ?? null) {
                'critical' => 'Critical',
                'unstable' => 'Unstable',
                'average' => 'Average',
                'stable' => 'Stable',
                'healthy' => 'Healthy',
                default => null,
            },
        );
    }
    
    public function products(){
        return $this->hasMany(Product::class, 'api_id');
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionLog::class, 'api_id');
    }

}
