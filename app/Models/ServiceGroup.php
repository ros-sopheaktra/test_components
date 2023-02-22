<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceGroup extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'service_groups';

    /**
     * Primary key.
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attribute that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'name',
        'total_amount',
        'description',
        
    ];

    /**
     * ###########################
     *      Helper Functions
     * ###########################
     */

    // Service Group Helper Functions [BEGIN]
        /**
         * Get all service group records from database.
         * @return ObjectRespond [ data: data_result; message: message_result ]
         */
        public static function getServiceGroups(){
            $respond = (object)[];

            try {
                $serviceGroups = ServiceGroup::all();
                $respond->data    = $serviceGroups;
                $respond->message = 'All service group records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get service group records from database!';
            }

            return $respond;
        }

        /**
         * Get specific service group record based on given id
         * from database.
         * @param Integer $id
         * @return ObjectRespond [ data: data_result; message: message_result ]
         */
        public static function getServiceGroup($id){
            $respond = (object)[];

            try {
                $serviceGroup = ServiceGroup::findOrFail($id);
                $respond->data    = $serviceGroup;
                $respond->message = 'Service group record found';
            } catch(ModelNotFoundException $ex) {
                $respond->data    = false;
                $respond->message = 'Service group record not found!';
            }

            return $respond;
        }

    // Service Group Helper Functions [END]

    // Service Charge Helper Functions [BEGIN]
        /**
         * Get all service charge records from database.
         * @return ObjectRespond [ data: data_result; message: message_result ]
         */
        public static function getServiceCharges(){
            $respond = (object)[];

            try {
                $serviceCharges = ServiceCharge::all();
                $respond->data    = $serviceCharges;
                $respond->message = 'All service charge records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get service charge records from database!';
            }

            return $respond;
        }
        
        /**
         * Get specific service charge record based on given id
         * from database.
         * @param Integer $id
         * @return ObjectRespond [ data: data_result; message: message_result ]
         */
        public static function getServiceCharge($id){
            $respond = (object)[];

            try {
                $serviceCharge = ServiceCharge::findOrFail($id);
                $respond->data    = $serviceCharge;
                $respond->message = 'Service charge record found';
            } catch(ModelNotFoundException $ex) {
                $respond->data    = false;
                $respond->message = 'Service charge record not found!';
            }

            return $respond;
        }

        /**
         * Check valid array of service charge ids.
         * @param Array $serviceChargesIdArr
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getArrServiceCharges($serviceChargesIdArr){
            $respond = (object)[];
            $arrayLength = count($serviceChargesIdArr);

            $serviceCharges = ServiceCharge::find($serviceChargesIdArr);
            if($serviceCharges == null){
                $respond->data = false;
                $respond->message = 'Service charge ids is invalid, enable to get data!';
                return $respond;
            }

            if($arrayLength != count($serviceCharges)){
                $respond->data = false;
                $respond->message = 'One of the service charge id not found!';
                return $respond;
            }

            $respond->data = $serviceCharges;
            $respond->message = 'All service charge ids are found';

            return $respond;
        }

    // Service Charge Helper Functions [END]

    /**
     * Check valid name value.
     * @param String $name
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    protected static function checkValidString($name){
        if (!preg_match("/^[a-zA-Z0-9' ]*$/",$name)) {
            $respond = (object) [
                'data'    => false,
                'message' => 'String is invalid!',
            ];
            return  $respond;
        } else {
            $respond = (object) [
                'data'    => $name,
                'message' => 'String is valid!',
            ];
            return  $respond;
        }
    }

    /**
     * ###########################
     *      Relationships
     * ###########################
     */

    /**
     * One service group to many quotations
     * @return App\Model\Quotation
     */
    public function quotations(){
        return $this->hasMany(
            Quotation::class,
            'service_group_id',
        );
    }

    /**
     * One service group to many sales
     * @return App\Model\Sales
     */
    public function sales(){
        return $this->hasMany(
            Sales::class,
            'service_group_id',
        );
    }

    /**
     * Many service groups to many service charges (bridge table)
     * @return App\Model\ServiceCharge
     */
    public function serviceCharges(){
        return $this->belongsToMany(
            ServiceCharge::class,
            'service_charge_groups_bridge',
            'service_group_id',
            'service_charge_id',
        );
    }

    /**
     * Many service groups to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

    /**
     * ####################################
     *      Fast Validation Functions
     * ####################################
     */

    /**
     * validation request data.
     * @param Form_Request_Value $name
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkReuqestValidation( $name ){
        $respond = (object)[];

        // check name
        $nameResult = ServiceGroup::checkValidString($name);
        if(!$nameResult->data){
            $respond->data    = false;
            $respond->message = 'Service group name invalid! Only alphanumeric with whitespace are available!';
            return $respond;
        }

        $respond->data    = true;
        $respond->message = 'All request are validated';
        $respond->name    = strtolower($nameResult->data);
        return $respond;
    }

}
