<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryOrderDetail extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'temporary_order_details';

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
        'order_quantity',
        'discount',
        'product_variant_id',
        'temporary_order_id',

    ];

    /**
     * ######################
     *     Relationships
     * ######################
     */

    /**
     * Many temporary order details to one temporary order.
     * @return App\Model\TemporaryOrder
     */
    public function temporaryOrder(){
        return $this->belongsTo(
            TemporaryOrder::class,
            'temporary_order_id',
        );
    }

    /**
     * One to many relationship with customer website
     * @return App\Model\ProductVariant
     */
    public function productvariant(){
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id',
        );
    }

}
