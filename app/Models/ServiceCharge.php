<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ServiceCharge extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'service_charges';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * @var Array
     */
    protected $fillable = [
        'name',
        'code',
        'warehouse_id',
        'price',
        'attachment',
        'description',

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

    /**
     * Get service charge records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getServiceCharges(){
        $respond = (object)[];

        try {
            $serviceCharges = ServiceCharge::all();
            $respond->data    = $serviceCharges;
            $respond->message = 'ServiceCharge records found';
        } catch(Exception $ex) {
            $respond->data    = false;
            $respond->message = 'Problem occured while trying to get service charge records';
        }

        return $respond;
    }

    /**
     * Get specific service charge record from database.
     * @param Integer $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getServiceCharge($id){
        $respond = (object)[];

        try {
            $serviceCharge = ServiceCharge::findOrFail($id);
            $respond->data    = $serviceCharge;
            $respond->message = 'ServiceCharge record found';
        } catch(Exception $ex) {
            $respond->data    = false;
            $respond->message = 'ServiceCharge record not found!';
        }

        return $respond;
    }

    /**
     * Check valid string that contain only alphanumeric and space.
     * @param String value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidString($value){
        $respond = (object)[];
        $value = preg_replace("/[\s$@_*]+/", " ", $value);
        
        if ( !preg_match("/^([a-z0-9A-Z ])+$/i", $value) ) {
            $respond->data = false;
            $respond->message = "Value invalid, name accept only alphanumeric with whitespace!";
        } else {
            $respond->data = $value;
            $respond->message = 'value valid';
        }

        return $respond;
    }

    /**
     * Check valid string that contain only alphanumeric.
     * @param String value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidCode($value){
        $respond = (object)[];
        $value = preg_replace("/[\s$@_*]+/", "", $value);
        
        if ( !preg_match("/^([a-z0-9A-Z])+$/i", $value) ) {
            $respond->data = false;
            $respond->message = "Value invalid, accept only alphanumeric without whitespace!";
        } else {
            $respond->data = $value;
            $respond->message = 'value valid';
        }

        return $respond;
    }

    /**
     * Check valid price type and value that should always greater than -1.
     * @param Integer value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidPrice($value){
        $respond = (object)[];
        
        if(!is_numeric($value)){
            $respond->data = false;
            $respond->message = "Price is not a number!";
            return $respond;
        }
        if($value<-1){
            $respond->data = false;
            $respond->message = "Price value should not be negative!";
            return $respond;
        }
        
        $respond->data = $value;
        $respond->message = 'value valid';
        return $respond;
    }

    /**
     * Check valid file upload format, extension, and type, return file result with encoded
     * file name follow structure of (code + create date) object respond. 
     * @param Reuqest $request
     * @param Form_Request_Value $fileClassName
     * @param App/Models/ServiceCharge $code
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidFile($request, $fileClassName, $code){
        $respond = (object)[];

        if($request->hasFile($fileClassName)){
            // generate file name
            $currentTimestamp = Carbon::now()->timestamp;
            $serviceFile = $request->file($fileClassName);
            $serviceFileName = $currentTimestamp . $code . '.' .$serviceFile->getClientOriginalExtension();
            
            // generate file path
            $serviceFilePath = 'docs/servicecharges/'.$serviceFileName;
                        
            // encoded and store into local disk
            $serviceFileEncoded = File::get($request[$fileClassName]);
            Storage::disk('public')->put($serviceFilePath, $serviceFileEncoded);

            $respond->data = $serviceFilePath;
            $respond->message = 'File successfully store into local disk!';
        } else {
            $respond->data = false;
            $respond->message = 'No file found!';
        }

        return $respond;
    }

    /**
     * Delete file from local storage by provided parameters.
     * @param String $filePath
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function deleteFileStorage($filePath){
        $respond = (object)[];

        // delete file from disk
        $result = Storage::disk('public')->delete($filePath);

        if(!$result) {  // false
            $respond->data = $result;
            $respond->message = $filePath.' file not found, enable to process the delete!';
        } else {        // true
            $respond->data = $result;
            $respond->message = 'File successfully deleted from local storage';
        }
        return $respond;
    }

    /**
     * Validation request data.
     * @param Form_Request_Value $name
     * @param Form_Request_Value $code
     * @param Form_Request_Value $price
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkRequestValidation($name, $code, $price){
        $respond = (object)[];

        // check name
        $nameResult = ServiceCharge::checkValidString($name);
        if(!$nameResult->data){
            $respond = $nameResult;
            return $respond;
        }

        // check code
        $codeResult = ServiceCharge::checkValidCode($code);
        if(!$codeResult->data){
            $respond->data = false;
            $respond->message = 'Value invalid, code accept only alphanumeric without whitespace!';
            return $respond;
        }

        // check price
        $priceResult = ServiceCharge::checkValidPrice($price);
        if(!$priceResult->data){
            $respond = $priceResult;
            return $respond;
        }

        $respond->data    = true;
        $respond->price   = $priceResult->data;
        $respond->message = 'All request validated!';
        $respond->name    = strtolower($nameResult->data);
        $respond->code    = strtolower($codeResult->data);

        return $respond;
    }

     /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * Many service charges to many service groups (bridge table)
     * @return App\Model\ServiceGroup
     */
    public function serviceGroups(){
        return $this->belongsToMany(
            ServiceGroup::class,
            'service_charge_groups_bridge',
            'service_charge_id',
            'service_group_id',
        );
    }

    /**
     * Many service charges to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
