<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'shipping_details';

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
        'fname',
        'lname',
        'name',
        'contact', 
        'email', 
        'address', 
        'apartment_unit', 
        'note', 
        'sangkat_name', 
        'district_name', 
        'receiver_numbers', 
        'shipping_address_id',
        'order_id'
    ];

    /**==================
     *   Relationships
    =====================*/

    /**
     * Many to one relationship with shipping address
     * @return App/Models/ShippingAddress
     */
    public function shippingAddress(){
        return $this->belongsTo(ShippingAddress::class);
    }

    /**
     * Many to one relationship with order
     * @return App/Models/Order
     */
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
