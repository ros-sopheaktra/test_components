<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class StockCount extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'stock_counts';

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
        'created_by',
        'reference_no',
        'type',
        'isAlreadyAddAdjustment',
        'status',
        'staff_note',
        'shop_id',
      
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */
     // Stock Count Helper Function [BEGIN]
        /**
         * Get all stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getStockCounts(){
            $respond = (object)[];
        
            try {
                $stock_counts = StockCount::orderBy('id', 'DESC')->get();
                $respond->data = $stock_counts;
                $respond->message = 'Stock count records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getStockCount($id){
            $respond = (object)[];
        
            try {
                $stock_count = StockCount::findOrFail($id);
                $respond->data = $stock_count;
                $respond->message = 'Stock count records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }
    // Stock Count Function [END]

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

    // Category Helper Function [BEING]
        /**
         * Get all category records from database.
         * @return ObjectRespond 
        */
        public static function getCategories(){
            $respond = (object)[];
            
            try {
                $categories = Category::all();
                $respond->data    = $categories; 
                $respond->message = 'Category records found!'; 
            } catch(ModelNotFoundException $e) {
                $respond->data    = false; 
                $respond->message = 'Problem while tying to get category records!'; 
            };

            return $respond;
        }

        /**
         * Get specific category records from database.
         * @param Integer $id
         * @return ObjectRespond 
        */
        public static function getCategory($id){
        $respond = (object)[];
        
        try {
            $category = Category::findOrFail($id);
            $respond->data    = $category; 
            $respond->message = 'Category record found!'; 
        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Problem while tying to get category record!'; 
        };

        return $respond;
        }
    // Category Helper Function [END]

    // Brand Helper Function [BEING]
        /**
         * Get all product brand records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getProductBrands(){
            $respond = (object)[];

            try {
                $productBrands = Brand::all();
                $respond->data = $productBrands;
                $respond->message = 'Records found';
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = 'There is a problem while trying to get brands!';
            }

            return $respond;
        }

        /**
         * Get all product brand record from database.
         * @param Int $id
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getProductBrand($id){
            $respond = (object)[];

            try {
                $productBrand = Brand::findOrFail($id);
                $respond->data = $productBrand;
                $respond->message = 'Records found';
            } catch(ModelNotFoundException $e) {
                $respond->data = false;
                $respond->message = 'Brand record not found!';
            }

            return $respond;
        }
    // Brand Helper Function [END]

    // Product Variant Helper Function [BEING]
        /**
         * Get all product variant record from database based on condition user given.
         * @param Int $shop
         * @param String $type
         * @param Array $categories
         * @param Array $brands
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function bindStructureOfPv($shop, $type, $categories, $brands, $stockCountId){
            $products = $shop->products;
            if ($type != 'full' && ($categories != null || $brands != null)) {
                if ($categories != null && $brands != null) {
                    $products = $products->whereIn('category_id', $categories);
                    $products = $products->whereIn('brand_id', $brands);
                } elseif($categories != null) {
                    $products = $products->whereIn('category_id', $categories);
                }elseif($brands != null){
                    $products = $products->whereIn('brand_id', $brands);
                }
            }

            // push product variant into collection
            foreach($products as $product){
                foreach($product->product_variants as $product_variant){
                    $temporaryStockCount = new TemporaryStockCount([
                        'stock_count_id'     => $stockCountId,
                        'product_variant_id' => $product_variant->id,
                    ]);
                    $temporaryStockCount->save();
                }
            } 
        }
    // Product Variant Helper Function [END]

    /**
     * ########################
     *      Relationship
     * ########################
     */

     /**
     * Many to one relationship with temporary stock count
     * @return App/Model/TemporaryStockCount
     */
    public function temporaryStockCounts(){
        return $this->hasMany(TemporaryStockCount::class);
    }

     /**
     * Many to one relationship with warehouse
     * @return App/Model/Shop
     */
    public function shop(){
        return $this->belongsTo(Shop::class);
    }

     /**
     * Many to many relationship with category
     * @return App/Model/Category
     */
    public function categories(){
        return $this->belongsToMany(
            Category::class,
            'stock_counts_categories',
            'stock_count_id',
            'category_id'
        );
    }

     /**
     * Many to many relationship with brand
     * @return App/Model/Brand
     */
    public function brands(){
        return $this->belongsToMany(
            Brand::class,
            'stock_counts_brands',
            'stock_count_id',
            'brand_id'
        );
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