<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyMember extends Model
{
    protected $fillable = [
        'name',
        'email',
        'qr_code_path',
        'loyalty_points',
    ];
}
