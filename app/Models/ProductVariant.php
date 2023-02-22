<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\CustomImageSliderFunctions\CustomImageSliderFunctions as Image;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'product_variants';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * Attribute that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'sku',
        'slug',
        'image',
        'cost',
        'price',
        'serial_number',
        'product_name',
        'detail_for_invioce',
        'pv_detail',
        'barcode',
        'alert_quantity',
        'barcode_id',
        'product_id',
        'size_id',
        'color_id',

    ];

    /**
     * ###############################
     *    Modules Helper Functions
     * ###############################
     */
        // Product Helper Function [BEGIN]
            /**
             * Get all products recorde based on array of 
             * product id parameter from database.
             * @param Array $productIdsArr
             * @return ResponObject [ data: result_data, messange: result_messange ]
             */
            public static function getProductsFromIdsArr($productIdsArr){
                $respond = (object)[];
            
                $arrLength = count($productIdsArr);
                if( $arrLength <= 0 ){
                    $respond->data    = false;
                    $respond->message = 'Unable to search product varaint due to empty products provided!';
                
                    return $respond;
                }

                $count = 1;
                $productsCollection = new Collection();
                foreach( $productIdsArr as $productId ){
                    try{
                        $product = Product::findOrFail($productId);
                        
                        $productVariantsCollection = new Collection();
                        foreach( $product->product_variants as $productVariant ){
                            $productVariant->number_id = $count++;
                            $productVariantsCollection->push($productVariant);
                        }
                        $product->productVariants = $productVariantsCollection;

                        $product->categoryData    = $product->category;
                        $product->subCategoryData = $product->subcategory_id != null ? $product->subcategory : null;
                        $product->unitData        = $product->productUnit;
                        $product->brandData       = $product->brand;
                        $product->imageData       = $productVariant->image;
                        
                        $productsCollection->push($product);
                    } catch ( ModelNotFoundException $e ) {
                        $respond->data    = false;
                        $respond->message = 'One of product id is invalid or does not exist on database, unable to get product records!';        
                    }
                }
                $respond->data    = $productsCollection;
                $respond->message = 'Successful getting all product records from database';

                return $respond;
            }
        // Product Helper Function [END]

        // Product Variant Helper Functions [BEGIN]
            /**
            * Get all product variant records form database.
            * @return ResponObject [ data: result_data, messange: result_messange ]
            */
            protected static function getProductVariants(){
                $respond = (object)[];
        
                try{
                    $productVariants = ProductVariant::orderBy('id', 'DESC')->get();

                    $respond->data     = $productVariants;
                    $respond->messange = 'Successful getting all product variant records from database.';
                } catch ( Exception $e ) {
                    $respond->data     = false;
                    $respond->messange = 'Problem occured while trying to get product variant records from database!';
                }

                return $respond;
            }
            
            /**
             * Get specific product variant record 
             * based on given id form database.
             * @param Integer $id
             * @return ResponObject [ data: result_data, messange: result_messange ]
             */
            protected static function getProductVariant($id){
                $respond = (object)[];
        
                try{
                    $productVariant = ProductVariant::findOrFail($id);

                    $respond->data     = $productVariant;
                    $respond->messange = 'Product variant record found';
                } catch ( ModelNotFoundException $e ) {
                    $respond->data     = false;
                    $respond->messange = 'Product variant record not found!';
                }

                return $respond;
            }

            /**
             * Get product variant in the form of collection 
             * based on given shop id parameter from database.
             * @param Integer $shopId
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProductVariantBasedOnAssociatedShopRecord($shopId){
                $respond = (object)[];

                // get shop record
                $shop = Shop::getShop($shopId);
                if( !$shop->data ){
                    return $shop;
                }
                $shop = $shop->data;

                try {
                    $pvCollections = new Collection();
                    foreach( $shop->product_variants as $productVariant ){
                        $productVariantQuantity = DB::table('product_variant_shops')
                                                    ->where('product_variant_id', $productVariant->id)
                                                    ->where('shop_id', $shopId)
                                                    ->first();
                        
                        $pvCollections->push( (object)[
                            'id'            => $productVariant->id,
                            'name'          => $productVariant->serial_number.' '.$productVariant->product_name,
                            'price'         => $productVariant->price,
                            'cost'          => $productVariant->cost,
                            'quantity'      => $productVariantQuantity->quantity,
                            'serial_number' => $productVariant->serial_number,
                            'brand'         => $productVariant->product->brand == null ? 'No brand' : $productVariant->product->brand->name,
                            'unit'          => $productVariant->product->productUnit == null ? 'No Unit' : $productVariant->product->productUnit->title,
                        ]);
                    }

                    $respond->data    = $pvCollections;
                    $respond->message = 'All product records found';
                } catch ( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Get product variant based on slug value from database.
             * @param String $slug
             * @return RespondObject [ data: data_result, message: result_message ]
             */
            protected static function getProductVariantBasedOnSlug($slug){
                $respond = (object)[];

                try {
                    $productVariant = self::query()
                                        ->where('slug', 'LIKE', "$slug") 
                                        ->get()
                                        ->first();

                    $respond->productId        = $productVariant;
                    $respond->productVariantId = null;
                    if( $productVariant != null ){
                        $respond->productId        = $productVariant->product->id;
                        $respond->productVariantId = $productVariant->id;
                    }

                    $respond->data    = $productVariant;
                    $respond->message = 'Succesfull getting product variant based on given slug which is [' . $slug . ']';
                } catch ( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variant based on slug value!';
                }

                return $respond;
            }

            /**
             * Delete product variant record from database.
             * @param  \Illuminate\Http\Request  $request
             * @return ResponObject [ data: result_data, messange: result_messange ]
             */
            protected static function destroyProductVariant(Request $request){    
                // delete record    
                try{
                    DB::beginTransaction();

                    $productVariant = ProductVariant::findOrFail( $request->get('pvId') );
                    if( $productVariant->product->checkbox_one_image == 'false' ){
                        Image::deleteImage( $productVariant->image );
                    }
                    $productVariant->delete();
                } catch ( ModelNotFoundException $e ) {
                    DB::rollBack();

                    return (object)[
                        'data'          => false,
                        'detailMessage' => $e->getMessage(),
                        'messange'      => 'Product variant record not found, unable to delete product variant!',
                    ];
                }
                DB::commit();

                return (object)[
                    'data'     => true,
                    'messange' => 'Product variant delete successfully...!',
                ];
            }

            /**
             * Validate duplicated possibility of given field from 
             * databased with given field name and value provided by parameters.
             * @param String $mode      [ the mode options]
             * @param String $fieldName [ the filed or attributes in database ]
             * @param String $value     [ the value to validate with ]
             * 
             * @return RespondObject [ data: result_data, message: result_message ]
             */
            public static function checkingProductVariantFile( $mode, $fieldName, $value ){
                $respond = (object)[
                    'data'         => true,
                    'isDuplicated' => false,
                    'message'      => $mode . ' can be used.',
                ];

                try {
                    $isDuplicated = ProductVariant::where( $fieldName, '=', $value );

                    // validat duplicate
                    if( $isDuplicated->count() > 0 ) {
                        $respond->isDuplicated = true;
                        $respond->message      = $mode . ' already exist, can not be used.';
                    }
                } catch ( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to find product variant ' . $mode . '!';
                }

                return $respond;
            }
        // Product Variant Helper Functions [END]

    /**
     * #######################
     *    Helper Functions
     * #######################
     */
        /**
         * Get size of product based on selected 
         * color submit from font-end as color id.
         * @param  \Illuminate\Http\Request  $request
         * @param Integer $colorId
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSizeOfColor( Request $request, $colorId ){
            $respond = (object)[];

            try {
                $productVariants = ProductVariant::query()
                                    ->where('color_id', $colorId)
                                    ->where('product_id', $request->get('pro_id'))
                                    ->get();

                $sizes = new Collection();
                foreach( $productVariants as $pv ){
                    $tmpSize = (object)[
                        'id'      => $pv->size->id,
                        'name'    => $pv->size->name,
                        'price'   => $pv->price,
                        'image'   => $pv->image,
                        'pv_name' => $pv->product_name,
                        'pv_des'  => $pv->pv_detail,
                        'pv_id'   => $pv->id,
                    ];
                    $sizes->push($tmpSize);
                }

                $respond->data    = $sizes->unique('id'); 
                $respond->message = 'Records found!'; 
            } catch ( ModelNotFoundException $e ) {
                $respond->data    = false; 
                $respond->message = 'Problem while tying to get records!'; 
            };

            return $respond;
        }

    /**
     * ########################
     *      Relationship
     * ########################
     */
        /**
         * Many product variants to one size relationship.
         * @return App\Models\Size
         */
        public function size(){
            return $this->belongsTo(
                Size::class,
                'size_id',
            );
        }

        /**
         * Many to product variants to one color relationship.
         * @return App\Models\Color
         */
        public function color(){
            return $this->belongsTo(
                Color::class,
                'color_id',
            );
        }

        /**
         * Many product variants to one product relationship.
         * @return App\Models\Product
         */
        public function product(){
            return $this->belongsTo(
                Product::class,
                'product_id',
            );
        }

        /**
         * Many product variants to many adjustments relationship.
         * @return App\Models\Adjustment
         */
        public function adjustments()
        {
            return $this->belongsToMany(
                Adjustment::class, 
                'product_variant_adjustments',
                'product_variant_id',
                'adjustment_id',
            )->withPivot(
                'quantity', 
                'type',
            );
        }

        /**
         * Many product variants to many shops relationship.
         * @return App\Models\Shop
         */
        public function shops(){
            return $this->belongsToMany(
                Shop::class,
                'product_variant_shops',
                'product_variant_id',
                'shop_id',
            )->withPivot(
                'quantity', 
                'location', 
                'remark',
            );
        }

        /**
         * Many colors to many log histories (Polymorphic) relationship.
         * @return App\Models\LogHistory
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

        /**
         * One product variant to many best selling products relationship.
         * @return App\Models\BestSellingProduct
         */
        public function bestSellingProducts(){
            return $this->hasMany(
                BestSellingProduct::class,
                'product_variant_id',
            );
        }

        /**
         * One product variant to one barcode relationship.
         * @return App\Models\Barcode
         */
        public function barcode(){
            return $this->belongsTo(
                Barcode::class,
                'barcode_id',
            );
        }

        /**
         * Many product variants to many quotations privot table.
         * @return App\Models\Quotation
         */
        public function quotations(){
            return $this->belongsToMany(
                Quotation::class,
                'quotations_product_variants_bridge',
                'product_variant_id',
                'quotation_id',
            );
        }
}