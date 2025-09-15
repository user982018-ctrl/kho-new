<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShippingOrder;
use App\Models\SaleCare;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\hasOne;

class Orders extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

     /**
     * Get the shippingOrder for the Orders.
     */
    public function shippingOrder(): hasOne
    {
        return $this->hasOne(ShippingOrder::class, 'order_id');
    }

    public function saleCare(): BelongsTo
    {
        return $this->belongsTo(SaleCare::class, 'sale_care', 'id');
    }
}
