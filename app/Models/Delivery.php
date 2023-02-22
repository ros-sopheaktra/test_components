<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'delivery';

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
        'sales_reference_no',
        'address',
        'customer_name',
        'attachment',
        'order_id',
        
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */

    // Delivery Helper Function [BEGIN]
        /**
         * Get delivery record from data best
         * @return string color name
         */
        public static function deliveries(){
            $respond = (object)[];
            
            try {
                $deliveries = Delivery::orderBy('id', 'DESC')->get();
                $respond->data = $deliveries;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find delivery record from data best
         * @return string color name
         */
        public static function delivery($id){
            $respond = (object)[];
            
            try {
                $delivery = Delivery::findOrFail($id);
                $respond->data = $delivery;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }
    // Delivery Helper Function [END]


    /**
     * ########################
     *      Relationship
     * ########################
     */
    
    /**
     * One to one relationship with order
     * @return App/Model/Order
     */
    public function order(){
        return $this->belongsTo(Order::class);
    }

    /**
     * Many color to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
