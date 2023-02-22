<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BarcodeFormat extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table  = 'barcode_formats';
    
    /**
     * Primary key
     * @var String
     */
    protected $primaryKey  = 'id';

    /**
     * The attributes that are mass assignable
     * @var Array
     */
    protected $fillable  = [
        'format',
        'format_slug',
        'code_digits',

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

     /**
     * Get all barcode records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getBarcodeFormats(){
        $respond = (object)[];

        try {
            $barcodes = BarcodeFormat::all();
            $respond->data = $barcodes;
            $respond->message = 'Records found!';
        } catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->messsage = 'There is a problem while trying to get barcode format record!';
        }

        return $respond;
    }

    /**
     * Get specific barcode record from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getBarcodeFormat($id){
        $respond = (object)[];

        try {
            $barcode = BarcodeFormat::findOrFail($id);
            $respond->data = $barcode;
            $respond->message = 'Records found!';
        } catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->messsage = 'Barcode format record not found!';
        }

        return $respond;
    }

    /**
     * Check valid string and replace all whitespace with dash (-).
     * @param String value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidString($value){
        $respond = (object)[];
        $value = preg_replace("/[\s$@_*]+/", "-", $value);
        
        if ( !preg_match("/^([a-z0-9]+-)*[a-z0-9]+$/i", $value) ) {
            $respond->data = false;
            $respond->message = "Barcode format invalid, please noted that no space before and end of format!";
        } else {
            $respond->data = $value;
            $respond->message = 'Format valid';
        }

        return $respond;
    }

    /**
     * Check valid barcode digits number with specific length (128).
     * @param Int $value
     * @param Int $maxDigits
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidDigits($value, $maxDigits){
        $respond = (object)[];
        
        // check type
        if(!is_numeric($value)){
            $respond->data = false;
            $respond->message = "Barcode digits type is mismatch!";
            return $respond;   
        }

        // validat value
        if($value<0 || $value>$maxDigits){
            $respond->data = false;
            $respond->message = "Barcode digits should start from 1 and should not bet greater than ".$maxDigits;
            return $respond;   
        }

        $respond->data = $value;
        $respond->message = 'Value is validted';
        return $respond;
    }

    /**
    * validation request data.
    * @param Form_Request_Value $format
    * @param Form_Request_Value $code_digits
    * @return RespondObject [ data: result_data, message: result_message ] 
    */
    public static function checkReuqestValidation($format, $code_digits){
        $respond = (object)[];

        // valid format
        $formatResult = BarcodeFormat::checkValidString($format);
        if(!$formatResult->data){
            $respond = $formatResult;
            return $respond;
        }

        // valid barcode digits
        $codeDigitsResult = BarcodeFormat::checkValidDigits($code_digits, 128);
        if(!$codeDigitsResult->data){
            $respond = $codeDigitsResult;
            return $respond;
        }

        $respond->data       = true;
        $respond->format     = $formatResult->data;
        $respond->codeDigits = $codeDigitsResult->data;
        $respond->message    = 'All request are valided';

        return $respond;
    }

     /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * One barcode_format to many barcodes relationship.
     * @return App/Modles/Barcode 
     */
    public function barcodes(){
        return $this->hasMany(
            Barcode::class,
            'barcode_format_id',
        );
    }

    /**
     * Many barcode formats to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
