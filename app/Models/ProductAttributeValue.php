<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductAttributes;

class ProductAttributeValue extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_attribute_values';

    public function attribute()
    {
        return $this->belongsTo(ProductAttributes::class);
    }
}
