<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerWebsite extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'customer_websites';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that mass assignable
     * @var Array
     */
    protected $fillable = [
        'phone',
        'day',
        'month',
        'point',
        'birthday',
        'user_id',
        'customer_group_id'
        
    ];

    /**
     * ########################
     *      Helper Functions
     * ########################
     */   

     /**
     * Get all customer records from database
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getCustomers(){
        $respond = (object)[];
        
        try {
            $customers = CustomerWebsite::all();
            $respond->data = $customers;
            $respond->message = 'Customer records found';
        } catch(Exception $ex) {
            $respond->data = false;
            $respond->message = $ex->getMessage();
        }

        return $respond;
    }

    /**
     * Get specific customer record from database.
     * @param Int $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function  getCustomer($id) {
        $respond = (object)[];

        try {
            $customer = CustomerWebsite::findOrFail($id);
            $respond->data    = $customer;
            $respond->message = 'Customer record found';
        } catch(ModelNotFoundException $e) {
            $respond->data    = false;
            $respond->message = $e->getMessage();
        }

        return $respond;
    }

    /**
     * Get all customer group records from database
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getCustomerGroups(){
        $respond = (object)[];
        
        try {
            $customerGroups = CustomerGroup::all();
            $respond->data = $customerGroups;
            $respond->message = 'Customer group records found';
        } catch(Exception $ex) {
            $respond->data = false;
            $respond->message = 'problem occured while trying to get customer group records!';
        }

        return $respond;
    }

     /**
     * Get specific customer group record from database.
     * @param Int $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function  getCustomerGroup($id) {
        $respond = (object)[];

        try {
            $customerGroup = CustomerGroup::findOrFail($id);
            $respond->data    = $customerGroup;
            $respond->message = 'Customer group record found';
        } catch(ModelNotFoundException $e) {
            $respond->data    = false;
            $respond->message = 'Customer group record not round!';
        }

        return $respond;
    }

    // District Helper Functions [BEING]

        /**
         * Get all district records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getDistricts(){
            $respond = (object)[];
            
            try {
                $districts = District::all();
                $respond->data = $districts;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }
    // District Helper Functions [END]

    /**
     * ########################
     *      Relationship
     * ########################
     */
     /**
     * One customer to one user relationship.
     * @return App\Model\User
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /** 
     * Restore the customer shopping cart into session.
     * @return App\Model\Cart
     */
    public function shoppingCart(){
        Cart::restore($this->id);
    }

    /**
     * Many customers to one customer group relationship.
     * @return App\Models\CustomerGroup
     */
    public function cusgroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    /**
     * One customer to one order relationship.
     * @return App\Models\Order
     */
    public function order(){
        return $this->belongsTo(
            User::class,
            'order_id',
            'order_id'
        );
    }

    /**
     * One customers to many shipping addresses relationship.
     * @return App\Models\ShippingAddress
     */
    public function shippingAddresses(){
        return $this->hasMany(ShippingAddress::class);
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
