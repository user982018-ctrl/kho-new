<?php


namespace App\Helpers;
use App\CategoryProduct;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\ShippingOrder;
use App\Models\User;
use App\Models\Call;
use App\Http\Controllers\ProductController;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Orders;
use App\Models\SaleCare;
use Illuminate\Support\Facades\Log;
use App\Models\Telegram;
use App\Models\Pancake;
use App\Models\LadiPage;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\Spam;
use PHPUnit\TextUI\Help;
use App\Models\SrcPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

setlocale(LC_TIME, 'vi_VN.utf8');

class HelperProduct
{
    public static function getNameAttributeByVariantId($id)
    {
        $variant = ProductVariant::find($id);
        $fullNameAttribute = ' ';
        if ($variant) {
            $listIdAttr = $variant->attributeValues->pluck('attribute_value_id')->toArray();
            // foreach ($variant->attributeValues as $attribute) {
            //     if (!empty($attribute->attribute)) {
            //         $fullNameAttribute  .= $attribute->attribute->value . ' ';
            //     }
            // }
            $attributes = ProductAttributeValue::whereIn('id', $listIdAttr)
                ->orderBy('orderBy', 'DESC')
                ->get();
            foreach ($attributes as $attribute) {
                $fullNameAttribute  .= $attribute->value . ' ';
            }
        }
        return $fullNameAttribute;
    }

    public static function getProductVariantById($id)
    {
        return ProductVariant::find($id);
    }
}