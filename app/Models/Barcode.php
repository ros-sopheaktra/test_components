<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Barcode extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table  = 'barcodes';
    
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
        'code',
        'barcode_format_id',

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
    public static function getBarcodes(){
        $respond = (object)[];
        try {
            $barcodes = Barcode::all();
            $respond->data = $barcodes;
            $respond->message = 'Records found';
        } catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->message = 'There is a problem while trying to get barcords record!';
        }
        return $respond;
    }

    /**
     * Get specific barcode records from database.
     * @param Integer $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getBarcode($id){
        $respond = (object)[];
        try {
            $barcode = Barcode::findOrFail($id);
            $respond->data = $barcode;
            $respond->message = 'Record found';
        } catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->message = 'There is a problem while trying to get barcord record!';
        }
        return $respond;
    }
    
    /**
     * Check if value is a number type.
     * @param String $value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function isNumber($value){
        $respond = (object)[];

        if(!is_numeric($value)){
            $respond->data    = false;
            $respond->message = 'Value is not a number!'; 
        } else {
            $respond->data    = $value;
            $respond->message = 'Value is a number!';              
        }

        return $respond;
    }

    /**
     * Check if value length meet the maximum of given length.
     * @param String $codeValue
     * @param Integer $maximumLength
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function isCodeMeetMaxLength($codeValue, $maximumLength){
        $respond = (object)[];

        if(strlen($codeValue) < $maximumLength){
            $respond->data = false;     
            $respond->message = 'code value does not reach the maximum length';     
        } else {
            $respond->data = true;     
            $respond->message = 'Code value is valid with maximumlength';
        }

        return $respond;
    }

    /**
    * validation request data.
    * @param Form_Request_Value $code
    * @param Form_Request_Value $code_digits
    * @return RespondObject [ data: result_data, message: result_message ] 
    */
    public static function checkReuqestValidation($code, $code_digits){
        $respond = (object)[];

        // valid code type
        $codeResult = Barcode::isNumber($code);
        if(!$codeResult->data){
            $respond->data = false;
            $respond->message = 'Barcode is not a number!';
            return $respond;
        }

        // check if barcode meet maximum length
        $codeDigitsResult = Barcode::isCodeMeetMaxLength($codeResult->data, $code_digits);
        if(!$codeDigitsResult->data){
            $respond->data = false;
            $respond->message = 'Barcode number does not meet the maximum of length '.$code_digits;
            return $respond;
        }

        $respond->data    = true;
        $respond->code    = $codeResult->data;
        $respond->message = 'All requests are valid';

        return $respond;
    }

     /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * Many barcode to one barcode_format relationship.
     * @return App/Modles/BarcodeFormat
     */
    public function barcodeFormat(){
        return $this->belongsTo(
            BarcodeFormat::class,
            'barcode_format_id',
        );
    }

    /**
     * One barcode to one product relationship.
     * @return App/Modles/Product 
     */
    public function product(){
        return $this->hasOne(
            Product::class,
            'barcode_id',
        );
    }

    /**
     * One barcode to one product variant relationship.
     * @return App/Modles/ProductVariant 
     */
    public function productvariant(){
        return $this->hasOne(
            ProductVariant::class,
            'barcode_id',
        );
    }

    /**
     * Many barcodes to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
