<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'discount_percent', 'is_active'])]
class Coupon extends Model
{
    //
}
