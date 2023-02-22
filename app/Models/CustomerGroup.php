<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerGroup extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String 
     */
    protected $table = 'customer_groups';

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
        'name',
        'discount',

    ];

    /**
     * ########################
     *      Helper Functions
     * ########################
     */

     /**
     * Get all customer group records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getCustomerGroups() {
        $respond = (object)[];

        try {
            $customerGroups = CustomerGroup::all();
            $respond->data    = $customerGroups;
            $respond->message = 'Customer group records found';             
         } catch(Exception $e) {
            $respond->data    = false;
            $respond->message = 'Problem occured while trying to get customer group records!';
        }

        return $respond;
    }

     /**
     * Get specific customer group record from database.
     * @param Int id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getCustomerGroup($id) {
        $respond = (object)[];

        try {
            $customerGroup = CustomerGroup::findOrFail($id);
            $respond->data    = $customerGroup;
            $respond->message = 'Customer group record found';             
         } catch(ModelNotFoundException $e) {
            $respond->data    = false;
            $respond->message = 'Customer group record not found!';
        }

        return $respond;
    }

    /**
     * Check valid string that contain only string and number.
     * @param String value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidStringOnly($value){
        $respond = (object)[];
        $value = preg_replace("/[\s$@_*]+/", " ", $value);
        
        if ( !preg_match("/^([a-zA-Z0-9 ])+$/i", $value) ) {
            $respond->data = false;
            $respond->message = "Value invalid, only string and number are allow on customer group name!";
        } else {
            $respond->data = $value;
            $respond->message = 'value valid';
        }

        return $respond;
    }

    /**
     * Check valid discount value.
     * @param String $discountValue
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidDiscount($discountValue){
        $respond = (object)[];

        // check valid type
        if(!is_numeric($discountValue)){
            $respond->data = false;
            $respond->message = 'Discount value is not a number!';
            return $respond;
        };

        // check value in range (0.1% - 100%)
        if( (float)$discountValue > 100 || (float)$discountValue < 0){
            $respond->data = false;
            $respond->message = 'Discount value should be inside the range of 0.1% to 100%!';
            return $respond;
        }

        $respond->data = $discountValue;
        $respond->message = 'Discount value is valid';

        return $respond;
    }

    /**
     * validation request data.
     * @param Form_Request_Value $name
     * @param Form_Request_Value $discountValue
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkReuqestValidation($name, $discountValue){
        $respond = (object)[];

        // check customer group name
        $groupNameResult = CustomerGroup::checkValidStringOnly($name);
        if(!$groupNameResult->data){
            $respond = $groupNameResult;
            return $respond;
        }

        // check discount value
        $discountValueResult = CustomerGroup::checkValidDiscount($discountValue);

        $respond->data     = true;
        $respond->name     = strtolower($groupNameResult->data);
        $respond->discount = $discountValueResult->data;
        $respond->message  = 'All data are valided!';

        return $respond;
    }

    /**
     * ########################
     *      Relationship
     * ########################
     */

    // one to many
    public function customers(){
        return $this->hasMany(
            CustomerGroup::class,
            'customer_group_id',
        );
    }

    // one to many
    public function customer_websites(){
        return $this->hasMany(CustomerWebsite::class, 'customer_group_id');
    }

    /**
     * Many customer groups to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
