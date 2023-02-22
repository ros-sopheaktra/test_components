<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Throwable;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CashRegister extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'cash_registers';

    /**
     * Primary key.
     * @var Integer 
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'name',
        'status',
        'description',
        'shop_id',
        'daily_total',
        'cash_in_hand',
        'latest_username',
        'shop_name',
    ];

    /**
     * ####################
     *    Enum Variables
     * ####################
     */
        /**
         * Cash Register STATUS
         * @var Array
         */
        public const STATUS = [
            'open'   => 'Open',
            'closed' => 'Closed',
        ];

    /**
     * ##############################
     *    Modules Helper Functions
     * ##############################
     */
        // Cash Register Helper Functions [BEGIN]
            /**
             * Get all cash register records from database.
             * @return RespondObject [data: result_data, message: result_message] 
             */
            public static function getCashRegisters(){
                $respond = (object)[];

                try {
                    $cashRegisters = CashRegister::all();
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
             * based on given id parameter from database.
             * @param Integer $id
             * @return RespondObject [data: result_data, message: result_message] 
             */
            public static function getCashRegister( $id ){
                $respond = (object)[];

                try {
                    $cashRegister = CashRegister::findOrFail( $id );
                    $respond->data    = $cashRegister;
                    $respond->message = 'Cash register record found';             
                } catch( ModelNotFoundException $e ) {
                    $respond->data          = false;
                    $respond->detailMessage = $e->getMessage(); 
                    $respond->message       = 'Cash register record not found!'; 
                }

                return $respond;
            }

            /**
             * Close cash register in db when it expired in 1 day
             * @return RespondObject [data: result_data, message: result_message] 
             */
            public static function closeCashRegisters(){
                $respond = (object)[];

                try {
                    $dateNow = Carbon::now()->format('d/m/y');
                    $cashRegisters = CashRegister::all();
                    foreach($cashRegisters as $cashRegister){
                        $createdAt = $cashRegister->created_at->format('d/m/y');
                        if($createdAt != $dateNow && $cashRegister->status != 'permanently closed'){
                            $cashRegister->status = 'permanently closed';
                            $cashRegister->update();
                        }
                    }
                    $respond->message = 'successful';             
                } catch( ModelNotFoundException $e ) {
                    $respond->data          = false;
                    $respond->message       = $e->getMessage(); 
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
                    $cashRegisters = CashRegister::orderBy('created_at', 'desc')->paginate( $numberOfRecord );
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

            /**
             * Load cash register records based on given collection of shops 
             * model data and desired number of record passed as parameter.
             * @param Array|Object|Collection $shops
             * @param Integer $numberOfRecord
             * 
             * @return ObjectRespond [data: result_data, message: result_message]
             */
            public static function loadCashRegisterByShops( $shops, $numberOfRecord ){
                $respond = (object)[];

                // validate empty shops
                if( !count($shops) || count($shops) <= 0 ){
                    $respond->data    = false;
                    $respond->message = 'Can not get cash register record on en empty shop, please refresh the page and try again!';

                    return $respond;
                }

                // getting cash registers based on shop
                $cashRegisterCollections = new Collection();
                try {
                    $shops->filter( fn($shop) => 
                        $shop->cashRegisters->filter( fn($cashRegister) => 
                            $cashRegisterCollections->push($cashRegister) 
                        )
                    );

                    $cashRegisters = $cashRegisterCollections->sortByDesc('created_at')->paginate( $numberOfRecord );
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
                } catch ( Exception | Throwable $ex ) {
                    $respond->data          = false;
                    $respond->detailMessage = $ex->getMessage();
                    $respond->message       = "Problem occured while trying to get ${numberOfRecord} of cash register records from database!";
                }

                return $respond;
            }

            /**
             * Group daily total into one way data based on given 
             * workshifts collection parameters and return in the form of array.
             * @param Collection $workshifts
             * @return Array $temp
             */
            public static function groupDailyTotalByPaymentMethod( $workshifts ){
                $temp = array();
                // dd($workshifts);
                foreach( $workshifts as $workshift ){
                    foreach( $workshift->workShiftPaymentMethods as $paymentMethod => $currencyField ){      
                        if( array_key_exists( $paymentMethod, $temp ) ){ // update record
                            $temp[$paymentMethod]->riel   += $currencyField->riel;
                            $temp[$paymentMethod]->dollar += $currencyField->dollar;
                        } else { // insert record
                            $temp[$paymentMethod] = (object)[
                                'riel'   => $currencyField->riel,
                                'dollar' => $currencyField->dollar,
                            ];
                        }
                    }
                }

                return $temp;
            }
        // Cash Register Helper Functions [END]

        // Work Shift Helper Functions [BEGIN]
            /**
             * Get all work shift records from database.
             * @return ObjectRespond [data: data_result, message: reulst_message]
             */
            public static function getWorkShifts(){
                $respond = (object)[];

                try {
                    $workShifts = WorkShift::all();
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

        // Shop Helper Function [BEGIN]
            /**
             * Get all shop records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShops(){
                $respond = (object)[];
                
                try {
                    $shops = Shop::all();
                    $respond->data    = $shops;
                    $respond->message = 'Succesfull getting all shop records from database.';             
                } catch ( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Problem occurred while trying to get all shop records from database!'; 
                }

                return $respond;
            }

            /**
             * Get specific shop record based on 
             * given id parameter from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShop( $id ){
                $respond = (object)[];

                try {
                    $shop = Shop::findOrFail( $id );
                    $respond->data    = $shop;
                    $respond->message = 'Shop record found';             
                } catch ( ModelNotFoundException | Throwable $e ){
                    $respond->data    = false;
                    $respond->message = 'Shop record not found!'; 
                }

                return $respond;
            }

            /**
             * Get shop records based on logged-in system user permissions.
             * @param App\Models\Users $systemUser
             * @return ObjectRespond [ data: data_result, mesage: result_message ]
             */
            public static function getShopBasedOnSystemUser( $systemUser ){
                $respond = (object)[];

                try {
                    $shops = $systemUser->shops;
                    $respond->data    = $shops;
                    $respond->message = 'Successful getting shop records based on system user';
                } catch( ModelNotFoundException | Throwable | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get shop records based on current login user!';
                }

                return $respond;
            }
        // Shop Helper Function [END]

    /**
     * #####################
     *     Relationships 
     * #####################
     */
        /**
         * Many cash registers to many log histories (Polymorphic)
         * @return App\Model\LogHistory 
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

        /**
         * One cash register to many workshifts.
         * @return App\Model\WorkShift
         */
        public function workShifts(){
            return $this->hasMany(
                WorkShift::class,
                'cash_register_id',
            );
        }

        /**
         * Many cash registers to one shop.
         * @return App\Models\Shop
         */
        public function shop(){
            return $this->belongsTo(
                Shop::class,
                'shop_id',
            );
        }

        /**
         * Many to many with user.
         * @return App\Model\CashRegisterHistories
         */
        public function cash_register_histories(){
            return $this->hasMany(CashRegisterHistories::class);
        }
}
