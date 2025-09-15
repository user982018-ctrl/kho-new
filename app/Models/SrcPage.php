<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Group;
class SrcPage extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'src_page';

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group', 'id');
    }

    public function userDigital(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_digital', 'id');
    }
}
