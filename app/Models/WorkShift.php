<?php

namespace App\Models;

use Exception;
use Throwable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkShift extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'work_shifts';

    /**
     * Primary key
     * @var Integer
     */
    protected $primaryKey = 'id';

    /**
     * The attribute that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'user_id',
        'is_open',
        'shop_id',
        'opened_at',
        'type_of_shift',
        'total_sales_made',
        'cash_register_id',
    ];

    /**
     * Type Of Shift Enumeration.
     * @var Array
     */
    public static $typeOfShifts = [
        'morning_shift'   => 'Morning Shift',
        'afternoon_shift' => 'Afternoon Shift',
        'night_shift'     => 'Night Shift',
    ];

    /**
     * ##############################
     *    Module Helper Functions
     * ##############################
     */
        // Work Shift Helper Functions [BEGIN]
            /**
             * Get all work shift records from database.
             * @return ObjectRespond [data: data_result, message: reulst_message]
             */
            public static function getWorkShifts(){
                $respond = (object)[];

                try {
                    $workShifts = WorkShift::all();

                    foreach( $workShifts as $workShift ){
                        $workShift->openedBy    = $workShift->user_id != null ? ucwords( $workShift->user->username ) : 'Unknown User';
                        $workShift->openedAt    = $workShift->opened_at .' (' .$workShift->created_at->diffForHumans(). ')';
                        $workShift->typeOfShift = self::$typeOfShifts[ $workShift->type_of_shift ];
                    }

                    $respond->data    = $workShifts;
                    $respond->message = 'Successful getting all work shift records from database';
                } catch( Exception | Throwable $ex ) {
                    $respond->data          = false;
                    $respond->detailMessage = $ex->getMessage();
                    $respond->message       = 'Problem occured while trying to get work shift records from database!';
                }

                return $respond;
            }

            /**
             * Get specific work shift record 
             * based on given id parameter from database.
             * @param Integer $id
             * @return ObjectRespond [data: data_result, message: reulst_message]
             */
            public static function getWorkShift( $id ){
                $respond = (object)[];

                try {
                    $workShift = WorkShift::findOrFail($id);
                    $respond->data    = $workShift;
                    $respond->message = 'Work shift record found';
                } catch( ModelNotFoundException $ex) {
                    $respond->data          = false;
                    $respond->detailMessage = $ex->getMessage();
                    $respond->message       = 'Work shift record not found from database!';
                }

                return $respond;
            }
        // Work Shift Helper Functions [END]

        // User Helper Functions [BEGIN]
            /**
             * Get all user records from database.
             * @return ObjectRespond [data: result_data, message: result_message] 
             */
            public static function getUsers() {
                $respond = (object)[];

                try {
                    $users = User::all();
                    $respond->data    = $users;
                    $respond->message = 'Successful getting all user records from database';
                } catch( Throwable | Exception $ex ) {
                    $respond->data          = false;
                    $respond->detailMessage = $ex->getMessage();
                    $respond->message       = 'Problem occured while trying to get user records from database!';
                }

                return $respond;
            }

            /**
             * Get specific user record based 
             * on given id parameter from database.
             * @param Integer $id
             * @return RespondObject [data: result_data, message: result_message] 
             */
            public static function getUser( $id ){
                $respond = (object)[];

                try {
                    $user = User::findOrFail($id);
                    $respond->data    = $user;
                    $respond->message = 'User record found';
                } catch( ModelNotFoundException $ex ){
                    $respond->data    = false;
                    $respond->message = 'User record not found!';
                    $respond->detailMessage = $ex->getMessage();
                }

                return $respond;
            }
        // User Helper Functions [END]

        // Shop Helper Function [BEGIN]
            /**
             * Get all shop records from database.
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getShops(){
                $respond = (object)[];
                
                try {
                    $shops = Shop::all();
                    $respond->data    = $shops;
                    $respond->message = 'Successful getting all shop records from database';
                } catch(Exception | Throwable $ex) {
                    $respond->data          = false;
                    $respond->message       = 'Problem occurred while trying to get shop records from database!';
                    $respond->detailMessage = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Get all shop records from database based
             * on given user model from database.
             * @param App\Models\User $user
             * @return ObjectRespond
             */
            public static function getShopsBasedOnUser( $user ){
                $respond = (object)[];

                try {
                    if($user->username === 'super_admin'){
                        $shops = Shop::all();
                    }else{
                        $shops = $user->shops;
                    }
                    $respond->data    = $shops;
                    $respond->message = 'Successful getting all shops records from databse';
                } catch( ModelNotFoundException | Throwable $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get shop based on current user account!';
                    $respond->messageDetail = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Get specific shop record based 
             * on given id parameter from database.
             * @param Integer $id
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getShop( $id ){
                $respond = (object)[];

                try {
                    $shop = Shop::findOrFail($id);
                    $respond->data    = $shop;
                    $respond->message = 'Shop record found';             
                } catch(ModelNotFoundException $ex) {
                    $respond->data          = false;
                    $respond->message       = 'Shop record not found!';
                    $respond->detailMessage = $ex->getMessage(); 
                }

                return $respond;
            }
        // Shop Helper Function [END]

        // Cash Register Helper Functions [BEGIN]
            /**
             * Get all cash register records from database.
             * @return RespondObject [data: result_data, message: result_message] 
             */
            public static function getCashRegisters(){
                $respond = (object)[];

                try {
                    $cashRegisters = CashRegister::all()->sortBy('name');
                    $respond->data    = $cashRegisters;
                    $respond->message = 'Successful getting all cash register records from database';             
                } catch( Exception | Throwable $e ) {
                    $respond->data          = false;
                    $respond->detailMessage = $e->getMessage();
                    $respond->message       = 'Problem occurred while trying to get cash register records from database!'; 
                }

                return $respond;
            }

            /**
             * Get specific cash register record 
             * based on giving id paramter form database.
             * @param Integer $id
             * @return RespondObject [data: result_data, message: result_message]
             */
            public static function getCashRegister( $id ){
                $respond = (object)[];

                try {
                    $cashRegister = CashRegister::findOrFail( $id );
                    $respond->data    = $cashRegister;
                    $respond->message = 'Cash register record found';
                } catch( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Cash register record not found!';
                    $respond->detailMessage = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Load cash register records based on paginated
             * desired number passed by paramter from database. 
             * @param Integer $numberOfRecord
             * @return RespondObject [data: result_data, message: result_message] 
             */
            public static function loadCashRegister( $numberOfRecord ){
                $respond = (object)[];

                try {
                    $cashRegisters = CashRegister::paginate( $numberOfRecord );
                    foreach( $cashRegisters as $cashRegister ){
                        if($cashRegister->status === 'open'){
                            $cashRegister->actionStatusExtraAttributes = (object) [
                                'action_status'  => 'Close Register',
                                'status_route'   => 'closeregister',
                                'css_class_name' => 'cash-register-closed-status',
                            ];
                        } else {
                            $cashRegister->actionStatusExtraAttributes = (object) [
                                'action_status'  => 'Open Register',
                                'css_class_name' => 'cash-register-open-status',
                            ];
                        }
                    }
                    $respond->data    = $cashRegisters;
                    $respond->message = "Successful getting ${numberOfRecord} of cash register records from database";
                } catch( Exception | Throwable $e ) {
                    $respond->data          = false;
                    $respond->detailMessage = $e->getMessage(); 
                    $respond->message       = "Problem occured while trying to get ${numberOfRecord} of cash register records from database!";
                }

                return $respond;
            }
        // Cash Register Helper Functions [END]

    /**
     * ######################
     *    Helper Functions
     * ######################
     */

        /**
         * Validate all mandatory modules requests submitted by form.
         * @param Integer $shopId
         * @param String  $shiftType
         * @param Integer $cashRegister
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function modulesValidation( $shopId, $shiftType, $cashRegisterId ){
            $respond = (object)[];

            // get shop record
            $shop = self::getShop($shopId);
            if( !$shop->data ){
                return $shop;
            }
            $shop = $shop->data;

            // get shift type record
            if( !array_key_exists($shiftType, self::$typeOfShifts) ){
                $respond->data    = false;
                $respond->message = 'Shift type value invalided or does not exist!';
                return $respond;
            }

            // validate cash register record
            $cashRegister = self::getCashRegister( $cashRegisterId );
            if( !$cashRegister->data ){
                return $cashRegister;
            }
            $cashRegister = $cashRegister->data;
            // validate if current status is open
            if( strtolower( $cashRegister->status ) === 'closed' ){
                return [
                    'data'    => false,
                    'message' => 'Current selected cash register status is closed, please make sure to open new cash before access POS mode!',
                ];
            }

            $respond->data         = true;
            $respond->shop         = $shop;
            $respond->shiftType    = $shiftType;
            $respond->cashRegister = $cashRegister;
            $respond->message      = 'Successful getting all mandatory records from database';

            return $respond;
        }

    /**
     * ###################
     *    Relationships
     * ###################
     */

        /**
         * Many work shift to one PaymentOptionWorkShift.
         * @return App\Model\PaymentOptionWorkShift
         */
        public function workShiftPayments(){
            return $this->hasMany(
                PaymentOptionWorkShift::class
            );
        }

        /**
         * Many work shifts to many log histories (Polymorphic)
         * @return App\Model\LogHistory
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

        /**
         * Many work shifts to one users.
         * @return App\Model\User
         */
        public function user(){
            return $this->belongsTo(
                User::class,
                'user_id',
            );
        }

        /**
         * Many workshifts to one cash register.
         * @return App\Model\CashRegister
         */
        public function cashRegister(){
            return $this->belongsTo(
                CashRegister::class,
                'cash_register_id',
            );
        }
}
