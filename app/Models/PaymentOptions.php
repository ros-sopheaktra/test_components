<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PaymentOptions extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'payment_options';

    /**
     * Primary key.
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'note',
        'method',
        'order_id',
        'attachment',
        'payment_amount',
        'reference_number',

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

    /**
     * Sales PAYMENT OPTIONS
     * @var Array
     */
    public const PAYMENTOPTIONS = [
        1 => 'bank transfer',
        2 => 'cash',
        3 => 'cheque',
        4 => 'credit card',

    ];

    // Payment Options Helper Functions [BEGIN]
        /**
         * Get all payment option records from database.
         * @return ObjectRespond [ data: result_data, message: result_message ]
         */
        public static function getPaymentOptions(){
            $respond = (object)[];

            try {
                $paymentOptions   = PaymentOptions::all();
                $respond->data    = $paymentOptions;
                $respond->message = 'All payment option records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get payment option records from database!';
            }

            return $respond;
        }
        /**
         * Get spefic payment option record based on given id from database.
         * @param Integer $id
         * @return ObjectRespond [ data: result_data, message: result_message ]
         */
        public static function getPaymentOption($id){
            $respond = (object)[];

            try {
                $paymentOption    = PaymentOptions::findOrFail($id);
                $respond->data    = $paymentOption;
                $respond->message = 'Payment option record found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Payment option record not found!';
            }

            return $respond;
        }

    // Payment Options Helper Functions [END]

    // Sale Helper Functions [BEGIN]
        /**
         * Get all sale records from database
         * @return ObjectRespond [ data: data_result, message: message_result ]
         */
        public static function getSales(){
            $respond = (object)[];

            try {
                $sales = Sales::all();
                $respond->data    = $sales;
                $respond->message = 'All sale records found';
            } catch(Exception $ex) {
                $respond->data = false;
                $respond->message = 'Problem occured while trying to get sale records from database!';
            }

            return $respond;
        }
        /**
         * Get specific sale record based on given id from database.
         * @param Integer $id
         * @return ObjectRespond [ data: data_result, message: message_result ]
         */
        public static function getOrder($id){
            $respond = (object)[];

            try {
                $order = Order::findOrFail($id);
                $respond->data    = $order;
                $respond->message = 'Order record found';
            } catch(ModelNotFoundException $ex) {
                $respond->data = false;
                $respond->message = 'Order record not found!';
            }

            return $respond;
        }
    // Order Helper Functions [END]

    /**
     * Check valid payment reference number.
     * @param String $referenceNumber
     * @return ObjectRespond [ data: data_result, message: message_result ] 
     */
    public static function checkValidReferenceNumber($referenceNumber){
        $respond = (object)[];

        if( !preg_match("/^([a-zA-Z0-9-])+$/i", $referenceNumber) ){
            $respond->data    = false;
            $respond->message = 'Payment reference number value invalided, only alphanumeric and - symbol is allow!';
        } else {
            $respond->data    = $referenceNumber;
            $respond->message = 'Payment reference number value valid';
        }

        return $respond;
    }
    
    /**
     * Returns the id of payment option
     * @param String $paymentOption
     * @return Int paymentStatusId
     */
    public static function getPaymentOptionsId($paymentOption){
        return array_search($paymentOption, self::PAYMENTOPTIONS);
    }

    /**
     * Check valid payment value by compare total amount value with 
     * addition of new submitted payment with payment made data on database.
     * @param String $saleTotalPayment
     * @param String $saleTotalPaymentMade
     * @param String $newSubmittedPaymentMade
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidPaymentMadeValue($saleTotalPayment, $saleTotalPaymentMade, $newSubmittedPaymentMade){
        $respond = (object)[];

        $newTotalPaymentAmount = number_format( ($saleTotalPaymentMade + $newSubmittedPaymentMade) , 2 , '.', '' );
        
        // validate total amount of payment greater than total amount
        // if($newTotalPaymentAmount > $saleTotalPayment){
        //     $respond->data    = false;
        //     $respond->message = 'Can not update payment due to submitted payment amount value greater than total amount!';
        //     return $respond;
        // }

        // validate if new submit payment made is equal 0 or smaller than 0
        if($newSubmittedPaymentMade <= 0) {
            $respond->data    = false;
            $respond->message = 'Can not update payment due to submitted payment amount value smaller than 0!';
            return $respond;
        }

        // validate total amount of payment smaller than 0
        if($newTotalPaymentAmount < 0) {
            $respond->data    = false;
            $respond->message = 'Can not update payment due to submitted payment amount value smaller than 0!';
            return $respond;
        }

        $respond->data    = $newTotalPaymentAmount;
        $respond->message = 'Payment amount valided';
        return $respond;
    }

    /**
     * Check valid file upload format, extension, and type, return file result with encoded
     * file name follow structure of (code + create date) object respond. 
     * @param  Reuqest $request
     * @param  Form_Request_Value $fileClassName
     * @param  App/Models/PaymentOptions $code
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidFile($request, $fileClassName, $code){
        $respond = (object)[];

        if($request->hasFile($fileClassName)){
            // generate file name
            $currentTimestamp = Carbon::now()->timestamp;
            $paymentFileAttachment = $request->file($fileClassName);
            $paymentFileName = $currentTimestamp . $code . '.' .$paymentFileAttachment->getClientOriginalExtension();
            
            // generate file path
            $paymentFilePath = 'docs/paymentattachments/'.$paymentFileName;
                        
            // encoded and store into local disk
            $paymentFileEncoded = File::get($request[$fileClassName]);
            Storage::disk('public')->put($paymentFilePath, $paymentFileEncoded);

            $respond->data = $paymentFilePath;
            $respond->message = 'Payment attachment successfully store into local disk!';
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
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * Many payment options to one oreder
     * @return App/Model/Order
     */
    public function order(){
        return $this->belongsTo(
            Order::class,
            'order_id',
        );
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
     * ##################################
     *      Fast Validation Functions
     * ##################################
     */

    /**
     * validation request data.
     * @param String $paymentReferenceNumber
     * @param String $paymentOption
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    protected static function checkReuqestValidation($paymentReferenceNumber, $paymentOption){
        $respond = (object)[];

        // validate payment reference number if value != null
        if($paymentReferenceNumber != null) {
            $paymentReferenceNumberResult = PaymentOptions::checkValidReferenceNumber($paymentReferenceNumber);
            if(!$paymentReferenceNumberResult->data){
                return $paymentReferenceNumberResult;
            }
        } else { $paymentReferenceNumberResult = (object) [ 'data' => null, ]; }

        // validate payment option method
        if( !PaymentOptions::getPaymentOptionsId($paymentOption) ){
            $respond->data    = false;
            $respond->message = 'Payment method invalid or incorrect provided!';
            return $respond;
        }

        $respond->data                   = true;
        $respond->paymentMethod          = strtolower($paymentOption);
        $respond->message                = 'All request values valided';
        $respond->paymentReferenceNumber = $paymentReferenceNumberResult->data != null ? strtolower($paymentReferenceNumberResult->data) : null;
        return $respond;
    }
}
