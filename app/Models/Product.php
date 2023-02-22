<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use ourcodeworld\NameThatColor\ColorInterpreter;
use Exception;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{ 
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'products';

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
        'name',
        'quantity',
        'checkbox_one_image',
        'slug',
        'alert_quantity',
        'gallery',
        'hide_in_pos',
        'hide_in_web',
        'product_point',
        'supplier_product_name',
        'supplier_product_code',
        'supplier_product_remark',
        'detail_for_invioce',
        'detail',
        'supplier_id',
        'category_id',
        'barcode',
        'barcode_id',
        'brand_id',
        'product_unit_id',
        'subcategory_id',
        
    ];

    /**
     * #################################
     *       Modules Helper Functions
     * #################################
     */
        // Api Product Helper Functions [BEGIN]
             /**
             * Get product besed on user filter brand and cate
             * @param int $catId
             * @param int $brandId
             * @return RespondObject [ data: result_data, message: result_message ]
             */
            public static function getProductsFilters( $catId, $brandId ){
                $respond = (object)[];

                try {
                    // get product variant filter size and color
                    if( $catId != 0 && $brandId != 0 ){
                        $products = Product::where('category_id', $catId)
                                            ->where('brand_id', $brandId)
                                            ->where('hide_in_web', 'false')
                                            ->get()
                                            ->paginate(40);
                    }elseif( $catId != 0 ){
                        $products = Product::where('category_id', $catId)
                                            ->where('hide_in_web', 'false')
                                            ->get()
                                            ->paginate(40);
                    }elseif( $brandId != 0 ){
                        $products = Product::where('brand_id', $brandId)
                                            ->where('hide_in_web', 'false')
                                            ->get()
                                            ->paginate(40);
                    }
            
                    // get products
                    $product_shows = new Collection();
                    $online_store   = OnlineStock::findOrFail(1);
                    foreach( $products as $product ){
                        $much = DB::table('product_shops')
                                ->where('product_id', $product->id)
                                ->where('shop_id', $online_store->shop_id)->get();
                    
                        if(count($much) != 0 ){
                            $tmpproduct = (object)[
                                'id'          => $product->id,
                                'name'        => $product->name,
                                'gallery'     => $product->product_variants->first()->image,
                                'category_id' => $product->category_id,
                            ];
                            $product_shows->push($tmpproduct);
                        }
                    }

                    $respond->data = $product_shows;
                } catch ( QueryException $e ) {
                    $respond->data    = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }

            /**
             * Get product besed on user filter sub cate
             * @param int $id
             * @return RespondObject [ data: result_data, message: result_message ]
             */
            public static function getProductsFiltersBySubCategory( $id ){
                $respond = (object)[];

                try {
                    // get product variant filter sub cate
                    $products = Product::where('subcategory_id', $id)
                                        ->get()
                                        ->paginate(40);
            
                    // get products
                    $product_shows = new Collection();
                    $online_store   = OnlineStock::findOrFail(1);
                    foreach( $products as $product ){
                        $much = DB::table('product_shops')
                                ->where('product_id', $product->id)
                                ->where('shop_id', $online_store->shop_id)->get();
                    
                        if(count($much) != 0 ){
                            $tmpproduct = (object)[
                                'id'          => $product->id,
                                'name'        => $product->name,
                                'gallery'     => $product->product_variants->first()->image,
                                'category_id' => $product->category_id,
                            ];
                            $product_shows->push($tmpproduct);
                        }
                    }

                    $respond->data = $product_shows;
                } catch ( QueryException $e ) {
                    $respond->data    = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }
        // Api Product Helper Functions [END]

        // Product Helper Functions (BEGIN)
            /**
             * Get all product records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductsByBrand($brandId){
                $respond = (object)[];
                $products = new Collection();
                
                try {
                    $online_store = OnlineStock::findOrFail(1);
                    $product_gets = Product::where('brand_id', $brandId)->get();
                    foreach( $product_gets as $product ){
                        $much = DB::table('product_shops')
                                ->where('product_id', $product->id)
                                ->where('shop_id', $online_store->shop_id)
                                ->get();
                        if($product->hide_in_web != 'true' && count($much) != 0){
                            $products->push($product);
                        }
                    }
                    $respond->data    = $products;
                } catch(Exception $e) {
                    $respond->data    = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }

            /**
             * Get all product records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProducts(){
                $respond           = (object)[];
                $productColleption = new Collection();

                try {
                    $products = Product::all();
                    $count    = 1;
                    foreach( $products as $product ){
                        foreach( $product->product_variants as $product_variant ){
                            $product_variant->number_id = $count++;
                        }
                        $productColleption->push($product);
                    }

                    $respond->data    = $productColleption;
                    $respond->message = 'Product records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }

            /**
             * Get specific product record from database.
             * @param $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProduct($id){
                $respond     = (object)[];
                $nullGallery = new Collection(['', '']);
                
                try {
                    $product = Product::findOrFail($id);
                    if( count($product->product_variants) == 0 ){
                        $one_img = null;
                    }else{
                        $one_img = $product->product_variants->first()->image;
                    }
                    $respond->data      = $product;
                    $respond->one_image = $one_img;
                    $respond->gallery   = $product->gallery == null ? $nullGallery : json_decode($product->gallery);
                    $respond->message   = 'Product record found';
                } catch( ModelNotFoundException $e ){
                    $respond->data      = false;
                    $respond->message   = 'Product record not found';
                }

                return $respond;
            }

            /**
             * Get specific product record from database.
             * @param $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductDetail($id){
                $respond = (object)[];
                
                try {
                    $product = Product::findOrFail($id);
                    $product_shows = (object)[
                        'id'                 => $product->id,
                        'name'               => $product->name,
                        'first_gallery'      => $product->gallery == null ? null : json_decode($product->gallery)[0],
                        'second_gallery'     => $product->gallery == null ? null : json_decode($product->gallery)[1],
                        'brand'              => $product->brand->name,
                        'category'           => $product->category,
                        'sub_category'       => $product->subcategory == null ? 'No Subcategory' : $product->subcategory->name,
                        'unit'               => $product->productUnit->title,
                        'alert_quantity'     => $product->alert_quantity,
                        'product_variants'   => self::generateStructurePv($product->product_variants),
                    ];

                    $respond->data      = $product_shows;
                    $respond->message   = 'Product record found';
                } catch(ModelNotFoundException $e) {
                    $respond->data      = false;
                    $respond->message   = 'Product record not found';
                }
                return $respond;
            }

            /**
             * Generate structure of product varaints
             * @param Object $product_variants
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function generateStructurePv($product_variants){
                $productVariantColleption = new Collection();

                foreach( $product_variants as $product_variant ){
                    $pvs = (object)[
                        'id'                => $product_variant->id,
                        'sku'               => $product_variant->sku,
                        'serial_number'     => $product_variant->serial_number,
                        'pv_name'           => $product_variant->product_name,
                        'cost'              => $product_variant->cost,
                        'price'             => $product_variant->price,
                        'size'              => $product_variant->size->name,
                        'color'             => self::getNameOfColor($product_variant->color->hex_code),
                        'slug'              => $product_variant->slug,
                        'image'             => $product_variant->image,
                        'pv_detail'         => $product_variant->pv_detail,
                        'pv_detail_invoice' => $product_variant->detail_for_invioce,
                        'barcode'           => $product_variant->barcode,
                        'shops'             => self::generateStructureShopOfPv($product_variant->shops, $product_variant->id),
                    ];
                    $productVariantColleption->push($pvs);
                }

                return $productVariantColleption;
            }

            /**
             * Generate structure shop of each product varaints  getting privot shops
             * @param Object $shop
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function generateStructureShopOfPv( $shops, $id ){
                $shopColleption = new Collection();
                $tempShopProductvariantPivot = DB::table('product_variant_shops')
                    ->where('product_variant_id', $id)
                    ->get();
                foreach( $shops as $key => $shop ){
                    $shop = (object)[
                        'id'       => $shop->id,
                        'name'     => $shop->name,
                        'quantity' => $tempShopProductvariantPivot[$key]->quantity,
                        'location' => $tempShopProductvariantPivot[$key]->location,

                    ];
                    $shopColleption->push($shop);
                }
                return $shopColleption;
            }

            /**
             * Get random recommend product
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getRandomRecommendProducts(){
                $respond = (object)[];
                
                try {
                    $products = Product::all();
                    if ( count($products) >= 4 ) {
                        $products = Product::all()->random(4);
                    } else {
                        $products = $products;
                    }

                    $respond->data      = $products;
                    $respond->message   = 'Product record found';
                } catch( ModelNotFoundException $e ){
                    $respond->data      = false;
                    $respond->message   = 'Product record not found';
                }

                return $respond;
            }
        // Product Helper Functions (END)

        // Sub Category Helper Functions (BEING)
            /**
             * Get all sub category records from database.
             * @return ObjectRespond 
             */
            public static function getSubCategories(){
                $respond = (object)[];
                
                try {
                    $subcategories    = SubCategory::all();
                    $respond->data    = $subcategories; 
                    $respond->message = 'Sub Category records found!'; 
                } catch ( ModelNotFoundException $e ) {
                    $respond->data    = false; 
                    $respond->message = 'Problem while tying to get sub category records!'; 
                };

                return $respond;
            }
        // Sub Category Helper Functions (ENG)

        // Product Type Helper Functions (BEGIN)
            /**
             * Get all product type records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductTypes(){
                $respond = (object)[];
                
                try {
                    $productTypes     = ProductType::all();
                    $respond->data    = $productTypes;
                    $respond->message = 'Product type records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product type records';
                }

                return $respond;
            }

            /**
             * Get specific product type record from database.
             * @param Integer $id
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductType($id){
                $respond = (object)[];
                
                try {
                    $productType = ProductType::findOrFail($id);
                    $respond->data    = $productType;
                    $respond->message = 'Product type record found';
                } catch( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Product type record not found';
                }

                return $respond;
            }
        // Product Type Helper Functions (END)

        // Barcode Helper Functions (BEGIN)
            /**
             * Get all barcode records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBarcodes(){
                $respond = (object)[];
                
                try {
                    $barcodes = Barcode::all();
                    $respond->data    = $barcodes;
                    $respond->message = 'Barcode records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get barcode records';
                }

                return $respond;
            }

            /**
             * Get specific barcode record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBarcode($id){
                $respond = (object)[];
                
                try {
                    $barcode = Barcode::findOrFail($id);
                    $respond->data    = $barcode;
                    $respond->message = 'Barcode record found';
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false;
                    $respond->message = 'Barcode record not found';
                }

                return $respond;
            }
        // Barcode Helper Functions (END)

        // Brand Helper Functions (BEGIN)
            /**
             * Get all brand records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBrands(){
                $respond = (object)[];
                
                try {
                    $brands = Brand::all();
                    $respond->data    = $brands;
                    $respond->message = 'Brand records found';
                } catch(Exception $e) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get brand records';
                }

                return $respond;
            }

            /**
             * Get specific brand record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBrand($id){
                $respond = (object)[];
                
                try {
                    $brand = Brand::findOrFail($id);
                    $respond->data    = $brand;
                    $respond->message = 'Brand record found';
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false;
                    $respond->message = 'Brand record not found';
                }

                return $respond;
            }
        // Brand Helper Functions (END)

        // Category Helper Functions (BEGIN)
            /**
             * Get all category records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getCategories(){
                $respond = (object)[];
                
                try {
                    $categories       = Category::all();
                    $respond->data    = $categories;
                    $respond->message = 'Category records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get category records';
                }

                return $respond;
            }

            /**
             * Get specific category record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getCategory($id){
                $respond     = (object)[];
                $productShow = new Collection();
                
                try {
                    $category     = Category::findOrFail($id);
                    $online_store = OnlineStock::findOrFail(1);
                    if ( $category->name == 'BY BRAND' ) {
                        $products = Product::all();
                    } else {
                        $products = $category->products;
                    }
                    
                    foreach( $products as $product ){
                        $much = DB::table('product_shops')
                        ->where('product_id', $product->id)
                        ->where('shop_id', $online_store->shop_id)
                        ->get();
                        if($product->hide_in_web != 'true' && count($much) != 0){
                            $productShow->push($product);
                        }
                    }
                    $respond->data = $category;
                    $respond->products = $productShow;
                    $respond->message = 'Category record found';
                } catch( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Category record not found';
                }

                return $respond;
            }
        // Category Helper Functions (END)

        // Shop Helper Functions (BEGIN)
            /**
             * Get all shop records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShops(){
                $respond = (object)[];
                
                try {
                    $shops = Shop::all();
                    $respond->data    = $shops;
                    $respond->message = 'Shop records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get shop records';
                }

                return $respond;
            }
        // Shop Helper Functions (END)

        // Product Unit Helper Functions (BEGIN)
            /**
             * Get all product unit records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductUnits(){
                $respond = (object)[];
                
                try {
                    $productUnits     = ProductUnit::all();
                    $respond->data    = $productUnits;
                    $respond->message = 'Product unit records found';
                } catch( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product unit records';
                }

                return $respond;
            }

            /**
             * Get specific product unit record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductUnit($id){
                $respond = (object)[];
                
                try {
                    $productUnit = ProductUnit::findOrFail($id);
                    $respond->data    = $productUnit;
                    $respond->message = 'Product unit record found';
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false;
                    $respond->message = 'Product unit record not found';
                }

                return $respond;
            }
        // Product Unit Helper Functions (END)

        // Color Helper Functions (BEGIN)
            /**
             * Get all color records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductColor($id){
                $respond = (object)[];
                $colors  = new Collection();
                
                try {
                    $product = Product::findOrFail($id);
                    foreach( $product->product_variants as $product_variant ){
                        $tmpColor = (object) [
                            'id'       => $product_variant->color->id,
                            'hex_code' => $product_variant->color->hex_code,
                            'name'     => self::getNameOfColor($product_variant->color->hex_code),
                        ]; 
                        $colors->push($tmpColor);
                    }
                    $respond->data    = $colors->unique('id');
                    $respond->message = 'Color records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Color occured while trying to get color records';
                }

                return $respond;
            }

            /**
             * Get the name of color based on given hex code
             * @return string color name
             */
            public static function getNameOfColor($hex_code){
                $instance = new ColorInterpreter();
                if($hex_code == 'One Color Only'){
                    $colorName = $hex_code;
                }else{
                    $color = $instance->name($hex_code);
                    $colorName = $color["name"];
                }
                return $colorName;
            }
        // Color Helper Functions (END)

        // Supplier Helper Functions (BEGIN)
            /**
             * Get all supplier records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getSuppliers(){
                $respond = (object)[];
                
                try {
                    $suppliers        = Supplier::all();
                    $respond->data    = $suppliers;
                    $respond->message = 'Supplier records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get supplier records';
                }
                return $respond;
            }

            /**
             * Get specific supplier record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getSupplier($id){
                $respond = (object)[];
                
                try {
                    $supplier         = Supplier::findOrFail($id);
                    $respond->data    = $supplier;
                    $respond->message = 'Supplier record found';
                } catch( ModelNotFoundException $e ){
                    $respond->data    = false;
                    $respond->message = 'Supplier record not found';
                }

                return $respond;
            }
        // Supplier Helper Functions (END)

        // check block helper functions(BEGIN)
            /**
             * Check valid string that contain alphanumeric, whitespace 
             * and special character like (-_#.) only.
             * @param String $value
             * 
             * @return ObjectRespond [ data: result_data, message: result_message ]
             */
            public static function checkValidNameSpecificSymbolOnly($value){
                $respond = (object)[];

                if( !preg_match("/^([a-zA-Z0-9-_#. ])+$/i", $value) ){
                    $respond->data    = false;
                    $respond->message = 'Value invalid, only alphanumeric, whitespace and (#._-) are allow';
                } else {
                    $respond->data    = $value;
                    $respond->message = 'Value valid';
                }

                return $respond;
            }

            /**
             * Check valid string that contain alphanumeric only without whitespace.
             * @param String $value
             * 
             * @return ObjectRespond [ data: result_data, message: result_message ]
             */
            public static function checkValidAlphanumericOnly($value){
                $respond = (object)[];

                if( !preg_match("/^([a-zA-Z0-9])+$/i", $value) ){
                    $respond->data    = false;
                    $respond->message = 'Value invalid, only alphanumeric are allow';
                } else {
                    $respond->data    = $value;
                    $respond->message = 'Value valid';
                }

                return $respond;
            }

            /**
             * Check valid string that contain alphanumeric with (-) only without whitespace.
             * @param String $value
             * 
             * @return ObjectRespond [ data: result_data, message: result_message ]
             */
            public static function checkValidStringnDashOnly($value){
                $respond = (object)[];

                if( !preg_match("/^([a-zA-Z0-9-])+$/i", $value) ){
                    $respond->data    = false;
                    $respond->message = 'Value invalid, only alphanumeric and (-) are allow';
                } else {
                    $respond->data    = $value;
                    $respond->message = 'Value valid';
                }

                return $respond;
            }

            /**
             * Clear string encoding.
             * @param String $value
             * 
             * @return String $value
             */
            public static function clearEncodingStr($value)
            {
                if ( is_array($value) ){
                    $clean = [];
                    foreach( $value as $key => $val ){
                        $clean[$key] = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
                    }
                    return $clean;
                }

                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        // check block helper function(END)
        
    /**
     * #################################
     *       Helper Functions
     * #################################
     */
        /**
         * Get mandatory modules records for product creation. 
         * @return RespondObject [ data: data_result, message: result_message ]
         */
        public static function getMandatoryModulesForProductCreation(){
            $respond = (object)[];
            
            // get all size record from databese
            $sizes = Size::getSizes();
            if( !$sizes->data ){
                return back()->with('error', $sizes->message);
            }
            $sizes = $sizes->data;
    
            // get all color from db
            $colors = Color::getColors();
            if( !$colors->data ){
                return back()->with('error', $colors->message);
            }
            $colors = $colors->data;

            // get all color from db
            $categories = Category::getCategoriesByBrands();
            if( !$categories->data ){
                return back()->with('error', $categories->message);
            }
            $categories = $categories->data;

            // get sub category from db
            $subCategories = self::getSubCategories();
            if( !$subCategories->data ){
                return back()->with('error', $subCategories->message);
            }
            $subCategories = $subCategories->data;

            // get product type records
            $productTypes  = self::getProductTypes();
            if( !$productTypes->data ){
                return view('dashboard/index')
                    ->with('error', $productTypes->message.' while trying create product!');
            }
            $productTypes = $productTypes->data;

            // get brand records
            $brands = self::getBrands();
            if( !$brands->data ){
                return view('dashboard/index')
                    ->with('error', $brands->message.' while trying create product!');
            }
            $brands = $brands->data;      

            // get product unit records
            $productUnits  = self::getProductUnits();
            if( !$productUnits->data ){
                return view('dashboard/index')
                    ->with('error', $productUnits->message. 'while trying create product!');
            }
            $productUnits = $productUnits->data;

            // get shop records
            $shops = self::getShops();
            if( !$shops->data ){
                return view('dashboard/index')
                    ->with('error', $shops->message. 'while trying create product!');
            }
            $shops = $shops->data;

            $respond->data          = true;
            $respond->shops         = $shops;
            $respond->sizes         = json_encode($sizes);
            $respond->colors        = json_encode($colors);
            $respond->brands        = $brands;
            $respond->categories    = $categories ;
            $respond->subCategories = $subCategories;
            $respond->productTypes  = $productTypes;
            $respond->productUnits  = $productUnits;
            $respond->message       = 'Successful getting all mandatory module records from database for product creation';

            return $respond;
        }

        /**
         * Edit product 
         *  @param $id 
         * 
         *  @return RespondObject
         */
        public static function getMandatoryModulesForProductEdit( $id, $currentPagination ) {
            $respond = (object)[];

            // get record
            $get_product = self::getProduct($id);
            if( !$get_product->data ){
                return back()->with('error', $get_product->message);
            }

            $product = $get_product->data;
            $galleries = $get_product->gallery;

            // get all size record from databese
            $sizes = Size::getSizes();
            if( !$sizes->data ){
                return back()->with('error', $sizes->message);
            }
            $sizes = $sizes->data;
            
            $colors = Color::getColors();
            // get all color from db
            if( !$colors->data ){
                return back()->with('error', $colors->message);
            }
            $colors = $colors->data;
            $categories = Category::getCategoriesByBrands();

            // get all color from db
            if( !$categories->data ){
                return back()->with('error', $categories->message);
            }
            $categories = $categories->data;

            // get sub category from db
            $subCategories = self::getSubCategories();
            if( !$subCategories->data ){
                return back()->with('error', $subCategories->message);
            }
            $subCategories = $subCategories->data;

            // get product type records
            $productTypes = self::getProductTypes();
            if( !$productTypes->data ){
                return view('dashboard/index')
                    ->with('error', $productTypes->message.' while trying create product!');
            }
            $productTypes = $productTypes->data;

            // get brand records
            $brands = self::getBrands();
            if( !$brands->data ){
                return view('dashboard/index')
                    ->with('error', $brands->message.' while trying create product!');
            }
            $brands = $brands->data;      

            // get product unit records
            $productUnits = self::getProductUnits();
            if( !$productUnits->data ){
                return view('dashboard/index')
                    ->with('error', $productUnits->message. 'while trying create product!');
            }
            $productUnits = $productUnits->data;

            // get shop records
            $shops = self::getShops();
            if( !$shops->data ){
                return view('dashboard/index')
                    ->with('error', $shops->message. 'while trying create product!');
            }
            $shops = $shops->data;

            // get records
            $barcodeFormats = BarcodeFormat::getBarcodeFormats();
            if( !$barcodeFormats->data ){
                return back()->with('error', $barcodeFormats->message);
            };
            $barcodeFormats = $barcodeFormats->data;

            $respond->sizes          = $sizes;
            $respond->colors         = $colors;
            $respond->categories     = $categories ;
            $respond->subCategories  = $subCategories;
            $respond->productTypes   = $productTypes;
            $respond->brands         = $brands;
            $respond->productUnits   = $productUnits;
            $respond->shops          = $shops;
            $respond->product        = $product;
            $respond->galleries      = $galleries;
            $respond->barcodeFormats = $barcodeFormats;
            $respond->data           = true;
            $respond->message        = 'Successful getting all mandatory module records from database for product creation';

            return $respond;
        }

        /**
         * Use to upload Csv file 
         * @param Mixed $resquest
         * 
         * @return Array 
         */
        public static function getMandatoryModulesForCsvUpload($request){
            $path    = $request->file('import-csv-product')->getRealPath();
            $records = array_map('str_getcsv', file($path));

            // validate empty uploaded records
            if( !count($records) ){
                return back()->with('error', 'Trying to import csv product of empty record, process denied!');
            }

            // Get field names from header column
            $fields = array_map('strtolower', $records[0]);
            
            // Remove the header column
            array_shift($records);

            $rows = [];
            foreach ( $records as $record ){
                if ( count($fields) != count($record) ) {
                    return back()->with('error', 'csv_upload_invalid_data');
                }

                // Decode unwanted html entities
                $record =  array_map("html_entity_decode", $record);

                // Set the field name as key
                $record = array_combine($fields, $record);

                // Get the clean data
                $rows[] = Product::clearEncodingStr($record);
            }
            return $rows;
        }

    /**
     * #################################
     *       Relationship
     * #################################
     */
        /**
         * Many products to one category.
         * @return App\Models\Category
         */
        public function category(){
            return $this->belongsTo(
                Category::class,
                'category_id',
            );
        }

        /**
         * Many products to one category. 
         * @return App\Models\Category
         */
        public function subcategory(){
            return $this->belongsTo(
                SubCategory::class
            );
        }

        /**
         * One product to one barcode.
         * @return App\Model\Barcode
         */
        public function barcode(){
            return $this->belongsTo(
                Barcode::class,
                'barcode_id',
            );
        }

        /**
         * Many products to one brand.
         * @return App\Model\Brand
         */
        public function brand(){
            return $this->belongsTo(
                Brand::class,
                'brand_id',
            );
        }

        /**
         * Many products to one product unit.
         * @return App\Model\ProductUnit
         */
        public function productUnit(){
            return $this->belongsTo(
                ProductUnit::class,
                'product_unit_id',
            );
        }

        /**
         * Many products to one product type.
         * @return App\Model\ProductType
         */
        public function productType(){
            return $this->belongsTo(
                ProductType::class,
                'product_type_id',
            );
        }

        /**
         * Many products to one supplier.
         * @return App\Model\Supplier
         */
        public function supplier(){
            return $this->belongsTo(
                Supplier::class,
                'supplier_id',
            );
        }

        /**
         * Many products to many containers pivot table.
         * @return App\Model\Container
         */
        public function containers(){
            return $this->belongsToMany(
                Container::class,
                'products_containers_bridge',
                'product_id',
                'container_id',
            );
        }

        /**
         * Many products to many sales pivot table.
         * @return App\Model\Sale
         */
        public function sales(){
            return $this->belongsToMany(
                Sale::class,
                'sales_products_bridge',
                'product_id',
                'sale_id',
            );
        }

        /**
         * Many products to many quotation pivot table.
         * @return App\Model\Quotation
         */
        public function quotations(){
            return $this->belongsToMany(
                Quotation::class,
                'quotations_products_bridge',
                'product_id',
                'quotation_id',
            );
        }

        /**
         * Many products to many shop pivot table.
         * @return App\Model\Shop
         */
        public function shops(){
            return $this->belongsToMany(
                Shop::class,
                'product_shops',
                'product_id',
                'shop_id',
            );
        }

        /**
         * Many products variant to one with product.
         * @return App\Model\ProductVariant
         */
        public function product_variants(){
            return $this->hasMany(ProductVariant::class);
        }

        /**
         * Many products to many log histories relationship (Polymorphic) 
         * @return App\Model\LogHistory
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

    /**
     * #################################
     *       Fast Validation Functions
     * #################################
     */
        /**
         * Valida request data.
         * @param Form_Request_Value $name
         * @param Form_Request_Value $productCode
         * @param Form_Request_Value $hsCode
         * @param Form_Request_Value $slug
         * @param Form_Request_Value $weight
         * @param Form_Request_Value $price
         * @param Form_Request_Value $cost
         * @param Form_Request_Value $supplierPartNumber
         * 
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function checkRequestValidation(
            $name, $productCode, $hsCode, 
            $slug, $weight, $price, 
            $cost, $supplierPartNumber)
        {
            $respond = (object)[];

            // valid product name
            $nameResult = self::checkValidNameSpecificSymbolOnly($name);
            if( !$nameResult->data ) {
                $respond->data    = $nameResult->data;
                $respond->message = $nameResult->message . ' on product name!';
                return $respond;
            }

            // valid product code
            $productCodeResult = self::checkValidAlphanumericOnly($productCode);
            if( !$productCodeResult->data ) {
                $respond->data    = $productCodeResult->data;
                $respond->message = $productCodeResult->message . ' on product code!';
                return $respond;
            }

            // valid hs code
            $hsCodeResult = self::checkValidAlphanumericOnly($hsCode);
            if( !$hsCodeResult->data ) {
                $respond->data    = $hsCodeResult->data;
                $respond->message = $hsCodeResult->message . ' on HS Code!';
                return $respond;
            }

            // valid product slug
            $slugResult = self::checkValidStringnDashOnly($slug);
            if( !$slugResult->data ){
                $respond->data    = $slugResult->data;
                $respond->message = $slugResult->message . ' on product slug!';
                return $respond;
            }

            // valid weight
            if( $weight < -1 ){
                $respond->data    = false;
                $respond->message = 'Product weight value must be positive value!';
                return $respond;
            }

            // valid price
            if( $price < -1 ){
                $respond->data    = false;
                $respond->message = 'Product price value must be positive value!';
                return $respond;
            }

            // valid cost
            if( $cost < -1 ){
                $respond->data    = false;
                $respond->message = 'Product cost value must be positive value!';
                return $respond;
            }

            // valid supplier part number
            $supplierPartNumberResult = self::checkValidStringnDashOnly($supplierPartNumber);
            if( !$supplierPartNumberResult->data ){
                $respond->data = $supplierPartNumberResult->data;
                $respond->message = $supplierPartNumberResult->message . ' on supplier part number!';
                return $respond;
            }

            $respond->data                = true;
            $respond->cost                = $cost;
            $respond->price               = $price;
            $respond->weight              = $weight;
            $respond->slug                = strtolower($slugResult->data);
            $respond->name                = strtolower($nameResult->data);
            $respond->hsCode              = strtolower($hsCodeResult->data);
            $respond->productCode         = strtolower($productCodeResult->data);
            $respond->supplierPartNumber  = strtolower($supplierPartNumberResult->data);
            $respond->message             = 'All request data are valid.';

            return $respond;
        }

        /**
         * Validat all model records from database base on given model ids.
         * @param Integer $productTypeId
         * @param Integer $barCodeId
         * @param Integer $brandId
         * @param Integer $categoryId
         * @param Integer $productUnitId
         * @param Integer $supplierId
         * 
         * @return ObjectRespond [ data:result_data, message: result_message ]
         */
        public static function checkValidModelRecords(
            $productTypeId, $barcodeId, $brandId, 
            $categoryId, $productUnitId, $supplierId)
        {
            $respond = (object)[];

            // get product type record
            $productTypeResult = Product::getProductType($productTypeId);
            if( !$productTypeResult->data ){
                $respond = $productTypeResult;
                return $respond;
            }

            // get bracode record
            $barcodeResult = Product::getBarcode($barcodeId);
            if( !$barcodeResult->data ){
                $respond = $barcodeResult;
                return $respond;
            }

            // get brand record
            $brandResult = Product::getBrand($brandId);
            if( !$brandResult->data ){
                $respond = $brandResult;
                return $respond;
            }

            // get category record
            $categoryResult = Product::getCategory($categoryId);
            if( !$categoryResult->data ){
                $respond = $categoryResult;
                return $respond;
            }

            // get product unit record
            $productUnitResult = Product::getProductUnit($productUnitId);
            if( !$productUnitResult->data ){
                $respond = $productUnitResult;
                return $respond;
            }

            // get supplier record
            $supplierResult = Product::getSupplier($supplierId);
            if( !$supplierResult->data ){
                $respond = $supplierResult;
                return $respond;
            }

            $respond->data          = true;
            $respond->brand         = $brandResult->data;
            $respond->barcode       = $barcodeResult->data;
            $respond->category      = $categoryResult->data;
            $respond->supplier      = $supplierResult->data;
            $respond->productUnit   = $productUnitResult->data;
            $respond->productType   = $productTypeResult->data;
            $respond->message       = 'All model record in database are found!';

            return $respond;
        }
}
