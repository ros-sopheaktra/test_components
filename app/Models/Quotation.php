<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Exception;
use Throwable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Quotation extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String 
     */
    protected $table = 'quotations';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';
    
    /**
     * Attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'paid', 
        'total',
        'status',
        'pick_up',
        'user_id',
        'shop_id',
        'staff_note',
        'grand_total',
        'deadline_at',
        'delivery_fee',
        'attached_file',
        'quotation_note',
        'reference_number',
        'customer_discount',
        'customer_website_id',

    ];

    /**
     * Quotation STATUS
     * @var Array
     */
    public const QUOTATIONSSTATUS = [
        1 => 'pending',
        2 => 'confirmed',
        3 => 'completed',
        4 => 'cancel',

    ];

    /**
     * ##################################
     *      Modules Helper Functions
     * ##################################
     */
        // Quotation Helper Functions [BEGIN]
            /**
             * Get all quotation records from database.
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getQuotations(){
                $respond = (object)[];

                try {
                    $quotations = Quotation::all();

                    $respond->data    = $quotations;
                    $respond->message = 'Successful getting all quotation records from database.';   
                } catch ( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get quotation records from database!';
                }

                return $respond;
            }

            /**
             * Get specific quotation record based on 
             * given id parameter from database.
             * @param Integer $id
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getQuotation($id){
                $respond = (object)[];

                try {
                    $quotation = Quotation::findOrFail($id);

                    $respond->data    = $quotation;
                    $respond->message = 'Quotation record found.';   
                } catch ( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Quotaion recored not found!';
                }
                
                return $respond;
            }

            /**
             * Get quotation from database and generate extra required 
             * datas and attributes in order to generate into pdf file.
             * @param Integer $id
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getQuotationWithCustomDataForPDF($id){
                $respond = (object)[
                    'data'    => true,
                    'message' => 'Success generate extra attributes for quotation pdf',
                ];

                // get quotation record
                $quotation = self::getQuotation($id);
                if( !$quotation->data ){
                    return $quotation;
                }
                $quotation = $quotation->data;
                
                // get default user record and username
                $onlineUser = DefaultUserDownloadPdf::getDefaultUserSetting(1);
                if( !$onlineUser->data ){
                    return $onlineUser;
                }
                $onlineUser = $onlineUser->data;
                $userName   = $onlineUser->user->firstname .' '. $onlineUser->user->lastname;

                $respond->userName = $userName;
                $respond->data     = $quotation;
                $respond->message  = 'Succesful getting record data with extra structure for pdf download';

                return $respond;
            }
        // Quotation Helper Functions [END]

        // Shop Helper Functions [BEGIN]
            /**
             * Get all shop records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShops(){
                $respond = (object)[];
                
                try {
                    $user  = auth()->user();
                    $shops = $user->shops; 

                    $respond->data    = $shops;
                    $respond->message = 'Successfull getting all shop records from database';             
                } catch ( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occurred while trying to get shop records from database!'; 
                }

                return $respond;
            }

            /**
             * Get specific shop record based 
             * on given id parameter from database.
             * @param Integer $id
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShop( $id ){
                $respond = (object)[];

                try {
                    $shop = Shop::findOrFail($id);

                    $respond->data    = $shop;
                    $respond->message = 'Shop record found.';             
                } catch ( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Ship record not found!'; 
                }

                return $respond;
            }
        // Shop Helper Functions [END]

        // Website Customer Helper Functions [BEGIN]
            /**
             * Get all website customer records from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getCustomers(){
                $respond = (object)[];

                try {
                    $websiteCustomers = CustomerWebsite::all();

                    $respond->data    = $websiteCustomers;
                    $respond->message = 'Successful getting website customer records from database';
                } catch ( Exception | Throwable $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get website customer records from database!';
                }

                return $respond;
            }

            /**
             * Get specific website customer record 
             * based on given id parameter from database.
             * @param Integer $id
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getCustomer($id){
                $respond = (object)[];

                try {
                    $websiteCustomer = CustomerWebsite::findOrFail($id);

                    $respond->data    = $websiteCustomer;
                    $respond->message = 'Website customer record found';
                } catch ( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Website customer record not found!';
                }

                return $respond;
            }
        // Website Customer Helper Functions [END]

        // Customer Group Helper Functions [BEGIN]
            /**
             * Get all customer group records from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getCustomerGroups(){
                $respond = (object)[];

                try {
                    $customerGroups = CustomerGroup::all();

                    $respond->data    = $customerGroups;
                    $respond->message = 'Succesful getting all customer group records from database';
                } catch ( Exception | Throwable $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get customer group records from database!';
                }

                return $respond;
            }
        // Customer Group Helper Functions [END]

        // Product Variant Helper Functions [BEGIN]
            /**
             * Get all product variant records from database.
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProductVariants(){
                $respond = (object)[];
            
                try {
                    $productvariants = ProductVariant::all();

                    $respond->data    = $productvariants;
                    $respond->message = 'Successful getting all product variant records from database';
                } catch ( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variant records from database!';
                }

                return $respond;
            }

            /**
             * Get product variant records from database 
             * based on given product variant ids parameter.
             * @param Array $productVariantIdArr
             * @return ObjectRespond [ data: data_result; message: message_result ]
             */
            public static function getProductVariantsArr( $productVariantIdArr ) {
                $respond = (object)[];

                $arrLength = count( $productVariantIdArr );
                if( $arrLength <= 0 ){
                    $respond->data    = false;
                    $respond->message = 'Unable to generate order receipt due to empty product variant provided!';

                    return $respond;
                }

                $productVariants = new Collection();
                foreach( $productVariantIdArr as $productVariantId ){
                    try {
                        $tmpProductVariant = ProductVariant::findOrFail( $productVariantId );
                        $productVariants->push( $tmpProductVariant );

                    } catch ( ModelNotFoundException $ex ) {
                        $respond->data    = false;
                        $respond->message = 'One of product variant ids does not exit, unable to generate order receipt, please double check and try again!';
                    }
                }
                $respond->data    = $productVariants;
                $respond->message = 'All product variant records found';

                return $respond;
            }

            /**
             * Get quantity of product variant from specific shop
             * based on given module product variant and shop id parameters.
             * @param App/Model/ProductVariant $productVariant
             * @param Integer $shopId
             */
            public static function getProductVariantShopQuantities( $productVariant, $shopId )
            {    
                $respond = (object)[];
                
                try {
                    $productVariants = DB::table('product_variant_shops')
                                        ->where('product_variant_id', $productVariant->id)
                                        ->where('shop_id', $shopId)
                                        ->get()->first();

                    $respond->data     = $productVariants;
                    $respond->quantity = $productVariants->quantity;
                    $respond->message  = 'Succesfull getting product variant quantity on specific shop id from database';
                } catch ( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variant quantity on specific shop id from database!';
                }

                return $respond;
            }

            /**
             * Get corresponding product variant records of given 
             * quotation module parameter and convert it into JSON format.
             * @param App\Models\Quotation
             * @return ObjectRespond [ data: data_result , message: result_message ]
             */
            public static function getJsonProductVariantsOfQuotation($quotation){
                $respond = (object)[];
                $shopId  = $quotation->shop_id;

                $productVariants = array();

                try {
                    foreach( $quotation->productVariants as $productVariant ){
                        $productVariantShop = DB::table('product_variant_shops')
                                                ->where('product_variant_id', $productVariant->id)
                                                ->where('shop_id', $shopId)
                                                ->first();
    
                        array_push( $productVariants, (object)[
                            'id'             => $productVariant->id,
                            'name'           => $productVariant->serial_number.' '.$productVariant->product_name,
                            'cost'           => $productVariant->cost,
                            'price'          => $productVariant->price,
                            'discount'       => $productVariant->pivot->discount,
                            'sub_total'      => number_format( ( $productVariant->price ) * ( $productVariant->pivot->quantity ), 2 ),
                            'quantity'       => $productVariant->pivot->quantity,
                            'quantity_store' => $productVariantShop->quantity,
                        ] );
                    }

                    $respond->data    = json_encode($productVariants);
                    $respond->message = 'Succesful getting product variants of quotation in JSON format';
                } catch ( Exception | Throwable $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while convert product variants of quotation into JSON format!';
                }

                return $respond;
            }

            /**
             * Get corresponding product variant records of given 
             * quotation module paramter and generate mandatory 
             * data structure for pdf downloading.
             * @param App\Models\Quotation $quotation
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getProductVariantsCollectionForPdfDownload($quotation){
                $respond = (object)[];

                $shopId = $quotation->shop_id;
                $totalProductVariantQuantities = 0;
                $quotationProductVariantsCollection = new Collection();
                $customerMode = $quotation->websiteCustomer == null ? 'Guest' : $quotation->websiteCustomer->user->username;

                try {
                    $quotationProductVariants = $quotation->productVariants;
                    foreach( $quotationProductVariants as $productVariant ){
                        // get temporary location
                        $tempLocation = self::getProductVariantShopQuantities( $productVariant, $shopId );
                        if( !$tempLocation->data ){
                            return $tempLocation;
                        }
                        $tempLocation = $tempLocation->data->location;
                        $product      = $productVariant->product;

                        $quotationProductVariantsCollection->push((object)[
                            'id'            => $productVariant->id,
                            'price'         => $productVariant->price,
                            'discount'      => $productVariant->pivot->discount,
                            'quantity'      => $productVariant->pivot->quantity,
                            'thumbnail'     => $productVariant->pivot->thumbnail,
                            'location'      => $tempLocation,
                            'serial_number' => $productVariant->serial_number,
                            'brand'         => $product->brand->name,
                            'unit'          => $product->productUnit->title,
                            'subTotal'      => $productVariant->price - ( ( $productVariant->price * $productVariant->pivot->discount ) / 100 ),
                            'description'   => $quotation->quotation_note,
                        ]);

                        $totalProductVariantQuantities += $productVariant->pivot->quantity;
                    }                
                } catch ( Exception | Throwable $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to generate product variant mandatory data for pdf downloading!';
                }

                $respond->data         = $quotationProductVariantsCollection;
                $respond->message      = 'Successful generate quotation product variant records for pdf downloading';
                $respond->customerMode = $customerMode;
                $respond->totalProductVariantQuantities = $totalProductVariantQuantities;

                return $respond;
            }
        // Product Variant Helper Functions [END]
        
        // District Helper Functions [BEING]
            /**
             * Get all district records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getDistricts(){
                $respond = (object)[];
                
                try {
                    $districts = District::all();

                    $respond->data    = $districts;
                    $respond->message = 'Successful getting all district records from database';             
                } catch ( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get district records from database!'; 
                }

                return $respond;
            }
        // District Helper Functions [END]

        // Payment Option [Bank Account] Helper Functions [BEGIN]
            /**
             * Get all payment options (bank account) records from database.
             * @return ObjectRespond [ data: result_data, message: result_message ] 
             */
            public static function getPaymentOptions(){
                $respond = (object)[];

                try {
                    $paymentOptions = BankAccount::all();

                    $respond->data    = $paymentOptions;
                    $respond->message = 'Sucessful getting all payment options (bank account) records from database';
                } catch ( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get all payment options (bank account) records from database!';
                }

                return $respond;
            }
        // Payment Option [Bank Account] Helper Functions [BEGIN]

        // Invoice Helper Functions [BEGIN]
            /**
             * Generate invoice name based on quotation id and quotation created date.
             * @param Integer $quotationId
             * @param Carbon $quotationCreatedDate
             * 
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function generateInvoiceName( $quotationId, $quotationCreatedDate ){
                $invoiceName = InvoiceNumber::orderNumberInvoicesPdf( $quotationId, $quotationCreatedDate );
                if( !$invoiceName->data ){
                    $invoiceName->message = 'Problem occured while trying to generate invoice name!';

                    return $invoiceName;
                }

                return $invoiceName;
            }
        // Invoice Helper Functions [END]

    /**
     * #########################
     *     Helper Functions
     * #########################
     */
        /**
         * Returns the index/id of quotation status
         * @param String $quotationStatus
         * @return Int quotationStatusId 
         */
        public static function getQuotationStatusId($quotationStatus){
            return array_search( $quotationStatus, self::QUOTATIONSSTATUS );
        }

        /**
         * Calculate total cost and total price after discount based on 
         * request data records of array of order quantities, array of order 
         * discount and product variant model object passed by paramaeters.
         * @param \Illuminate\Http\Request $request
         * @param App\Models\ProductVariant
         * 
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function calculateTotalCostAndPriceAfterDiscount( $request, $productVariants ){
            $respond = (object)[
                'data'    => true,
                'message' => 'Succesful calculated total cost and total price after discount',
            ];

            $totalPrice              = 0;
            $totalCost               = 0;
            $totalPriceAfterDiscount = 0;
            $quantities              = $request['quantities'];
            $productDiscount         = $request['discounts'];
            
            foreach( $productVariants as $key => $productVariant ){
                $totalPrice +=  $quantities[$key] * $productVariant->price;
                $totalCost  +=  $quantities[$key] * $productVariant->cost;
    
                // discount price of each product
                $discount                 = ( ( $quantities[$key] * $productVariant->price ) * $productDiscount[$key] ) / 100;
                $totalPriceAfterDiscount += ( $quantities[$key] * $productVariant->price ) - $discount;
            }

            $respond->totalCost               = $totalCost;
            $respond->totalPriceAfterDiscount = $totalPriceAfterDiscount;

            return $respond;
        }

        /**
         * Get mandatory modules records for quotation creation and edition form.
         * @param Integer $quotationId [ default null ]
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function getMandatoryModulesForQuotationCreationEdition($quotationId=null){
            $respond = (object)[
                'data'    => true,
                'message' => 'Successful getting all mandatory 
                            modules for quotation creation and edition',
            ];

            // get quotation and corresponding product variant record on != null
            $quotation = $JSON_productVariants = $quotationCustomerId = $quotationId;
            if( $quotationId != null ){
                $quotation = self::getQuotation($quotationId);
                if( !$quotation->data ){
                    return $quotation;
                }
                $quotation = $quotation->data;

                // get quotation customer id
                if( $quotation->customer_website_id != null || $quotation->customer_website_id != '' ){
                    $quotationCustomerId = $quotation->customer_website_id;
                }

                // get product variant records
                $JSON_productVariants = self::getJsonProductVariantsOfQuotation($quotation);
                if( !$JSON_productVariants->data ){
                    return $JSON_productVariants;
                }
                $JSON_productVariants = $JSON_productVariants->data;
            }

            // current logged in user
            $systemUser = auth()->user();

            // get website customer records
            $customers = self::getCustomers();
            if( !$customers->data ){
                return $customers;
            }
            $customers = $customers->data;

            // get customer group records
            $customerGroups = self::getCustomerGroups();
            if( !$customerGroups ){
                return $customerGroups;
            }
            $customerGroups = $customerGroups->data;

            // get district records
            $districts = self::getDistricts();
            if( !$districts ){
                return $districts;
            }
            $districts = $districts->data;

            // get shop records
            $shops = self::getShops();
            if( !$shops->data ){
                return $shops;
            }
            $shops = $shops->data;

            $respond->shops                = $shops;
            $respond->quotation            = $quotation;
            $respond->districts            = $districts;
            $respond->customers            = $customers;
            $respond->systemUser           = $systemUser;
            $respond->quotationCustomerId  = $quotationCustomerId;
            $respond->JSON_productVariants = $JSON_productVariants;
            $respond->customerGroups       = $customerGroups->sortBy('name');

            return $respond;
        }

        /**
         * Get mandatory modules with custom structured for download quotation pdf(invoice).
         * @param Integer $quotationId
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function getMandatoryModulesForQuotationPdfDownload($quotationId){
            $respond = (object)[
                'data'    => true,
                'message' => 'Succesful getting all mandatory modules for download quotation pdf',
            ];

            // get quotation record
            $quotation = self::getQuotationWithCustomDataForPDF($quotationId);
            if( !$quotation->data ){
                return $quotation;
            }
            $onlineUser   = $quotation->userName;
            $quotation    = $quotation->data;
            $mode         = 'invoice';
            $shopLocation = $quotation->shop->address;

            // get quotation product variants ( quotatoin details )
            $quotationProductVariants = self::getProductVariantsCollectionForPdfDownload($quotation);
            if( !$quotationProductVariants->data ){
                return $quotationProductVariants;
            }
            $shippingDetail                = $quotation->websiteCustomer;
            $customerMode                  = $quotationProductVariants->customerMode;
            $totalProductVariantQuantities = $quotationProductVariants->totalProductVariantQuantities;
            $quotationProductVariants      = $quotationProductVariants->data;

            // get payment option records
            $bankAccounts = self::getPaymentOptions();
            if( !$bankAccounts->data ){
                return $bankAccounts;
            }
            $bankAccounts = $bankAccounts->data;

            // generate invoice name
            $invoiceName = self::generateInvoiceName( $quotationId, $quotation->created_at );
            if( !$invoiceName->data ){
                return $invoiceName;
            }
            $invoiceName = $invoiceName->data;

            $respond->mode                          = $mode;
            $respond->quotation                     = $quotation;
            $respond->onlineUser                    = $onlineUser;
            $respond->invoiceName                   = $invoiceName;
            $respond->bankAccounts                  = $bankAccounts;
            $respond->shopLocation                  = $shopLocation;
            $respond->shippingDetail                = $shippingDetail;
            $respond->customerMode                  = $customerMode;
            $respond->quotationProductVariants      = $quotationProductVariants;
            $respond->totalProductVariantQuantities = $totalProductVariantQuantities;

            return $respond;
        }

    /**
     * ########################
     *      Relationships
     * ########################
     */
        /**
         * One quotation to one sale relationship.
         * @return App\Models\Sales
         */
        public function sale(){
            return $this->hasOne(
                Sales::class,
                'quotation_id',
            );
        }

        /**
         * Many quotations to one website customer relationship.
         * @return App\Models\CustomerWebsite
         */
        public function websiteCustomer(){
            return $this->belongsTo(
                CustomerWebsite::class,
                'customer_website_id',
            );
        }

        /**
         * Many quotations to one user relationship.
         * @return App\Models\User
         */
        public function user(){
            return $this->belongsTo(
                User::class,
                'user_id',
            );
        }

        /**
         * Many quotations to one shop relationship.
         * @return App\Models\Shop
         */
        public function shop(){
            return $this->belongsTo(
                Shop::class,
                'shop_id',
            );
        }

        /**
         * Many quotations to many products pivot table.
         * @return App\Models\Product
         */
        public function products(){
            return $this->belongsToMany(
                Product::class,
                'quotations_products_bridge',
                'quotation_id',
                'product_id',
            );
        }

        /**
         * Many quotations to many product variants pivot table.
         * @return App\Models\ProductVariant
         */
        public function productVariants(){
            return $this->belongsToMany(
                ProductVariant::class,
                'quotations_product_variants_bridge',
                'quotation_id',
                'product_variant_id',
            )->withPivot(
                'name', 'sku', 'thumbnail', 'cost',
                'quantity','price','discount',
            );
        }

        /**
         * Many quotations to many log histories (Polymorphic).
         * @return App\Models\LogHistory 
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

    /**
     * ###################################
     *      Fast Validation Functions
     * ###################################
     */
        /**
         * Validate mandatory modules records for quotation store and 
         * update method based on submited requests data from front-end.
         * @param \Illuminate\Http\Request $request
         * @param Integer $quotationId [ default null ]
         * 
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function checkMandatoryModulesRecordsForQuotationStoreUpdate( $request, $quotationId=null ){
            $respond = (object)[
                'data'    => true,
                'message' => 'Successful getting all mandatory 
                            modules for quotation store and update',
            ];

            // get quotation record on != null
            $quotation = $quotationId;
            if( $quotationId != null ){
                $quotation = self::getQuotation($quotationId);
                if( !$quotation->data ){
                    return $quotation;
                }
                $quotation = $quotation->data;
            }

            // get product variant records
            $productVariants = self::getProductVariantsArr($request['productVariantId']);
            if( !$productVariants->data ){
                return $productVariants;
            }
            $productVariants = $productVariants->data;

            // get website customer record
            $websiteCustomer = self::getCustomer($request['cus-id']);
            if( !$websiteCustomer->data ){
                return $websiteCustomer;
            }
            $websiteCustomer  = $websiteCustomer->data;
            $customerDiscount = $websiteCustomer->cusgroup;

            $respond->quotation        = $quotation;
            $respond->productVariants  = $productVariants;
            $respond->websiteCustomer  = $websiteCustomer;
            $respond->customerDiscount = $customerDiscount;

            return $respond;
        }

        /**
         * Validation all request data submited from front-end.
         * @param \Illuminate\Http\Request $request
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function checkReuqestValidation($request){
            $respond = (object)[
                'data'    => true,
                'message' => 'All request values are valided'
            ];

            // quotation tax
            $quotationTax = $request['vat'] == null ? 0 : $request['vat'];

            // delivery options
            $hasDeliveryOption = $request['delivery_check'] == null ? 'off' : $request['delivery_check'];

            // delivery fee
            $deliveryFee  = $request['shipping'];
            $hasDeliveryOption == 'on' ? $deliveryFee = 0 : null;

            // quotation status
            $quotationStatusResult = self::getQuotationStatusId($request['quotation-status']);
            if( !$quotationStatusResult ){
                $respond->data    = false;
                $request->message = 'Quotaiton status not found or incorrect provided value!';

                return $respond;
            }
            $quotationStatus = $request['quotation-status'];

            // shop / warehouse id
            $shopId = $request['shop_id'];

            // staff noted
            $staffNoted = $request['staff-note'];

            // quotation noted
            $quotationNoted = $request['quotation-note'];

            // quotation deadline date
            $deadLineDate = $request['datetime'];

            // quotation reference number
            $reference = $request['reference'];

            // product variant order quantities
            $productVariantOrderQuantities = $request['quantities'];

            // product variant order discount
            $productVariantOrderDiscounts = $request['discounts'];

            $respond->shopId                        = $shopId;
            $respond->reference                     = $reference;
            $respond->staffNoted                    = $staffNoted;
            $respond->deliveryFee                   = $deliveryFee;
            $respond->deadLineDate                  = $deadLineDate;
            $respond->quotationTax                  = $quotationTax;
            $respond->quotationNoted                = $quotationNoted;
            $respond->quotationStatus               = $quotationStatus;
            $respond->hasDeliveryOption             = $hasDeliveryOption;
            $respond->productVariantOrderDiscounts  = $productVariantOrderDiscounts;
            $respond->productVariantOrderQuantities = $productVariantOrderQuantities;

            return $respond;
        }
}
