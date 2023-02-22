<?php

namespace App\Models;

use Exception;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sales extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'sales';

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
        'paid',
        'note',
        'total',
        'status',
        'biller_id',
        'deadline_at',
        'quotation_id',
        'payment_status',
        'payment_options',
        'reference_number',
        'service_group_id',
        'payment_options_reference_number',
        
    ];

    /**
     * Sales PAYMENTSTATUS
     * @var Array  
     */
    public const PAYMENTSTATUS = [
        1 => 'due',
        2 => 'paid',
        3 => 'denial',
        4 => 'partial',
        5 => 'pending',

    ];

    /**
     * Sales PAYMENT OPTIONS
     * @var Array
     */
    public const PAYMENTOPTIONS = [
        1 => 'bank transfer',
        2 => 'cash',
        3 => 'cheque',
        4 => 'credit card',

    ];

    /**
     * Sales SALESSTATUS
     * @var Array
     */
    public const SALESSTATUS = [
        1 => 'pending',
        2 => 'complete',
        3 => 'denial',
        4 => 'due',

    ];

    /**
     * Delivery status enum bootstrap style sheet.
     * @var Array
     */
    public static $DELIVERYSTATUS_STYLESHEET = [
        'btn-outline-danger'  => 'cancel',
        'btn-outline-warning' => 'pending',
        'btn-outline-info'    => 'delivering',
        'btn-outline-success' => 'delivered',
        'btn-outline-blue'    => 'pickup',

    ];

    /**
     * Payment status enum bootstrap style sheet.
     * @var Array
     */
    public static $PAYMENTSTATUS_STYLESHEET = [
        'btn-outline-danger'  => 'cancel',
        'btn-outline-warning' => 'pending',
        'btn-outline-info'    => 'partial',
        'btn-outline-success' => 'paid',

    ]; 

    /**
     * ##################################
     *      Modules Helper functions
     * ##################################
     */
        // Sales Helper Function [BEGIN]
            /**
             * Get all sales records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getSales(){
                $respond = (object)[];

                try {
                    $sales = Sales::all();
                    $respond->data    = $sales;
                    $respond->message = 'Sales records found.';             
                } catch(Exception $e) {
                    $respond->data    = false;
                    $respond->message = 'Problem occurred while trying to get booking records!'; 
                }

                return $respond;
            }

            /**
             * Get specific sales record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getSale( $id ){
                $respond = (object)[];

                try {
                    $sale = Sales::findOrFail($id);
                    $respond->data    = $sale;
                    $respond->message = 'Sales record found.';             
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false;
                    $respond->message = 'Booking record not found!'; 
                }

                return $respond;
            }
        // Sales Helper Function [END]

        // Product Helper Function [BEGIN]
            /**
             * Get all product records from database.
             * 
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProducts(){
                $respond = (object)[];

                try {
                    $products         = Product::all();
                    $respond->data    = $products;
                    $respond->message = 'All product records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product records!';
                }

                return $respond;
            }

            /**
             * Get all product records from database based on given product ids.
             * @param Array $productIdArr
             * 
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProductsArr( $productIdArr ){
                $respond = (object)[];
                
                $arrLength = count($productIdArr);
                if($arrLength <= 0){
                    $respond->data    = false;
                    $respond->message = 'Unable to add product to booking, empty product provided!';
                    return $respond;
                }
                
                try {
                    $products         = Product::findOrFail($productIdArr);
                    $respond->data    = $products;
                    $respond->message = 'All products records found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'One of product ids does not exit, unable to create booking, please double check product or refresh the page!';
                }

                return $respond;
            }

            /**
             * Get specific product record from database based on given id.
             * @param Integer $id
             * 
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProduct( $id ){
                $respond = (object)[];

                try {
                    $products         = Product::findOrFail($id);
                    $respond->data    = $products;
                    $respond->message = 'Product record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Product record not found!';
                }

                return $respond;
            }
        // Product Helper Function [END]

        // Product Variant Helper Functions [BEGIN]
            /**
             * Get all product variant records from database.
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProductVariants(){
                $respond = (object)[];
            
                try {
                    $productvariants  = ProductVariant::all();
                    $respond->data    = $productvariants;
                    $respond->message = 'Successfull getting all product variant records from database';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variant records from database!';
                }

                return $respond;
            }

            /**
             * Get product variant records from database 
             * based on given product variant ids.
             * @param Array $productVariantIdArr
             * @return ObjectRespond [ data: data_result; message: message_result ]
             */
            public static function getProductVariantsArr( $productVariantIdArr ) {
                $respond = (object)[];

                // prevent null pv
                if($productVariantIdArr == null || $productVariantIdArr == ''){
                    $respond->data    = false;
                    $respond->message = 'Please select product before add sales...!';
                    return $respond;
                }

                $arrLength = count( $productVariantIdArr );
                if( $arrLength <= 0 ) { // check empty arr
                    $respond->data    = false;
                    $respond->message = 'Unable to generate order receipt due to empty product variant provided!';

                    return $respond;
                }

                $productVariants = new Collection();
                foreach( $productVariantIdArr as $productVariantId ) {
                    try {
                        $tmpProductVariant = ProductVariant::findOrFail( $productVariantId );
                        $productVariants->push( $tmpProductVariant );

                    } catch( ModelNotFoundException $ex ){
                        $respond->data    = false;
                        $respond->message = 'One of product variant ids does not exit, unable to generate order receipt, please double check and try again!';
                    }
                }

                $respond->data    = $productVariants;
                $respond->message = 'All product variant records found';

                return $respond;
            }

            /**
             * Get product variant ids and it's coresponding order quantities based on 
             * given order module as a paramter, and return in JSON format.
             * @param App/Model/Order
             * 
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getOrderOfProductVariantInJsonFormat( $order ){
                $respond = (object)[];

                $orderOfProductVariants = array();

                try {
                    foreach( $order->orderDetails as $orderDetail ) {
                            $productVariantShop = DB::table('product_variant_shops')
                                                    ->where('product_variant_id', $orderDetail->productVariant->id)
                                                    ->where('shop_id', $order->shop_id)
                                                    ->first();

                        array_push( $orderOfProductVariants, (object)[
                            'id'             => $orderDetail->productVariant->id,
                            'name'           => $orderDetail->productVariant->serial_number.' '.$orderDetail->productVariant->product_name,
                            'cost'           => $orderDetail->productVariant->cost,
                            'price'          => $orderDetail->productVariant->price,
                            'discount'       => $orderDetail->discount,
                            'sub_total'      => number_format( ( $orderDetail->productVariant->price ) * ( $orderDetail->quantity ), 2 ),
                            'quantity'       => $orderDetail->quantity,
                            'quantity_store' => $productVariantShop->quantity,
                        ] );
                    }

                    $respond->data    = json_encode( $orderOfProductVariants );
                    $respond->message = 'Successful getting product variants in JSON format';
                } catch( Exception | Throwable $ex ){
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get order of product variants in json format!';
                }
                
                return $respond;
            }

            /**
             * Get quantity of product variant of specific shop
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

                    $respond->id       = $productVariant->id;
                    $respond->data     = $productVariants;
                    $respond->quantity = $productVariants->quantity;
                    $respond->message  = 'Succesfull getting product variant quantity on specific shop id from database';
                } catch( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variant quantity on specific shop id from database!';
                }

                return $respond;
            }
        // Product Variant Helper Functions [END]

        // Biller Helper Function [BEGIN]
            /**
             * Get all biller records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBillers(){
                $respond = (object)[];

                try {
                    $billers = Biller::all();
                    $respond->data    = $billers;
                    $respond->message = 'Successfull getting all biller records from database';             
                } catch(Exception $e) {
                    $respond->data    = false;
                    $respond->message = 'Problem occurred while trying to get biller records from database!'; 
                }

                return $respond;
            }

            /**
             * Get specific biller record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBiller( $id ){
                $respond = (object)[];

                try {
                    $biller = Biller::findOrFail($id);
                    $respond->data    = $biller;
                    $respond->message = 'Biller record found.';             
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false;
                    $respond->message = 'Biller record not found!'; 
                }

                return $respond;
            }
        // Biller Helper Function [END]

        // Service Group Helper Functions [BEGIN]
            /**
             * Get all service group records from database.
             * 
             * @return ObjectRespond [ data: data_result; message: message_result ]
             */
            public static function getServiceGroups(){
                $respond = (object)[];

                try {
                    $serviceGroups    = ServiceGroup::all();
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
             * 
             * @return ObjectRespond [ data: data_result; message: message_result ]
             */
            public static function getServiceGroup( $id ){
                $respond = (object)[];

                try {
                    $serviceGroup     = ServiceGroup::findOrFail($id);
                    $respond->data    = $serviceGroup;
                    $respond->message = 'Service group record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Service group record not found!';
                }

                return $respond;
            }
        // Service Group Helper Functions [END]

        // Quotation Helper Function [BEGIN]
            /**
             * Get all quotation records from database.
             * 
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getQuotations(){
                $respond = (object)[];

                try {
                    $qoutations       = Quotation::all();
                    $respond->data    = $qoutations;
                    $respond->message = 'Quotation records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while tyring to get quotation records from database!';
                }

                return $respond;
            }

            /**
             * Get specific quotation record from database base on given id.
             * @param Integer $id
             * 
             * @return App/Model/Quotation
             */
            public static function getQuotation( $id ){
                $respond = (object)[];

                try {
                    $quotation        = Quotation::findOrFail($id);
                    $respond->data    = $quotation;
                    $respond->message = 'Quotation record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Quotation record not found!';
                }

                return $respond;
            }
        // Quotation Helper Function [END]

        // Order Helper Funtion [BEGIN]
            /**
             * Get specific order record based on given id from database.
             * @param Integer $id
             * 
             * @return ObejectRespond [ data: date_result, message: message_result ]
             */
            public static function getOrder($id){
                $respond = (object)[];

                try {
                    $user_online      = DefaultUserDownloadPdf::findOrFail(1);
                    $user_online_name = User::findOrFail($user_online->user_id);
                    $order            = Order::findOrFail($id);

                    $respond->data            = $order;
                    $respond->username_online = $user_online_name->firstname .' '. $user_online_name->lastname;
                    $respond->message         = 'Order record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Order record not found!';
                }

                return $respond;
            }

            /**
             * Filter order records based on order status from database.
             * @param String $orderStatus
             * 
             * @return RespondObject [ data: data_result, message: result_message ]
             */
            public static function filderOrderByStatus( $orderStatus ){
                $respond = (object)[];
                $systemUser   = auth()->user();
                $userShopsIds = $systemUser->shops->pluck('id');
                try {
                   // dynamic change query operator based on orderStatus
                    $queryOperator = '=';
                    if( !$orderStatus || $orderStatus == 'all' ){
                        $queryOperator = '!=';
                        $orderStatus   = null;
                    }

                    $orders = Order::whereIn('shop_id', $userShopsIds)
                                ->where('order_status', $queryOperator, $orderStatus)
                                ->with('shippingDetail')
                                ->orderBy('created_at', 'DESC')
                                ->paginate(25)->onEachSide(2);

                    $respond->data    = $orders;
                    $respond->message = "Successfull filter order records by ".strtolower( $orderStatus );
                } catch( Exception | Throwable | ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = "Problem occured while trying to filter order by status from the database!";
                }

                return $respond;
            }

            /**
             * Get location of product variant records from database.
             * @param App/Model/Order $order
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getOrderDetailsOfPv( $order ){
                $respond = (object)[];

                // get shop location record
                try {
                    $respond->location_shop = Shop::findOrFail( $order->shop_id )->address;
                } catch( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get location of the shop from database!';
                    
                    return $respond;
                }

                $totalQtyOfProduct      = 0;
                $orderDetailCollections = new Collection();
                // get order detail of product variants co-responding to the shop
                try {
                    $orderDetails = $order->orderDetails;

                    foreach( $orderDetails as $orderDetail ){
                        $tempProductvariantPivots = DB::table('product_variant_shops')
                            ->where('shop_id', $order->shop_id)
                            ->where('product_variant_id', $orderDetail->product_variant_id)
                            ->first();
                        
                        $orderDetailCollections->push((object)[
                            'id'            => $orderDetail->id,
                            'price'         => $orderDetail->price,
                            'discount'      => $orderDetail->discount,
                            'quantity'      => $orderDetail->quantity,
                            'thumbnail'     => $orderDetail->thumbnail,
                            'location'      => $tempProductvariantPivots->location,
                            'serial_number' => $orderDetail->productVariant->serial_number,
                            'brand'         => $orderDetail->productVariant->product->brand->name,
                            'unit'          => $orderDetail->productVariant->product->productUnit->title,
                            'subTotal'      => $orderDetail->price - ( ( $orderDetail->price * $orderDetail->discount ) / 100 ),
                            'description'   => $orderDetail->detail_invoice != null ?  $orderDetail->detail_invoice :  $orderDetail->name,
                        ]);

                        $totalQtyOfProduct += $orderDetail->quantity;
                    }
                    
                    $respond->totalQtyOfProduct = $totalQtyOfProduct;
                    $respond->data              = $orderDetailCollections;
                    $respond->message           = 'All order detail records found';
                    $respond->customer_mode     = $order->customer_website == null ? 'Guest' : $order->customer_website->user->username;

                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while tryting to get product variants record of order detail';
                }

                return $respond;
            }
        // Order Helper Funtion [END]

        // Website Customer Helper Function [BEGIN]
            /**
             * Get all website customer records from database.
             * 
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getWebsiteCustomers(){
                $respond = (object)[];

                try {
                    $websiteUsers     = CustomerWebsite::all();
                    $respond->data    = $websiteUsers;
                    $respond->message = 'Successfull getting all website user records from database';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get website user records from database!';
                }

                return $respond;
            }

            /**
             * Get specific customer record based on 
             * given id paramter from database.
             * @param Int $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getCustomer( $id ){
                $respond = (object)[];

                try {
                    $customer = CustomerWebsite::findOrFail($id);
                    $respond->data    = $customer;
                    $respond->message = 'Website user record found';
                } catch( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Website user record not found!';
                }

                return $respond;
            }

            /**
             * Get all customer group records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getCustomerGroups(){
                $respond = (object)[];
                
                try {
                    $customerGroups   = CustomerGroup::all();
                    $respond->data    = $customerGroups;
                    $respond->message = 'Successfull getting all customer group records from database';
                } catch( Exception $ex ) {
                    $respond->data = false;
                    $respond->message = 'problem occured while trying to get customer group records from database!';
                }

                return $respond;
            }

            /**
             * Get walkin customer record from database.
             * @return ObjectRespond [ data: data_result, mesage: result_message ]
             */
            public static function getWalkInCustomer(){
                $respond = (object)[];

                try {
                    $walkInCustomer   = User::where('username', 'walk_in_customer')->first();
                    $respond->data    = $walkInCustomer;
                    $respond->message = 'Successfull getting walkin customer record from database';
                } catch( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get walkin customer record from database!';
                }

                return $respond;
            }
        // Website Customer Helper Function [END]

        // Shop Helper Function [BEGIN]
            /**
             * Get all shop records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShops(){
                $respond = (object)[];
                
                try {
                    $user = auth()->user();
                    $shops = $user->shops;  
                    $respond->data    = $shops;
                    $respond->message = 'Successfull getting all shop records from database';             
                } catch( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occurred while trying to get shop records from database!'; 
                }

                return $respond;
            }

            /**
             * Get specific shop record based 
             * on given id parameter from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getShop( $id ){
                $respond = (object)[];

                try {
                    $shop = Shop::findOrFail( $id );
                    $respond->data    = $shop;
                    $respond->message = 'Shop record found.';             
                } catch( ModelNotFoundException $e ) {
                    $respond->data    = false;
                    $respond->message = 'Ship record not found!'; 
                }

                return $respond;
            }
        // Shop Helper Function [END]

        // District Helper Functions [BEING]
            /**
             * Get all district records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getDistricts(){
                $respond = (object)[];
                
                try {
                    $districts = District::all();
                    $respond->data    = $districts;
                    $respond->message = 'Successful getting all district records from database';             
                } catch( Exception $e ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get district records from database!'; 
                }

                return $respond;
            }
        // District Helper Functions [END]

        // Over Sales Helper Function [BEING]
            /**
             * Get overSale record based on 
             * given id parmter form database.
             * @param int $id
             * 
             * @return ResponObject [ data: result_data, messange:result_messange ]
             */
            protected static function getOverSale( $id ){
                $respond = (object)[];
        
                try {
                    $overSale = OverSales::findOrFail( $id );
                    $respond->data     = $overSale;
                    $respond->messange = 'Oversale record found';
                } catch ( ModelNotFoundException $e ) {
                    $respond->data     = false;
                    $respond->messange = 'Oversale record not found!';
                }

                return $respond;
            } 
        // Over Sales Helper Function [ENG]
        
    /**
     * ############################
     *      Helper functions
     * ############################
     */
        /**
         * Calculate total cost of sales based on service fee and discount percentage.
         * @param  Float $serviceFee
         * @param  Float $discountPercentage
         * 
         * @return Float $totalCost
         */
        public static function calculateTotalCost( $serviceFee, $discountPercentage ){
            $discountPercentage = ($discountPercentage / 100);
            $totalCost = $serviceFee - ($serviceFee * $discountPercentage);
            $totalCost = number_format($totalCost, 2, '.', '');

            return $totalCost;
        }

        /**
         * Calculate total cost of sale based on vat percentage and return total including vat.
         * @param Float $totalCost
         * @param Float $vatPercentage
         * 
         * @return Float $totalCost
         */
        public static function calculateTotalCostIncludingVat( $totalCost, $vatPercentage ){
            $vatPercentageInNumber = (100 + $vatPercentage) / 100;
            $totalCostAfterVat = ($totalCost * $vatPercentageInNumber);
            $totalCostAfterVat = number_format($totalCostAfterVat, 2, '.', '');

            return $totalCostAfterVat;
        }

        /**
         * Returns the id of payment status
         * @param String $paymentStatus
         * 
         * @return Int paymentStatusId 
         */
        public static function getPaymentStatusId( $paymentStatus ){
            return array_search($paymentStatus, self::PAYMENTSTATUS);
        }

        /**
         * Returns the id of payment option
         * @param String $paymentOption
         * 
         * @return Int paymentStatusId
         */
        public static function getPaymentOptionsId( $paymentOption ){
            return array_search($paymentOption, self::PAYMENTOPTIONS);
        }

        /**
         * Returns the id of sales status
         * @param String $salesStatus
         * 
         * @return Int salesStatusId 
         */
        public static function getSalesStatusId( $salesStatus ) {
            return array_search($salesStatus, self::SALESSTATUS);
        }

        /**
         * Check payment number type and value.
         * @param String $number
         * 
         * @return Boolean true|false
         */
        protected static function checkValidPaymentNumber( $number ) {
            if(is_numeric($number) && $number > 0){
                return $number;
            } else {
                return false;
            }
        }

        /**
         * Check valid address value.
         * @param $value
         * 
         * @return ObjectRespond [ data:data_result, message:result_message  ]
         */
        public static function checkValidAddress( $value ){
            $respond = (object)[];

            if( !preg_match("/^([a-zA-Z0-9 #,.-])+$/i", $value) ){
                $respond->data    = false;
                $respond->message = "Value invalid, only alphanumeric, whitespace and # , . - are allow";
            } else {
                $respond->data    = $value;
                $respond->message = 'Value valid';
            }

            return $respond;
        }

        /**
         * Check valid static base on status value and status mode.
         * @param String $statusValue
         * @param String $mode [payment_status, sales_status]
         * 
         * @return Boolean false | String statusValue
         */
        public static function checkValidStatus( $statusValue, $mode ){
            $statusResult = false;

            switch ($mode) {
                case 'payment_status' : {
                    $statusResult = Sales::getPaymentStatusId($statusValue); 
                }
                    break;
                case 'payment_options' : {
                    $statusResult = Sales::getPaymentOptionsId($statusValue);
                }
                    break;
                case 'shipment_status' : {
                    $statusResult = Sales::getShipmentStatusId($statusValue); 
                }
                    break;
                case 'sales_status' : {
                    $statusResult = Sales::getSalesStatusId($statusValue); 
                }
                    break;
            }
            
            return !$statusResult ? $statusResult : $statusValue;
        }

        /**
         * Check valid alphanumeric only without whitespace.
         * @param String value;
         * 
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        protected static function checkValidString( $value ){
            $respond = (object)[];

            if (!preg_match("/^[a-zA-Z0-9' ]*$/",$value)) {
                $respond->data    = false;
                $respond->message = 'String is invalid!';
            } else {
                $respond->data    = $value;
                $respond->message = 'String is valid!';
            }

            return  $respond;
        }

        /**
         * Check valid sale product quantity values.
         * @param Array arrValues;
         * 
         * @return RespondObject [ data: result_data, message: result_message ]
         */
        protected static function checkValidQuantityArr( $arrValues ){
            $respond = (object)[];
            
            $arrLength = count($arrValues);
            for($i = 0; $i < $arrLength; $i++){
                $tmepResult = Sales::checkValidPaymentNumber( $arrValues[$i] );
                if(!$tmepResult){
                    $respond->data = false;
                    $respond->message = 'One of request product quantity is not a number of value is smaller than 0, unable to create booking!';

                    return $respond;
                }
            }

            $respond->data    = $arrValues;
            $respond->message = 'Sales product quantities value are all valided';

            return  $respond;
        }

        /**
         * Check valid payment value by compare total amount value with 
         * addition of new submitted payment with payment made data on database.
         * @param String $saleTotalPayment
         * @param String $saleTotalPaymentMade
         * @param String $newSubmittedPaymentMade
         * 
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function checkValidPaymentMadeValue( $saleTotalPayment, $saleTotalPaymentMade, $newSubmittedPaymentMade ){
            $respond = (object)[];
            $newTotalPaymentAmount = number_format( ($saleTotalPaymentMade + $newSubmittedPaymentMade), 2 );
            
            // validate total amount of payment greater than total amount
            if($newTotalPaymentAmount > $saleTotalPayment){
                $respond->data    = false;
                $respond->message = 'Can not update payment due to submitted payment amount value greater than total amount!';

                return $respond;
            }

            // validate total amount of payment smaller than 0
            if($newTotalPaymentAmount < 0) {
                $respond->data    = false;
                $respond->message = 'Can not update payment due to submitted payment amount value smaller than 0!';

                return $respond;
            }

            $respond->data    = $newTotalPaymentAmount;
            $respond->message = 'Payment amount valided';

            return $respond;
        }

        /**
         * Get list of mandatory modules 
         * record for creating sales record.
         * 
         * @return RespondObject [ data: data_result, message: result_message ] 
         */
        public static function getMandatoryModulesForSalesCreation() {
            $respond = (object)[];

            // get website customer records
            $websiteCustomers = self::getWebsiteCustomers();
            if( !$websiteCustomers->data ){
                return $websiteCustomers;
            }
            $websiteCustomers = $websiteCustomers->data;

            // get shop records
            $shops = self::getShops();
            if( !$shops->data ){
                return $shops;
            }
            $shops = $shops->data;

            // get biller records
            $billers = Sales::getBillers();
            if( !$billers->data ){
                return $billers;
            }
            $billers = $billers->data;

            // get customer group records
            $customerGroups = self::getCustomerGroups();
            if( !$customerGroups->data ){
                return $customerGroups;
            }
            $customerGroups = $customerGroups->data;

            // get district records
            $districts = self::getDistricts();
            if( !$districts->data ){
                return $districts;
            }
            $districts = $districts->data;

            // get overSale record
            $overSale = self::getOverSale(1);
            if( !$overSale->data ){
                return $overSale;
            }
            $overSale = $overSale->data;

            $respond->shops            = $shops;
            $respond->billers          = $billers;
            $respond->overSale         = $overSale;
            $respond->districts        = $districts;
            $respond->customerGroups   = $customerGroups;
            $respond->websiteCustomers = $websiteCustomers;
            
            $respond->data    = true;
            $respond->message = 'Successful getting all mandatory module records from database for sales creation';

            return $respond;
        }

        /**
         * Get list of mandatory modules record based 
         * on order id for edit sales record.
         * @param Integer $orderId
         * 
         * @return RespondObject [ data: data_result, message: result_message ]
         */
        public static function getMandatoryModulesForSalesEdition( $orderId ){
            $respond = (object)[];

            // get order record
            $order = self::getOrder($orderId);
            if( !$order->data ){
                return $order;
            }
            $order = $order->data;

            // get product variants in json format based on order
            $JsonOrderOfProductVariants = self::getOrderOfProductVariantInJsonFormat( $order );
            if( !$JsonOrderOfProductVariants->data ){
                return $JsonOrderOfProductVariants;
            }
            $JsonOrderOfProductVariants = $JsonOrderOfProductVariants->data;
            
            // get order customer id record
            $walkInCustomer = self::getWalkInCustomer();
            if( !$walkInCustomer->data ){
                return $walkInCustomer;
            }
            $walkInCustomer  = $walkInCustomer->data;
            $orderCustomerId = $walkInCustomer->customer_website->id;
            if( $order->customer_website_id != null || $order->customer_website_id != '' ){
                 $orderCustomerId = $order->customer_website_id;
            }

            // get website customer records
            $customers = self::getWebsiteCustomers();
            if( !$customers->data ){
                return $customers;
            }
            $customers = $customers->data;
            
            // get product variant records
            $productVariants = self::getProductVariants();
            if( !$productVariants->data ){
                return $productVariants;
            }
            $productVariants = $productVariants->data;

            // get shop records
            $shops = self::getShops();
            if( !$shops->data ){
                return $shops;
            }
            $shops = $shops->data;

            // get biller records
            $billers = self::getBillers();
            if( !$billers->data ){
                return $billers;
            }
            $billers = $billers->data;

            $respond->order                      = $order;
            $respond->shops                      = $shops;
            $respond->billers                    = $billers;
            $respond->customers                  = $customers;
            $respond->productVariants            = $productVariants;
            $respond->orderCustomerId            = $orderCustomerId;
            $respond->JsonOrderOfProductVariants = $JsonOrderOfProductVariants;
            
            $respond->data    = true;
            $respond->message = 'Succesfull getting all mandatory modules from database';

            return $respond;
        } 

    /**
     * ########################
     *      Relationship
     * ########################
     */
        /**
         * One sale to one quotation.
         * (parent to child)
         * @return App/Model/Quotation
         */
        public function quotation(){
            return $this->hasOne(
                Quotation::class,
                'quotation_id',
            );
        }

        /**
         * Many sales to one biller.
         * @return App/Model/Biller
         */
        public function biller(){
            return $this->belongsTo(
                Biller::class,
                'biller_id',
            );
        }

        /**
         * Many sales to one service group.
         * @return App/Model/ServiceGroup
         */
        public function serviceGroup(){
            return $this->belongsTo(
                ServiceGroup::class,
                'service_group_id',
            );
        }
        
        /**
         * Many sales to many product pivot table.
         * @return App/Model/Product
         */
        public function products(){
            return $this->belongsToMany(
                Product::class,
                'sales_products_bridge',
                'sale_id',
                'product_id',
            );
        }

        /**
         * One sale to many payment options.
         * @return App/Model/PaymentOptions
         */
        public function paymentOptions(){
            return $this->hasMany(
                PaymentOptions::class,
                'sale_id',
            );
        }

        /**
         * Many sales to many log histories relationship (Polymorphic)
         * @return App/Model/LogHistory 
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }
 
    /**
     * ##################################
     *      Fast Validation Functions
     * ##################################
     */
        /**
         * Validation request data.
         * @param Form_Request_Value $salesStatus
         * @param Form_Request_Value $paymentStatus
         * @param Form_Request_Value $referenceNumber
         * @param Form_Request_Value $quantities
         * 
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        protected static function checkReuqestValidation( $salesStatus, $paymentStatus, $referenceNumber, $quantities ){
            $respond = (object)[];

            // check valid sales status
            $salesStatusResult = Sales::checkValidStatus($salesStatus, 'sales_status');
            if(!$salesStatusResult){
                $respond->data = $salesStatusResult;
                $respond->message = 'Booking status not found or incorrect providing!';

                return $respond;
            }

            // check valid payment status
            $paymentStatusResult = Sales::checkValidStatus($paymentStatus, 'payment_status');
            if(!$paymentStatusResult) {
                $respond->data = $paymentStatusResult;
                $respond->message = 'Payment status not found or incorrect providing!';

                return $respond;
            }

            // check valid reference number 
            $referenceNumberResult = Sales::checkValidString($referenceNumber);
            if(!$referenceNumberResult->data){
                $referenceNumberResult->message = 'Reference number is invalid!';

                return $referenceNumberResult;
            }

            // check sale product quantity
            $quantitiesResult = Sales::checkValidQuantityArr($quantities);
            if(!$quantitiesResult->data){
                return $quantitiesResult;
            }

            $respond->salesStatus       = $salesStatusResult;
            $respond->paymentStatus     = $paymentStatusResult;
            $respond->saleProductQtnArr = $quantitiesResult->data;
            $respond->referenceNumber   = strtolower($referenceNumberResult->data);

            $respond->data = true;
            $respond->message = 'All request values valided';

            return $respond;
        }
}
