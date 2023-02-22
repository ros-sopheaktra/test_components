<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Supplier extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'suppliers';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that mass assignable.
     * @var Array
     */
    protected $fillable = [
        'state',
        'address',
        'company',
        'country',
        'vat_number',
        'gts_number',
        'postal_code',
        'city_province',

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

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
     * Check valid name value.
     * @param String $name
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    protected static function checkValidString($name){
        $respond = (object)[];

        if (!preg_match("/^[a-zA-Z0-9' ]*$/",$name)) {
            $respond->data    = false;
            $respond->message = ' is invalid!';
        } else {
            $respond->data    = $name;
            $respond->message = ' is valid!';
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
     * Check valid postal code in range of 1 to 5 digits.
     * @param String $postalCode
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    protected static function checkValidPostalCode($postalCode){
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
     * @param HTML_Form_Request $country
     * @param HTML_Form_Request $address
     * @param HTML_Form_Request $city
     * @param HTML_Form_Request $state
     * @param HTML_Form_Request $vat_number
     * @param HTML_Form_Request $gts_number
     * @param HTML_Form_Request $postal_code
     */
    public static function checkReuqestValidation($company, $country, $address, $city, $state, $vat_number, $gts_number, $postal_code){
        $respond = (object)[];

        // validat company value
        $companyResult = Supplier::checkValidString($company);
        if(!$companyResult->data) {
            $companyResult->message = 'Company value is not valid!';
            return $companyResult;
        }

        // validat country value
        $countryResult = Supplier::checkValidString($country);
        if(!$countryResult->data) {
            $countryResult->message = 'Country value is not valid!';
            return $countryResult;
        }

        // validat address value
        $addressResult = Supplier::checkValidCustomString($address);
        if(!$addressResult->data) {
            $addressResult->message = 'Address value is not valid!';
            return $addressResult;
        }

        // validat city value
        $cityResult = Supplier::checkValidString($city);
        if(!$cityResult->data) {
            $cityResult->message = 'City value is not valid!';
            return $cityResult;
        }

        // validat state value
        $stateResult = Supplier::checkValidString($state);
        if(!$stateResult->data) {
            $stateResult->message = 'State value is not valid!';
            return $stateResult;
        }

        // validat vat number value
        $vatResult = Supplier::checkValidString($vat_number);
        if(!$vatResult->data) {
            $vatResult->message = 'Vat number is not valid!';
            return $vatResult;
        }

        // validat gts number value
        $gtsResult = Supplier::checkValidString($gts_number);
        if(!$gtsResult->data) {
            $gtsResult->message = 'Gts number is not valid!';
            return $gtsResult;
        }

        // validat postal code value
        $postalCodeResult = Supplier::checkValidPostalCode($postal_code);
        if(!$postalCodeResult->data) {
            return $postalCodeResult;
        }

        $respond->data       = true;
        $respond->company    = $companyResult->data;
        $respond->country    = $countryResult->data;
        $respond->address    = $addressResult->data;
        $respond->city       = $cityResult->data;
        $respond->state      = $stateResult->data;
        $respond->vat        = $vatResult->data;
        $respond->gts        = $gtsResult->data;
        $respond->postalCode = $postalCodeResult->data;

        return $respond;
    }

    /**
     * ########################
     *      Relationship
     * ########################
     */

     /**
     * Many billers to one supplier
     * @return App/Model/Biller
     */
    public function billers(){
        return $this->belongsTo(
            Biller::class,
            'supplier_id',
        );
    }

    /**
     * One product unit to many suppliers.
     * @return App/Model/Product
     */
    public function products(){
        return $this->hasMany(
            Product::class,
            'supplier_id',
        );
    }

    /**
     * Many suppliers to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
