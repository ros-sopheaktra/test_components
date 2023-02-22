<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShippingAddress extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'shipping_addresses';

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
        'address', 
        'apartment_unit', 
        'note', 
        'sangkat_id', 
        'customer_website_id'
    ];

    /**===================
     *  Helper Functions
     *====================*/

     /**
     * Get shippingAddress record form db base on given id
     * @param int $id
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function getShippingAddress($id){
        $respond = (object)[];
 
        try{
            $shippingAddress = ShippingAddress::findOrFail($id);
            $respond->data = $shippingAddress;
            $respond->messange = 'ShippingAddress record found';
        }catch(ModelNotFoundException $e) {
             $respond->data = false;
            $respond->messange = 'ShippingAddress record not found!';
         }
         return $respond;
     }


    /**==================
     *   Relationships
    =====================*/

    /**
     * Many to one relationship with customer
     * @return App/Models/CustomerWebsite
     */
    public function customer_website(){
        return $this->belongsTo(CustomerWebsite::class);
    }

    /**
     * Many to one relationship with sangkat
     * @return App/Models/Sangkat
     */
    public function sangkat(){
        return $this->belongsTo(Sangkat::class);
    }

    /**
     * Many to one relationship with shipping detail
     * @return App/Models/ShippingDetail
     */
    public function shippingDetails(){
        return $this->hasMany(ShippingDetail::class);
    }

}
