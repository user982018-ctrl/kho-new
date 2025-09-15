<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SaleCare;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LadiPage extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ladi_page';

    // /**
    //  * Get the author of the post.
    //  */
    // public function call(): BelongsTo
    // {
    //     return $this->belongsTo(SaleCare::class,'result_call');
    // }
}
