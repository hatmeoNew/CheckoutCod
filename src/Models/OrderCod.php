<?php

namespace NexaMerchant\CheckoutCod\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use NexaMerchant\CheckoutCod\Contracts\OrderCod as OrderCodContract;

class OrderCod extends Model implements OrderCodContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip_address', 'ip_country'];

}