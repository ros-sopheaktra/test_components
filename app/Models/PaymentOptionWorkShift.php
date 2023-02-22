<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOptionWorkShift extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'payment_option_work_shift';

    /**
     * Primary key.
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'payment_in_usd',
        'payment_in_rial',
        'payment_method',
        'work_shift_id',
    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

    /**
     * Many products to one workShift.
     * @return App\Model\WorkShift
     */
    public function workShift(){
        return $this->belongsTo(
            WorkShift::class
        );
    }
}
