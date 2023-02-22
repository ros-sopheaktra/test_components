<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryMiscellaneousOrderDetail extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'temporary_misc_order_details';

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
        'name',
        'price',
        'discount',
        'order_quantity',
        'temporary_order_id',

    ];

    /**
     * ######################
     *     Relationships
     * ######################
     */
        /**
         * Many temporary miscellaneous order details to one temporary order.
         * @return App\Model\TemporaryOrder
         */
        public function temporaryOrder(){
            return $this->belongsTo(
                TemporaryOrder::class,
                'temporary_order_id',
            );
        }

}
