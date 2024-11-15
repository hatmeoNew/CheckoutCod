<?php

namespace NexaMerchant\CheckoutCod\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use NexaMerchant\CheckoutCod\Contracts\OrderCod as OrderCodContract;

class OrderCod extends Model implements OrderCodContract
{
    private $table = 'order_cods';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id','ip_address', 'ip_country'];

}