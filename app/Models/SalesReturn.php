<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SalesReturn extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'sale_returns';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'date',
        'reference_no',
        'charge',
        'return_total',
        'return_grandtotal',
        'return_item',
        'sales_return_status',
        'sales_return_note',
        'staff_note', 
        'order_id'
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */

    // Sales Return Helper Function [BEGIN]
        /**
         * Get all sales return based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSalesReturns(){
            $respond = (object)[];
        
            try {
                $salesreturns = SalesReturn::orderBy('id', 'DESC')->get();
                $respond->data = $salesreturns;
                $respond->message = 'Sales return records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find sales return based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSalesReturn($id){
            $respond = (object)[];
        
            try {
                $salesreturn = SalesReturn::findOrFail($id);
                $respond->data = $salesreturn;
                $respond->message = 'Sales return records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

    // Sales Return Function [END]

    // Order Helper Functions [BEING]
       /**
         * Get specific order record based on given id from database.
         * @param Integer $id
         * @return ObejectRespond [ data: date_result, message: message_result ]
         */
        public static function getOrder($id){
            $respond = (object)[];

            try {
                $order = Order::findOrFail($id);
                $respond->data            = $order;
                $respond->message         = 'Order record found';
            } catch(ModelNotFoundException $ex) {
                $respond->data    = false;
                $respond->message = 'Order record not found!';
            }

            return $respond;
        }
    // Order Helper Functions [END]

    // Website Customer Function [BEGIN]

        /**
         * Get all website customer records from database.
         * @return ObjectRespond [ data: data_result, message: message_result ]
         */
        public static function getWebsiteCustomers(){
            $respond = (object)[];

            try {
                $website_cus      = CustomerWebsite::all();
                $respond->data    = $website_cus;
                $respond->message = 'All product records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get product records!';
            }

            return $respond;
        }

    // Website Customer Function [END]

    // Shop Helper Function [BEGIN]

        /**
         * Get all shop records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getShops(){
            $respond = (object)[];
            
            try {
                $shops = Shop::all();
                $respond->data = $shops;
                $respond->message = 'Shop records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get shop records!'; 
            }

            return $respond;
        }

        /**
         * Get specific shop records from database based on ginven id.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getShop($id){
            $respond = (object)[];

            try {
                $shop = Shop::findOrFail($id);
                $respond->data    = $shop;
                $respond->message = 'Shop records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get shop records!'; 
            }

            return $respond;
        }
    // Shop Helper Function [BEGIN]

    /**
     * ########################
     *      Relationship
     * ########################
     */

     /**
     * Many to one relation with order.
     * @return App/Model/Orders
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

    /**
     * Many users to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistoryables(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }
}
