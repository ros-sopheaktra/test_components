<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Shop extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'shops';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attribute that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'name',
        'address',
        'logo',
        
    ];

    /**
     * ################################
     *     Modules Helper Functions
     * ################################
     */
        // Shop Helper Functions [BEGIN]
            /**
             * Get all shop records from database.
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getShops(){
                $respond = (object)[];
                
                try {
                    $shops = Shop::all();

                    $respond->data    = $shops;
                    $respond->message = 'Succesful getting all shop records from database';             
                } catch ( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occurred while trying to get shop records from database!'; 
                }

                return $respond;
            }

            /**
             * Get specific shop record based on given id parameter from database.
             * @param Integer $id
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getShop($id){
                $respond = (object)[];

                try {
                    $shop = Shop::findOrFail($id);

                    $respond->data    = $shop;
                    $respond->message = 'Shop record found';             
                } catch ( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Shop record not found!'; 
                }

                return $respond;
            }
            
        // Shop Helper Functions [END]

        // Product Variant Helper Functions [BEGIN]
            /**
             * Get collection of product variants based on given shop/warehouse eloquent data paramaeter.
             * @param App\Models\Shop $shop
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getProductVariantCollectionsByShop($shop){
                $respond = (object)[];

                // get product variant based on shop
                try {
                    $total = 0;
                    $productVariantCollections = new Collection();

                    foreach( $shop->product_variants as $productVariant ){
                        $quantities = DB::table('product_variant_shops')
                            ->where( 'shop_id', $shop->id )
                            ->where( 'product_variant_id', $productVariant->id )
                            ->pluck('quantity')
                            ->first();

                        $total += $quantities;

                        $productVariantCollections->push((object)[
                            'id'         => $productVariant->id,
                            'image'      => $productVariant->image,
                            'sku'        => $productVariant->sku,
                            'quantity'   => $quantities,
                        ]);
                    }

                    $respond->data    = $productVariantCollections;
                    $respond->total   =  $total;
                    $respond->message = 'Successful getting product variant collections based on shop id';             
                } catch ( Exception | Throwable $e ) {
                    $respond->data          = false;
                    $respond->detailMessage = $e->getMessage(); 
                    $respond->message       = 'Problem occured while trying to get product variant collections based on shop id!'; 
                }

                return $respond;
            }
        // Product Variant Helper Functions [END]

    /**
     * ########################
     *     Helper Functions
     * ########################
     */
        /**
         * Validate alphanumeric regex only.
         * #### (!)Alphanumeric means only alphabet and number (!)
         * @param String $value
         * @return Boolean
         */
        protected static function checkValidAllowOnlyNumberAndLetter($value){
            return !preg_match( '/^(?:[A-Za-z]+)(?:[A-Za-z0-9 _]*)$/', $value ) ? false : true;
        }

    /**
     * #######################
     *      Relationships
     * #######################
     */
        /**
         * Many shops to many products pivot table.
         * @return App\Models\Product
         */
        public function products(){
            return $this->belongsToMany(
                Product::class,
                'product_shops',
                'shop_id',
                'product_id',
            );
        }

        /**
         * Many shops to many product variants pivot table.
         * @return App\Models\ProductVariant
         */
        public function product_variants(){
            return $this->belongsToMany(
                ProductVariant::class,
                'product_variant_shops',
                'shop_id',
                'product_variant_id',
            )->withPivot(
                'quantity', 'location', 'remark'
            );
        }

        /**
         * Many shops to one adjustment relationship.
         * @return App\Models\Adjustment
         */
        public function adjustments(){
            return $this->hasMany(
                Adjustment::class,
            );
        }
        /**
         * Many shops to one epxense relationship.
         * @return App\Models\expens
         */
        public function expense(){
            return $this->hasMany(
                Expense::class,
            );
        }

        /**
         * Many shops to many users pivot table.
         * @return App\Models\User
         */
        public function users(){
            return $this->belongsToMany(
                User::class,
                'user_shops',
                'shop_id',
                'user_id',
            );
        }

        /**
         * One shop to many cash registers relationship.
         * @return App\Models\CashRegister
         */
        public function cashRegisters(){
            return $this->hasMany(
                CashRegister::class,
                'shop_id',
            );
        }

        /**
         * Many shops to many log histories (Polymorphic).
         * @return App\Models\LogHistory
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

    /**
     * ##################################
     *     Fast Validation Functions
     * ##################################
     */
        /**
         * Check validate request data submit from font-end.
         * @param \Illuminate\Http\Request $request
         * @return ObjectRespond [ data: result_data, messange:result_messange ]
         */
        protected static function checkValidateRequests($request){
            $respond = (object)[
                'data'    => true,
                'message' => 'All request value are valide',
            ];

            // validate empty name
            $name = $request['shop_name'];
            if ( strlen($name)  <= 0 ) {
                $respond->data    = false;
                $respond->message = 'Shop name can not be empty, please try again!';

                return $respond;
            }

            $respond->name = $name;

            return $respond;
        }
}
