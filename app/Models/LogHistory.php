<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role as ModelsRole;

class LogHistory extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'log_histories';

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
        'log_header',
        'permission_slug',
        'description',
        'user_id',
        'username'

    ];

    // #########################
    //     Helper Functions
    // #########################

        // Log History Helper Function [BEGIN]

            /**
             * Get all log history records from database.
             * @return ObjectRespond [ data: date_result, message: result_message ]
             */
            public static function getLogHistories(){
                $respond = (object)[];

                try {
                    $rootUser = User::where('firstname', 'root')->first();
                    if($rootUser == null){
                        $rootUser = new Collection();
                        $rootUser->id = 0;
                    }
                    $logHistories = LogHistory::orderBy('created_at', 'DESC')->where('user_id', '!=', $rootUser->id)->get();
                    $respond->data    = $logHistories->paginate(10);
                    $respond->message = 'All log history records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Get specific log history record by id or by log_header from database.`
             * @param String $attribute
             * @param String $queryBy [ id, header ]
             * 
             * @return ObjectRespond [ data: date_result, message: result_message ]
             */
            public static function getLogHistory( $attribute, $queryBy ){
                $respond = (object)[];

                try {

                    if ($queryBy == 'header'){
                        $logHistory = LogHistory::where('log_header', $attribute)->get();
                    } else {
                        $logHistory= LogHistory::findOrFail($attribute);
                    }

                    $respond->data    = $logHistory;
                    $respond->message = 'Log history record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Log history not records!';
                }

                return $respond;
            }

            /**
             * Get collections of log history records by given header permission collection of log_header.
             * @param Collection $headerPermissionsCollection
             * 
             * @return ObjectRespond [ data: date_result, message: result_message ]
             */
            public static function findArrOfLogHistoryLogHeader($headerPermissionsCollection){
                $respond = (object)[];

                try {
                    $logHistoryRecords = new Collection();

                    foreach($headerPermissionsCollection as $permissionLogHeader) {
                        $tempLogHistory = LogHistory::where('permission_slug', $permissionLogHeader)->get();

                        if( count($tempLogHistory) ) {
                            foreach( $tempLogHistory as $item ) {
                                $logHistoryRecords->push($item);
                            }
                        }
                    }

                    $respond->data    = $logHistoryRecords;
                    $respond->message = 'Log history record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Log history not records!';
                }

                return $respond;
            }

        // Log History Helper Function [END]

        // Get logHistory helper function[BEGIN]

            /**
             *  Get all loghistory base on pagination 
             * @param 10 pagination 
             * @param 100 need store in database 
             * 
             * @return responde
             */
            public static function getLogHistoryBaseOnPagination()
            { 
                $respond = (object)[];
            
                try {
                    $logHistories = LogHistory::getLogHistories();
                    $logHistories = $logHistories->data;
                    $totalRecords = $logHistories->total();
        
                    if ($totalRecords > 1000) {
                        $numberDeleteRecorde = $totalRecords - 1000;
                        $dataNeedToDelete = self::all()->take($numberDeleteRecorde);
                        LogHistory::destroy($dataNeedToDelete);
        
                    }
                    $respond = $logHistories;
                    $respond->message = 'All log history records found';
                } catch (Exception $ex) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;


            }
            
        // Get logHistory helper function[END]

    // ###################
    //    Relationship
    // ###################

        /**
         * Many log histories to one user
         * 
         * @return App/Model/User
         */
        public function user(){
            return $this->belongsTo(
                User::class,
                'user_id',
            );
        }

        /**
         * Many log histories to many bols relationship (Polymorphic)
         * 
         * @return App/Model/Bol
         */
        public function bols(){
            return $this->morphedByMany(
                Bol::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many sales relationship (Polymorphic)
         * @return App/Model/Sales
         */
        public function sales(){
            return $this->morphedByMany(
                Sales::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many quotation relationship (Polymorphic)
         * @return App/Model/Quotation
         */
        public function quotations(){
            return $this->morphedByMany(
                Quotation::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many products (Polymorphic)
         * @return App/Model/Product
         */
        public function products(){
            return $this->morphedByMany(
                Product::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many customers (Polymorphic)
         * @return App/Model/Customer
         */
        public function customers(){
            return $this->morphedByMany(
                Customer::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many suppliers (Polymorphic)
         * @return App/Model/Supplier
         */
        public function suppliers(){
            return $this->morphedByMany(
                Supplier::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many barcodes (Polymorphic)
         * @return App/Model/Barcode
         */
        public function barcodes(){
            return $this->morphedByMany(
                Barcode::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many barcode formats (Polymorphic)
         * @return App/Model/BarcodeFormat
         */
        public function barcodeFormats(){
            return $this->morphedByMany(
                BarcodeFormat::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many billers (Polymorphic)
         * @return App/Model/Biller
         */
        public function billers(){
            return $this->morphedByMany(
                Biller::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many customer groups (Polymorphic)
         * @return App/Model/CustomerGroup
         */
        public function customerGroups(){
            return $this->morphedByMany(
                CustomerGroup::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many categories (Polymorphic)
         * @return App/Model/Category
         */
        public function categories(){
            return $this->morphedByMany(
                Category::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many notifications (Polymorphic)
         * @return App/Model/Notification
         */
        public function notifications(){
            return $this->morphedByMany(
                Notification::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many product types (Polymorphic)
         * @return App/Model/ProductType
         */
        public function productTypes(){
            return $this->morphedByMany(
                ProductType::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many product units (Polymorphic)
         * @return App/Model/ProductUnit
         */
        public function productUnits(){
            return $this->morphedByMany(
                ProductUnit::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many product brands (Polymorphic)
         * @return App/Model/Brand
         */
        public function brands(){
            return $this->morphedByMany(
                Brand::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many service charges (Polymorphic)
         * @return App/Model/ServiceCharge
         */
        public function serviceCharges(){
            return $this->morphedByMany(
                ServiceCharge::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many service groups (Polymorphic)
         * @return App/Model/ServiceGroup
         */
        public function serviceGroups(){
            return $this->morphedByMany(
                ServiceGroup::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many roles (Polymorphic)
         * @return Spatie/Permission/Models/Role
         */
        public function roles(){
            return $this->morphedByMany(
                ModelsRole::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many users (Polymorphic)
         * @return App/Model/User
         */
        public function users(){
            return $this->morphedByMany(
                User::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many containers (Polymorphic)
         * @return App/Model/Container
         */
        public function containers(){
            return $this->morphedByMany(
                Container::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many carriers (Polymorphic)
         * @return App/Model/Carrier
         */
        public function carriers(){
            return $this->morphedByMany(
                Carrier::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many shippers (Polymorphic)
         * @return App/Model/Shipper
         */
        public function shippers(){
            return $this->morphedByMany(
                Shipper::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many color (Polymorphic)
         * @return App/Model/Color
         */
        public function colors(){
            return $this->morphedByMany(
                Color::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many size (Polymorphic)
         * @return App/Model/Size
         */
        public function sizes(){
            return $this->morphedByMany(
                Size::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many adjustment (Polymorphic)
         * @return App/Model/Adjustment
         */
        public function adjustments(){
            return $this->morphedByMany(
                Adjustment::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many shop (Polymorphic)
         * @return App/Model/Shop
         */
        public function shops(){
            return $this->morphedByMany(
                Shop::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many product variants (Polymorphic)
         * @return App/Model/ProductVariant
         */
        public function product_variants(){
            return $this->morphedByMany(
                ProductVariant::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many workshifts (Polymorphic)
         * @return App/Model/WorkShift
         */
        public function workShifts(){
            return $this->morphedByMany(
                WorkShift::class,
                'historyables',
            );
        }

        /**
         * Many log histories to many cash registers (Polymorphic)
         * @return App/Model/CashRegister
         */
        public function cashRegisters(){
            return $this->morphedByMany(
                CashRegister::class,
                'historyables',
            );
        }
    }
