<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductAttributeValue;


class ProductVariantAttributeValue extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_variant_attribute_value';

    public function attribute()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'attribute_value_id');
    }
}
