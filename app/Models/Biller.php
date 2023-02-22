<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Biller extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'billers';

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
        'name',
        'firstname',
        'lastname',
        'phone',
        'email',
        'customer_id',
        'supplier_id',

    ];

    /**
     * ########################
     *      Helper Functions
     * ########################
     */

    // Biller Helper Functions [BEGIN]
        /**
         * Get all biller records from database.
         * @return RespondObject [ data: data_result, message: message_result ]
         */
        protected static function getBillers(){
            $respond = (object)[];
            
            try {
                $billers = Biller::all();
                $respond->data    = $billers;
                $respond->message = 'All biller records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get biller records!'; 
            }

            return $respond;
        }

        /**
         * Get specific biller record from database.
         * @param Integer $id
         * @return RespondObject [ data: data_result, message: message_result ]
         */
        protected static function getBiller($id){
            $respond = (object)[];
            
            try {
                $biller = Biller::findOrFail($id);
                $respond->data    = $biller;
                $respond->message = 'Biller records found';
            } catch(ModelNotFoundException $ex) {
                $respond->data    = false;
                $respond->message = 'Biller records not found!'; 
            }

            return $respond;
        }
    // Biller Helper Functions [END] 

    // Customer Helper Functions [BEGIN] 
        /**
         * Get all customer records from database.
         * @return RespondObject [ data: data_result, message: message_result ]
         */
        protected static function getCustomers(){
            $respond = (object)[];
            
            try {
                $customers = Customer::all();
                $respond->data    = $customers;
                $respond->message = 'All customer records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get customer records!'; 
            }

            return $respond;
        }

        /**
         * Get specific customer record from database.
         * @param Integer $id
         * @return RespondObject [ data: data_result, message: message_result ]
         */
        protected static function getCustomer($id){
            $respond = (object)[];
            
            try {
                $customer = Customer::findOrFail($id);
                $respond->data    = $customer;
                $respond->message = 'Customer records found';
            } catch(ModelNotFoundException $ex) {
                $respond->data    = false;
                $respond->message = 'Customer records not found!'; 
            }

            return $respond;
        }
    // Customer Helper Functions [END] 

    /**
     * Get all supplier records from database
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getSuppliers(){
        $respond = (object)[];
        
        try {
            $suppliers = Supplier::all();
            $respond->data    = $suppliers; 
            $respond->message = 'Supplier records found'; 

        } catch(Exception $e) {
            $respond->data    = false; 
            $respond->message = 'Problem occured while trying to get supplier records!'; 
        }

        return $respond;
    }

    /**
     * Get specific supplier record from database
     * @param Integer $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getSupplier($id){
        $respond = (object)[];
        
        try {
            $supplier = Supplier::findOrFail($id);
            $respond->data    = $supplier; 
            $respond->message = 'Supplier record found'; 

        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Supplier record not found!'; 
        }

        return $respond;
    }
    
    /**
     * Check value and allow alphanumeric without whitespace only.
     * @param String $value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    protected static function checkValidString($value){
        $respond = (object)[];

        if (!preg_match("/^[a-zA-Z0-9]*$/",$value)) {
            $respond->data    = false;
            $respond->message = 'String is invalid!';
        } else {
            $respond->data    = $value;
            $respond->message = 'String is valid!';
        }

        return  $respond;
    }  
    
    /**
     * Check valid email value.
     * @param String $email
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    // public static function checkValidEmail($email){
    //     $respond = (object)[];

    //     if (!preg_match("/^[\w-]+[@]+[a-z]+\.+[a-z]*$/", $email)) {
    //         $respond->data    = false;
    //         $respond->message = 'Email is invalid!';
    //     } else {
    //         $respond->data    = $email;
    //         $respond->message = 'Email is valid!';
    //     }

    //     return $respond;
    // }

    /**
     * Check valid phone number in range of (9 to 10 digits)
     * @param String $phoneNumber
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidPhoneNumber($phoneNumber){
        $respond = (object)[];
        
        // check valid type
        if(!is_numeric($phoneNumber)){
            $respond->data    = false;
            $respond->message = 'Phone number should be number type!';
            return  $respond;
        }

        // check number range (10 to 15)
        if( strlen($phoneNumber) < 9 || strlen($phoneNumber) > 16 ) {
            $respond->data    = false;
            $respond->message = 'Phone number should be in range of 9 to 15 digit!';
            return  $respond;
        }

        $respond->data    = $phoneNumber;
        $respond->message = 'Phone number is valid';

        return  $respond;
    }

     /**
     * ########################
     *      Relationship
     * ########################
     */ 

    /**
     * Many billers to one supplier
     * @return App/Model/Supplier
     */
    public function supplier(){
        return $this->belongsTo(
            Supplier::class,
            'supplier_id',
        );
    }

    /**
     * Many billers to one customer
     * @return App/Model/Customer
     */
    public function customer(){
        return $this->belongsTo(
            Customer::class,
            'customer_id',
        );
    }

    /**
     * One biller to many sales
     * @return App/Model/Sales
     */
    public function sales(){
        return $this->hasMany(
            Sales::class,
            'biller_id',
        );
    }

    /**
     * One biller to many quotation
     * @return App/Model/Quotation
     */
    public function quotations(){
        return $this->hasMany(
            Quotation::class,
            'biller_id',
        );
    }

    /**
     * Many billers to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

    /**
     * ###################################
     *      Fast Validation Functions
     * ###################################
     */

    /**
     * Valida request data.
     * @param Form_Request_Value $name
     * @param Form_Request_Value $email
     * @param Form_Request_Value $phone
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkRequestValidation($firstname, $lastname ) {
        $respond = (object)[];

        // check valid first name
        $firstname = Biller::checkValidString($firstname);
        if(!$firstname->data){
            $firstname->message = 'Invalid biller first name value, only string without whitespace are allow!';
            return $firstname;
        }

        // check valid last name
        $lastname = Biller::checkValidString($lastname);
        if(!$lastname->data){
            $lastname->message = 'Invalid biller last name value, only string without whitespace are allow!';
            return $lastname;
        }

        // check valid email
        // $emailResult = Biller::checkValidEmail($email);
        // if(!$emailResult->data) {
        //     return $emailResult;
        // }

        // check valid phone number range ( 9 to 10 range)
        // $phoneNumberResult = Biller::checkValidPhoneNumber($phone);
        // if(!$phoneNumberResult->data) {
        //     return $phoneNumberResult;
        // }
       
        $respond->data      = true;
        $respond->firstname = strtolower($firstname->data);
        $respond->lastname  = strtolower($lastname->data);
        // $respond->email   = strtolower($emailResult->data);
        $respond->message   = 'All requests valided!';

        return $respond;
    }

}
