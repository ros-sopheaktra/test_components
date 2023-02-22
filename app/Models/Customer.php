<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Customer extends Model
{
    use HasFactory;
    
    /**
     * Table name
     * @var String
     */
    protected $table = 'customers';

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
        'email',
        'phone',
        'state',
        'address',
        'company',
        'vat_tin',
        'vat_number',
        'gts_number',
        'postal_code',
        'city_province',
        'customer_group_id',
        
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
            $customers = Customer::all();
            $respond->data = $customers;
            $respond->message = 'Customer records found';
        } catch(Exception $ex) {
            $respond->data = false;
            $respond->message = 'problem occured while trying to get customer records!';
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
            $customer = Customer::findOrFail($id);
            $respond->data    = $customer;
            $respond->message = 'Customer record found';
        } catch(ModelNotFoundException $e) {
            $respond->data    = false;
            $respond->message = 'Customer record not round!';
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

    /**
     * Check valid email value.
     * @param String $email
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidEmail($email){
        $respond = (object)[];

        if (!preg_match("/^[\w-]+[@]+[a-z]+\.+[a-z]*$/", $email)) {
            $respond->data    = false;
            $respond->message = 'Email is invalid!';
        } else {
            $respond->data    = $email;
            $respond->message = 'Email is valid!';
        }

        return $respond;
    }

    /**
     * Check valid name value.
     * @param String $name
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidString($name){
        $respond = (object)[];

        if (!preg_match("/^[a-zA-Z0-9' ]*$/",$name)) {
            $respond->data    = false;
            $respond->message = 'String is invalid!';
        } else {
            $respond->data    = $name;
            $respond->message = 'String is valid!';
        }

        return  $respond;
    }

    /**
     * Check valid company name value.
     * @param String $name
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidCompanyName($name){
        $respond = (object)[];

        if (!preg_match("/^[a-zA-Z0-9 ,.]*$/",$name)) {
            $respond->data    = false;
            $respond->message = 'String is invalid!';
        } else {
            $respond->data    = $name;
            $respond->message = 'String is valid!';
        }

        return  $respond;
    }

    /**
     * Check valid value of alphanumeric (#,.- ) only.
     * @param String $value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    protected static function checkValidCustomString($value){
        $respond = (object)[];

        if (!preg_match("/^[a-zA-Z0-9 #,.-]*$/",$value)) {
            $respond->data    = false;
            $respond->message = ' is invalid!';
        } else {
            $respond->data    = $value;
            $respond->message = ' is valid!';
        }

        return  $respond;
    }

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

        // check number range (10 to 11)
        if( strlen($phoneNumber) < 9 || strlen($phoneNumber) > 10 ) {
            $respond->data    = false;
            $respond->message = 'Phone number should be in range of 9 to 10 digit!';
            return  $respond;
        }

        $respond->data    = $phoneNumber;
        $respond->message = 'Phone number is valid';

        return  $respond;
    }

    /**
     * Check valid postal code in range of 1 to 5 digits.
     * @param String $postalCode
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidPostalCode($postalCode){
        $respond = (object)[];

        // check postal code type
        if(!is_numeric($postalCode)){
            $respond->data    = false;
            $respond->message = 'Postal code is not a number!';
            return  $respond;
        }
        // check postal code range 1 to 5 digits
        if( strlen($postalCode) != 5 ){
            $respond->data    = false;
            $respond->message = 'Postal code digit should be 5 digits!';
            return  $respond;
        }

        $respond->data    = $postalCode;
        $respond->message = 'Postal code number is valid';

        return  $respond;
    }

    /**
     * Check request valid value and redirect back base on option.
     * @param HTML_Form_Request $company
     * @param HTML_Form_Request $email
     * @param HTML_Form_Request $phone
     * @param HTML_Form_Request $address
     * @param HTML_Form_Request $postal_code
     * @param HTML_Form_Request $vat_number
     * @param HTML_Form_Request $gts_number
     */
    public static function checkReuqestValidation($company, $email, $phone, $address, $postal_code, $vat_number, $vat_tin, $gts_number){
        $respond = (object)[];

        // check valid company name
        $companyNameResult = Customer::checkValidCompanyName($company);
        if(!$companyNameResult->data) {
            $companyNameResult->message = 'Company name value is invalid!';
            return $companyNameResult;
        }

        // check valid email
        $emailResult = Customer::checkValidEmail($email);
        if(!$emailResult->data) {
            return $emailResult;
        }

        // check valid phone numner range ( 9 to 10 range)
        $phoneNumberResult = Customer::checkValidPhoneNumber($phone);
        if(!$phoneNumberResult->data) {
            return $phoneNumberResult;
        }

        // check valid address
        $addressResult = Customer::checkValidCustomString($address);
        if(!$addressResult->data) {
            $addressResult->message = 'Address value is invalid!';
            return $addressResult;
        }

        // check valid postal code
        $postalCodeResult = Customer::checkValidPostalCode($postal_code); 
        if(!$postalCodeResult->data) {
            return $postalCodeResult;
        }

        // check valid vat number
        $vatNumberResult = Customer::checkValidString($vat_number);
        if(!$vatNumberResult) {
            $vatNumberResult->message = 'Vat number value is invalid!';
            return $vatNumberResult;
        }

        // check valid vat tin on != null
        if($vat_tin != null){
            if( !preg_match("/^[a-zA-Z0-9-]*$/",$vat_tin) ){
                $respond->data    = false;
                $respond->message = 'Vat Tin value invalide, allow only alphanumeric with - symbol!';
                return $respond;
            }
        }

        // check valid gts number
        $gtsNumberResult = Customer::checkValidString($gts_number);
        if(!$gtsNumberResult) {
            $gtsNumberResult->message = 'Gts number value is invalid!';
            return $gtsNumberResult;
        }

        $respond->data          = true;
        $respond->email         = $emailResult->data;
        $respond->phone         = $phoneNumberResult->data;
        $respond->address       = $addressResult->data;
        $respond->company       = $companyNameResult->data;
        $respond->vat_tin       = strtolower($vat_tin);
        $respond->vat_number    = $vatNumberResult->data;
        $respond->gts_number    = $gtsNumberResult->data;
        $respond->postal_code   = $postalCodeResult->data;

        return $respond;
    }

    /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * One customer to many billers.
     * @return App/Model/Biller
     */
    public function billers(){
        return $this->hasMany(
            Biller::class,
            'customer_id',
        );
    }


    /**
     * Many customers to one customer group.
     * @return App/Model/CustomerGroup
     */
    public function customerGroup(){
        return $this->belongsTo(
            CustomerGroup::class,
            'customer_group_id',
        );
    }

    /**
     * Many customers to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
