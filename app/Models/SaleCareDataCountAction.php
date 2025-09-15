<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleCareDataCountAction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_care_count_action';


    public function scCountAction()
    {
        return $this->hasMany(SaleCareDataCountActionDetail::class, 'id_sc_count');
    }
}
