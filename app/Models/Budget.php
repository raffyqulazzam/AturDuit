<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'amount',
        'period',
        'period_start',
        'period_end',
        'spent',
        'is_active',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getProgressPercentageAttribute()
    {
        return $this->amount > 0 ? ($this->spent / $this->amount) * 100 : 0;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->spent;
    }
}
