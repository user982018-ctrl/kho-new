<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductVariantAttributeValue;

class ProductVariant extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_variants';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        // return $this->belongsToMany(ProductVariantAttributeValue::class, 'product_variant_id');
        return $this->hasMany(ProductVariantAttributeValue::class, 'product_variant_id');
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }
}
