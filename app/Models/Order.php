<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'custom_id', 'user_id', 'plan_id', 'months', 'coupon_id',
    'full_name', 'phone', 'address', 'city', 'postal_code', 'country',
    'subtotal', 'discount', 'tax', 'total', 'status', 'stripe_session_id', 'prorated_discount'
])]
class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
