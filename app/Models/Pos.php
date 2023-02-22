<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use DateTime;
use Exception;
use Throwable;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Pos extends Model
{
    use HasFactory;

    /**
     * ###############################
     *     Modules Helper function
     * ###############################
     */
        
        // Order Helper Functions [BEGIN]
            /**
             * Get specific order record based on given id from database.
             * @param Integer $id
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getOrder($id){
                $respond = (object)[];
                
                try {
                    $order = Order::findOrFail($id);
                    $respond->data    = $order;
                    $respond->message = 'Order record found';
                } catch( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Order record not found!!';
                }

                return $respond;
            }
        // Order Helper Functions [END]

        // Exchange Rate Helper Functions [BEGIN]
            /**
             * Get sepcific exchange rate data based on given symbol from database.
             * @param String $symbol [ $, R ]
             * @return RespondObject [ data: data_result, message: message_result ]
             */
            public static function getExchangeRateBySymbol($symbol){
                $respond = (object)[];
                $symbol = strtoupper($symbol);

                try {
                    $exchangeRate = ExchangeRate::where('symbol', '=', $symbol)->get()->first();
                    $respond->data    = $exchangeRate;
                    $respond->message = 'Exchange rate data found';
                } catch(Exception | ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Exchange rate data not found!';
                }

                return $respond;
            }
        // Exchange Rate Helper Functions [END]

        // Bank account Helper Functions [BEING]
            /**
             * Get all bank account records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBankAccounts(){
                $respond = (object)[];

                try {
                    $bankAccounts = BankAccount::all();
                    $respond->data = $bankAccounts;
                    $respond->message = 'Records found';
                } catch(Exception $e) {
                    $respond->data = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }
            /**
             * Get all bank account records from database ().
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getBankAccountSkipFirst(){
                $respond = (object)[];
                $invoice = false;
                $receipt = false;

                try {
                    $bankAccounts = BankAccount::where('id', '>', 1)->get();
                    foreach($bankAccounts as $bankAccount){
                        if($bankAccount->is_show_in_pos_invoice){
                            $invoice = true;
                        }
                        if($bankAccount->is_show_in_pos_receipt){
                            $receipt = true;
                        }
                    }
                    $respond->data = $bankAccounts;
                    $respond->invoice = $invoice;
                    $respond->receipt = $receipt;
                    $respond->message = 'Records found';
                } catch(Exception $e) {
                    $respond->data = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }
        // Bank account Helper Functions [END]

        // Website Customer Helper Functions [BEGIN]
            /**
             * Get all website customer records from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getWebsiteCustomers(){
                $respond = (object)[];

                try {
                    $websiteCustomers = CustomerWebsite::all();
                    $respond->data           = $websiteCustomers;
                    $respond->message        = 'Successfully getting customer records';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get customer records from database!';
                }

                return $respond;
            }

            /**
             * Get default walkin customer record from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getWalkInCustomer(){
                $respond = (object)[];

                try {
                    $walkingCustomer  = User::where('username', 'walk_in_customer')->first()->customer_website;
                    $respond->data    = $walkingCustomer;                                        
                    $respond->message = 'Successfully getting walk in customer record';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get walk in customer records from database!';
                }

                return $respond;
            }
        // Guest helper functions [START]
                /**
                 * get specific guest record 
                 * @param integer #id
                 * @return objectRespond [ data: data_result , message:message_result ]
                 */
                public static function getGuest($id){
                    $respond = (object)[];
                    
                    try {
                        $guest = Guests::findOrFail($id);
                        $respond->data    = $guest;
                        $respond->message = 'Guest record found!';
                    } catch( ModelNotFoundException $ex ) {
                        $respond->data    = false;
                        $respond->message = 'guest record not found!!';
                    }
                    return $respond;
                }
        // Guest helper functions [END]


            /**
             * Get specific website customer record based on given id paramter 
             * from database.
             * @param Integer $id
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getWebsiteCustomer( $id ) {
                $respond = (object)[];

                try {
                    $websiteCustomer  = CustomerWebsite::findOrFail($id);
                    $respond->data    = $websiteCustomer;
                    $respond->message = 'Customer record found';
                } catch ( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Customer record not found!';
                }

                return $respond;
            }

            /**
             * Get all customer group records from database
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getCustomerGroups(){
                $respond = (object)[];
                
                try {
                    $customerGroups = CustomerGroup::all();
                    $respond->data = $customerGroups;
                    $respond->message = 'Customer group records found';
                } catch(Exception $ex) {
                    $respond->data = false;
                    $respond->message = 'problem occured while trying to get customer group records!';
                }

                return $respond;
            }

            /**
             * 
             */
        // Website Customer Helper Functions [END]

        // District Helper Functions [BEING]

        /**
         * Get all district records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
            public static function getDistricts(){
                $respond = (object)[];
                
                try {
                    $districts = District::all();
                    $respond->data = $districts;
                    $respond->message = 'Records found.';             
                } catch(Exception $e) {
                    $respond->data    = false;
                    $respond->message = $e->getMessage(); 
                }

                return $respond;
            }
        // District Helper Functions [END]

        // Category Helper Functions [BEGIN]
            /**
             * Get all category records with its corresponding 
             * product from database.
             * @return ObjectRespond 
            */
            public static function getCategories() {
                $respond = (object)[];
                
                try {
                    $categories       = Category::all();
                    $respond->data    = $categories;
                    $respond->message = 'Category records found!'; 
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false; 
                    $respond->message = 'Problem while tying to get category records!'; 
                };

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

            /**
             * Get all product variant based on shop 
             * product from database.
             * @return ObjectRespond 
            */
            public static function getPvBasedOnShop($shop_id) {
                $respond = (object)[];
                $allCategories = new Collection();

                // get shop record
                $shop = self::getshop($shop_id);
                if(!$shop->data){
                    return back()->with('error', $shop->message);
                }
                $shop = $shop->data;

                // get category record
                $categories = self::getCategories();
                if(!$categories->data){
                    return back()->with('error', $categories->message);
                }
                $categories = $categories->data;
                
                try {
                    $product_variants = $shop->product_variants;
                    foreach($categories as $category){
                        $categoryCollep = new Collection();
                        foreach($product_variants as $product_variant){
                            if($product_variant->product->category->name == $category->name){
                                $product_variant->quantities = self::getQuantityOfProductVariantBasedOnShop($product_variant->id, $shop->id);
                                $categoryCollep->push($product_variant);
                            }
                        }
                        $tmps = (object) [
                            'name'             => $category->name,
                            'id'               => $category->id,
                            'product_variants' => $categoryCollep,
                        ];
                        $allCategories->push($tmps);
                    }

                    $respond->data     = $allCategories;
                    $respond->shop_id  = $shop->id;
                    $respond->address  = $shop->address;
                    $respond->message = 'Category records found!'; 
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false; 
                    $respond->message = 'Problem while tying to get category records!'; 
                };

                return $respond;
            }

            /**
             * Get quantity of product variant based on shop
             * @param int $pvId
             * @param int $shopId
             * @return int $quantity
             */
            public static function getQuantityOfProductVariantBasedOnShop($pvId, $shopId){
                $tempProductvariantPivots = DB::table('product_variant_shops')
                ->where('shop_id', $shopId)
                ->where('product_variant_id', $pvId)
                ->get()
                ->first();

                $quantity = $tempProductvariantPivots->quantity;

                return $quantity;
            }

            /**
             * Get specific category records from database.
             * @param Integer $id
             * @return ObjectRespond 
            */
            public static function getCategory( $id ) {
                $respond = (object)[];
                
                try {
                    $category         = Category::findOrFail($id);
                    $respond->data    = $category; 
                    $respond->message = 'Category record found!'; 
                } catch(ModelNotFoundException $e) {
                    $respond->data    = false; 
                    $respond->message = 'Problem while tying to get category record!'; 
                };

                return $respond;
            }

            /**
             * 
             */
        // Category Helper Functions [END]

        // Product Helper Functions [BEGIN]
            /**
             * Get all product records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProducts(){
                $respond = (object)[];
                
                try {
                    $products = Product::all();
                    $respond->data    = $products;
                    $respond->message = 'Product records found';
                } catch(Exception $e) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product records!';
                }

                return $respond;
            }

            /**
             * Get specific product record from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProduct($id){
                $respond = (object)[];
                
                try {
                    $product = Product::findOrFail($id);
                    foreach($product->product_variants as $product_variant){
                        $one_img = $product_variant->image;
                        break;
                    }
                    $respond->data      = $product;
                    $respond->one_image = $one_img;
                    $respond->message   = 'Product record found';
                } catch(ModelNotFoundException $e) {
                    $respond->data      = false;
                    $respond->message   = 'Product record not found';
                }

                return $respond;
            }

        // Product Helper Functions [END]

        // Product Variant Helper Functions [BEGIN]
            /**
             * Get product variant records based on given associative product 
             * variant key value array.
             * 
             * @param App/Model/Product $productModule
             * @param Array $attributeKeyValueArrs
             * @param String $nextAttributeName
             * 
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getProductVariantsExtraAttribute( $product, $attributeKeyValueArrs, $nextAttributeName ) {
                $respond = (object)[];

                try {
                    $attributeKeyValueArrs['product_id'] = $product->id;
                    $productVariants = ProductVariant::where( $attributeKeyValueArrs )->get();

                    $extraPvAttributes = new Collection();
                    switch ( $nextAttributeName ) {
                        case 'size': { // get collection of unique sizeids for current product variants
                            $extraAttributesOfPv = $productVariants->unique('size_id');

                            foreach( $extraAttributesOfPv as $extraAttributeOfPv ) {
                                $tmpSize = $extraAttributeOfPv->size;
                                $tmpSize->name = ucwords( $tmpSize->name );
                                $extraPvAttributes->push( $tmpSize );                
                            }

                            $attributeName = 'Size';
        
                        }
                            break;
                        case '...': {
                            // code ...
                        }   
                            break;                     
                        default:
                            # code...
                            break;
                    }

                    $respond->attributeName   = $attributeName;
                    $respond->productVariants = $productVariants;
                    $respond->data            = $extraPvAttributes;
                    $respond->message         = 'Successfully getting product variant records';
                } catch( ModelNotFoundException | Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variants based on attribute key value array!';
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

                // this code work but return only one record if the same product variant is submitted
                // try {
                //     $productVariants  = ProductVariant::findOrFail($productVariantIdArr);
                //     $respond->data    = $productVariants;
                //     $respond->message = 'All product variant records found';
                // } catch(ModelNotFoundException $ex) {
                //     $respond->data    = false;
                //     $respond->message = 'One of product variant ids does not exit, unable to generate order receipt, please double check and try again!';
                // }

                $respond->data    = $productVariants;
                $respond->message = 'All product variant records found';
                return $respond;

            }

            /**
             * Get all products variants based on mode and mode value of category.
             * @param String         $mode [ by_name | by_id ]
             * @param String         $modeValue [ 1 | len]
             * @return ObjectRespond $productvariants
             */
            protected static function getAllPvsFromCategory($catId, $shopId){
                $respond = (object)[];

                // get category record
                $category = Pos::getCategory($catId);
                if(!$category->data){
                    return back()->with('error', $category->message);
                }
                $category = $category->data;

                // get shop record
                $shop = self::getshop($shopId);
                if(!$shop->data){
                    return back()->with('error', $shop->message);
                }
                $shop = $shop->data;

                // bind into ready produt variants colletion object
                $productVariantsCollection = new Collection();
                $product_variants = $shop->product_variants;
                foreach($product_variants as $product_variant){
                    if($product_variant->product->category->name == $category->name){
                        $product_variant->catId      = $category->id;
                        $product_variant->quantities = self::getQuantityOfProductVariantBasedOnShop($product_variant->id, $shop->id);
                        $productVariantsCollection->push($product_variant);
                    }
                }

                $respond->data = $productVariantsCollection;
                $respond->message = 'Successed getting all product variants';
                return $respond;
            }

        // Product Variant Helper Functions [END]

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

        // ... Helper Functions [BEGIN]
            /**
             * 
             */
        // ... Helper Functions [END]

    /**
     * #######################
     *     Helper function
     * #######################
     */
        
        /**
         * Calculated for the change amount based on total amount in USD only and return 
         * in both usd format and riel format, if USD amount can modulo with 10, then return 
         * +10 as a dollars based changed, and the rest that can't modulo with 10 as a riel
         * changed based.
         * 
         * @param Float $totalAmountInUsd
         * @param Float $rielCurrency
         * @return ObjectRespond [ usd_changed_amount, riel_changed_amount ]
         */
        public static function calculatedChangedAmountInUsdAndRiel( $totalAmountInUsd, $rielCurrency ){
            $respond = (object)[
                'usd_changed_amount'  => 0,
                'riel_changed_amount' => 0,
            ];

            $totalAmountInUsdAfterModulo       = fmod( $totalAmountInUsd, 10 );
            $totalAmountInRielFromModuloResult = ceil( $totalAmountInUsdAfterModulo * $rielCurrency );

            $respond->usd_changed_amount  = $totalAmountInUsd - $totalAmountInUsdAfterModulo;
            $respond->riel_changed_amount = ( number_format($totalAmountInRielFromModuloResult/100) )*100;
            
            return $respond;
        }

        /**
         * Generate temporary pdf receipt in local Storage and return path.
         * @param DomPdf\DomPdf $domPdf
         * @return ObjectRespond $respond
         */
        public static function generateTemporaryLocalStorageReceiptPdf( $mpdf ) {
            $respond = (object)[];

            /**
             * #################################################################################
             *      Temporary receipt file name structure is 
             *      [ Netra_Order_YYYYmmDDhhIIss.pdf ] which will 
             *      be store in [ storage/public/receiptPdf ] directory. 
             * #################################################################################
             */
            $currentDateTime = new DateTime();
            $currentDateTime = $currentDateTime->format('YmdHis');
            
            // Storage::put( 'public/receiptPdf/leskyla_orders'.$currentDateTime.'.pdf', $mpdf->Output() );
            Storage::disk('public')->put('/receiptPdf/leskyla_orders'.$currentDateTime.'.pdf', $mpdf->Output($currentDateTime.'.pdf', "S"));

            // validate if store process is successed
            $isReceiptExist = Storage::disk('public')->exists('/receiptPdf/leskyla_orders'.$currentDateTime.'.pdf');
            if( $isReceiptExist ) {
                $tmpPdfReceiptFilePath = public_path('storage\receiptPdf\leskyla_orders'.$currentDateTime.'.pdf');

                $respond->data     = $tmpPdfReceiptFilePath;
                $respond->fileName = 'leskyla_orders'.$currentDateTime.'.pdf';
                $respond->message  = 'Receipt successfully store into local storage.';

            } else {
                $respond->data    = false;
                $respond->message = 'Receipt failed to store into local storage.';
            }

            return $respond;
        }

        /**
         * Validate table selection mode and form a structure ready for store 
         * into database.
         * @param $tableSelectionValue
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function validateTableSelectionMode( $tableSelectionValue ) {
            $respond = (object)[
                'table_selection_mode'  => 'take away', // default value
                'table_number'          => '',          // default value
            ];

            // check if it was dine in value, we will trim table on dine in option.
            if( strpos( $tableSelectionValue, 'dine-in' ) !== false ) {
                $talbeNumber = ltrim( $tableSelectionValue, "dine-in#" );

                $respond->table_selection_mode = 'dine in';
                $respond->table_number         = $talbeNumber;
            }

            return $respond;
        }

    /**=============================
        Fast Validation Functions
    ================================*/

     /**
     * Loop name of product to add space in pdf
     * @param String $result
     * @return string
    */
    public static function addSpaceToOrderDetailNamePdf($text){
        $result = '';
        $number_of_text = str_split($text);
        $count = count($number_of_text);
        $number = 0;
        if($count > 15){
                foreach($number_of_text as $key => $item){
                    if($number == 15){
                        $result .= ' ';
                        $number = 0;
                    }
                    $result .= $item;
                    $number++;
                }
                return $result;
        }else{
            return $text;
        }
    }

    /**
     * Get latest record id of order from database.
     * @return ObejectRespond [ data: date_result, message: message_result ]
     */
    // public static function getLatestOrderId(){
    //     $respond = (object)[];

    //     try {
    //         $latestOrderId = Order::latest()->pluck('id')->first();
    //         $respond->data    = $latestOrderId += 1;
    //         $respond->message = 'Success getting latest id of order record';

    //     } catch(Exception $ex) {
    //         $respond->data    = false;
    //         $respond->message = 'Problem occured while trying to get latest order record id from database!';
    //     }

    //     return $respond;
    // }

    /**
     * Validate all request data submit from client side by form.
     * 
     * @param String $paymentMethod
     * @param String $amountAfterDiscount
     * @param String $rielCurrencyValue
     * @param String $amountPaidInUsd
     * @param String $amountPaidInRiel
     * @param String $tableSelectionMode
     * 
     * @return RespondObject [ data: result_data, message: result_message ]
     */
    public static function validateRequest( $paymentMethod, $amountAfterDiscount, $rielCurrencyValue, $amountPaidInUsd, $amountPaidInRiel ) 
    {
        $respond = (object)[];

        $isPaymentMethodValid = Checkout::checkValidPaymentMethods($paymentMethod);
        if (!$isPaymentMethodValid) {
            $respond->data = false;
            $respond->message = 'Invalid Payment Methods!';
            return $respond;
        }

        // find for payment option
        $paymentInUsd = 0;
        $paymentInRiel = 0;
        $paymentOptionWorkshift = $amountPaidInUsd - $amountAfterDiscount;
        $changedFromUsdToRiel = self::calculatedChangedAmountInUsdAndRiel( $amountAfterDiscount, $rielCurrencyValue );
        if($amountPaidInUsd == 0 && $amountPaidInRiel != 0){
            $paymentInRiel = $changedFromUsdToRiel->riel_changed_amount;
        }elseif($paymentOptionWorkshift == 0){
            $paymentInUsd = $amountPaidInUsd;
        }elseif($paymentOptionWorkshift > 0){
            $paymentInUsd = $amountAfterDiscount;
        }elseif($paymentOptionWorkshift < 0){
            $paymentInUsd = $amountPaidInUsd;
            $changedUsd = $amountAfterDiscount - $amountPaidInUsd;
            $changedInRial = self::calculatedChangedAmountInUsdAndRiel( $changedUsd, $rielCurrencyValue );
            $paymentInRiel = $changedInRial->riel_changed_amount;
        }

        // // validate payment method
        // $paymentMethodArr = ([
        //     'aba-payment-option'    => 'aba',
        //     'phone-payment-option'  => 'phone_number',
        //     // 'acleda-payment-option' => 'acleda',
        //     'cod-payment-option'    => 'cash-on-delivery',
        // ]);
        // $isPaymentExist = $paymentMethodArr[ $paymentMethod ] ?? null;
        // if( $isPaymentExist == null ) {
        //     $respond->data    = false;
        //     $respond->message = 'Payment method invalid or incorrect provided!';
        // }
        // $paymentMethod = $paymentMethodArr[ $paymentMethod ];
        // dd($paymentMethod);

        // validate currency mode and convert amount after discount based on currency mode.
        // total amount of customer paid in usd [ (riel convert to usd) plus usd amount ]
        $customerPaymentMadeInUsd                = $amountPaidInUsd;
        $customerPaymentMadeInUsdConvertFromRiel = number_format( ($amountPaidInRiel / $rielCurrencyValue), 2);
        $totalCustomerPaymentMadeInUsd           = $customerPaymentMadeInUsd + $customerPaymentMadeInUsdConvertFromRiel;
                
        // final result in total of USD
        $totalChangeValueInUsd   = $totalCustomerPaymentMadeInUsd - $amountAfterDiscount;
        $usdAndRielChangedAmount = self::calculatedChangedAmountInUsdAndRiel( $totalChangeValueInUsd, $rielCurrencyValue );

        $respond->data                    = true;
        $respond->message                 = 'All request are valid';
        $respond->currencyMode            = 'usd';
        $respond->paymentMethod           = $isPaymentMethodValid;
        $respond->paymentInUsd            = $paymentInUsd;
        $respond->paymentInRiel           = $paymentInRiel;
        $respond->totalChangeValueInUsd   = $totalChangeValueInUsd;
        $respond->totalPaymentMadeInUsd   = $totalCustomerPaymentMadeInUsd;
        $respond->usdAndRielChangedAmount = $usdAndRielChangedAmount;

        return $respond;
    }

}