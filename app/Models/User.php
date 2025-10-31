<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'balance'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
