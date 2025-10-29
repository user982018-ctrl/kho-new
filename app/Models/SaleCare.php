<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Call;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Orders;
use App\Models\SrcPage;
use Illuminate\Database\Eloquent\Relations\hasOne;

class SaleCare extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_care';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_order',
        'id_order_new',
        'issetDuplicate',
        'duplicate_id',
        'type_TN',
        'has_TN',
        'time_update_TN',
        'time_wakeup_TN',
        'm_id',
        'is_duplicate',
        'old_customer',
        'has_old_order',
        'group_id',
        'sex',
        'full_name',
        'phone',
        'address',
        'messages',
        'TN_can',
        'type_tree',
        'product_request',
        'reason_not_buy',
        'note_info_customer',
        'is_runjob',
        'result_call',
        'next_step',
        'assign_user',
        'src_id',
        'page_id',
        'page_name',
        'page_link',
        'created_at',
        'updated_at'
    ];

     /**
     * Get the shippingOrder for the Orders.
     */
    // public function call(): HasMany
    // {
    //     return $this->HasMany(Call::class, 'id','result_call');
    // }
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class, 'result_call', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_user', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'id_order', 'id');
    }

    public function orderNew(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'id_order_new', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function typeTN(): BelongsTo
    {
        return $this->belongsTo(CategoryCall::class, 'type_TN', 'id');
    }

    public function resultCall(): BelongsTo
    {
        return $this->belongsTo(Call::class, 'result_call', 'id');
    }

    public function listHistory(): HasMany
    {
        return $this->hasMany(SaleCareHistoryTN::class,'sale_id')->orderBy('id', 'desc');;
    }

    public function getSrcPage(): BelongsTo
    {
        return $this->belongsTo(SrcPage::class, 'src_id', 'id');
    }
}
