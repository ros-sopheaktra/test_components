<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'stock_transfers';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'date',
        'reference_no',
        'delivery_fee',
        'transfer_status',
        'attach_document',
        'from_warehouse_name',
        'from_warehouse_id',
        'to_warehouse_name',
        'to_warehouse_id',
        'delivery_note',
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */

    // Stock Transfer Helper Function [BEGIN]
        /**
         * Get all stock transfer based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getStockTransfers(){
            $respond = (object)[];
        
            try {
                $stock_transfers = StockTransfer::orderBy('id', 'DESC')->get();
                $respond->data = $stock_transfers;
                $respond->message = 'Stock transfer records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find stock transfer based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getStockTransfer($id){
            $respond = (object)[];
        
            try {
                $stock_transfer = StockTransfer::findOrFail($id);
                $respond->data = $stock_transfer;
                $respond->message = 'Stock transfer records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

    // Stock Transfer Function [END]

    // Shop Helper Function [BEGIN]

        /**
         * Get all shop records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getShops(){
            $respond = (object)[];
            
            try {
                $shops = Shop::all();
                $respond->data = $shops;
                $respond->message = 'Shop records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get shop records!'; 
            }

            return $respond;
        }

        /**
         * Get specific shop records from database based on ginven id.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getShop($id){
            $respond = (object)[];

            try {
                $shop = Shop::findOrFail($id);
                $respond->data    = $shop;
                $respond->message = 'Shop records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get shop records!'; 
            }

            return $respond;
        }
    // Shop Helper Function [END]

    // Product Variant Helper Functions [BEGIN]
        /**
        * Get all product variant records form database.
        * @return ResponObject [ data: result_data, messange: result_messange ]
        */
        protected static function getProductVariants(){
            $respond = (object)[];
    
            try{
                $productVariants   = ProductVariant::orderBy('id', 'DESC')->get();
                $respond->data     = $productVariants;
                $respond->messange = 'Product variants found';
            }catch(Exception $e) {
                $respond->data     = false;
                $respond->messange = 'Problem occured while trying to get product variants from database!';
            }

            return $respond;
        }
    // Product Variant Helper Functions [END]
            

    /**
     * ########################
     *      Relationship
     * ########################
     */

     /**
     * Many to many relation with product variants.
     * @return App/Model/ProductVariant
     */
    public function product_variants(){
        return $this->belongsToMany(
            ProductVariant::class, 
            'stock_transfer_productvariants',
            'stock_transfer_id',
            'product_variant_id'
        )
        ->withPivot('quantity');
    }

    /**
     * Many size to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
