<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'order_details';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * Attribute that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'name',
        'cost',
        'price',
        'quantity',
        'thumbnail',
        'return_quantity',
        'detail_invoice',
        'sku',
        'discount',
        'product_variant_id',
        'order_id',
        
    ];

    /**==================
     *   Relationships
    =====================*/

    /**
     * One order detail to many product variants.
     * @return App/Models/ProductVariant
     */
    public function productVariant()
    {
        return $this->belongsTo(
            ProductVariant::class, 
            'product_variant_id', 
            'id'
        );
    }

    /**
     * Many order details to one order
     * @return App/Models/Order
     */
    public function order()
    {
        return $this->belongsTo(
            Order::class, 
            'order_id', 
            'id'
        );
    }

}
