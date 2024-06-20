<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfers_History extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'type', 'amount', 'Balance_after', 'Debt_after'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
