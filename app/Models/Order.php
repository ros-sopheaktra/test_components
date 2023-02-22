<?php

namespace App\Models;

use Exception;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'orders';

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
        'order_number',
        'cash',
        'credit',
        'balance',
        'total_cost',
        'total_price',
        'delivery_fee',
        'shop_id',
        'grand_total',
        'sale_tax',
        'sale_type',
        'payment_status',
        'delivery_status',
        'payment_method',
        'order_status',
        'customer_website_id',
        'date',
        'order_discount',
        'reference_no',
        'payment_term',
        'attach_document',
        'sale_note',
        'staff_note',
        'pick_up',
        'biller_id',
        'user_id',
        'payment_deadline',
        'pay_made_in_usd',
        'pay_made_in_reil',
        'has_custom_delivery_charge',
        'delivery_charge_name',
        'delivery_charge_price',
        
    ];

    /**
     * ################################
     *     Modules Helper Functions
     * ################################
     */
        // Order Helper Functions [Begin]
            /**
             * Get all order records from database.
             * @return ObejectRespond [ data: date_result, message: message_result ]
             */
            public static function getOrders(){
                $respond = (object)[];

                try {
                    $orders = Order::all();
                    $respond->data    = $orders;
                    $respond->message = 'All order records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get order records from database!';
                }

                return $respond;
            }

            /**
             * Get specific order record based on given id from database.
             * @param Integer $id
             * @return ObejectRespond [ data: date_result, message: message_result ]
             */
            public static function getOrder($id){
                $respond = (object)[];

                try {
                    $user_online = DefaultUserDownloadPdf::findOrFail(1);
                    $user_online_name = User::findOrFail($user_online->user_id);
                    $order = Order::findOrFail($id);
                    $respond->data            = $order;
                    $respond->shop_id         = $order->shop_id;
                    $respond->username_online = $user_online_name->firstname .' '. $user_online_name->lastname;
                    $respond->message         = 'Order record found';
                } catch(ModelNotFoundException $ex) {
                    $respond->data    = false;
                    $respond->message = 'Order record not found!';
                }

                return $respond;
            }
        // Order Helper Functions [END]

        // Order Detail Helper Functions [BEING]
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
                    $allBankAccounts = BankAccount::all();
                    foreach($bankAccounts as $bankAccount){
                        if($bankAccount->is_show_in_pos_invoice){
                            $invoice = true;
                        }
                        if($bankAccount->is_show_in_pos_receipt){
                            $receipt = true;
                        }
                    }
                    $respond->data = $bankAccounts;
                    $respond->allBankAccounts = $allBankAccounts;
                    $respond->invoice = $invoice;
                    $respond->receipt = $receipt;
                    $respond->message = 'Records found';
                } catch(Exception $e) {
                    $respond->data = false;
                    $respond->message = $e->getMessage();
                }

                return $respond;
            }

            /**
             * Count order of invoice and return the name of invoice
             * @param int $id
             * @param date $date
             * @return ObejectRespond [ data: date_result, message: message_result ]
            */
            public function orderNumberInvoices($id, $date){
                $respond = (object)[];

                try {
                    $number = 0;
                    if($id != 0){
                        $order_by_dates = Order::query()
                                        ->whereDate('created_at', '>=', $date)->get();
                        foreach($order_by_dates as $key => $order_by_date){
                            if($order_by_date->id == $id){
                                $number = $key;
                            }
                        }
                    }

                    $name_invoice = $date->format('dmY').'-'.sprintf("%03d", $number+1);
                    $respond->data = $name_invoice;
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;
            }
                
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
            public static function validateRequest($amountAfterDiscount, $rielCurrencyValue, $amountPaidInUsd, $amountPaidInRiel ) 
            {
                $respond = (object)[];
                $customerPaymentMadeInUsd                = $amountPaidInUsd;
                $customerPaymentMadeInUsdConvertFromRiel = number_format( ($amountPaidInRiel / $rielCurrencyValue), 2);
                $totalCustomerPaymentMadeInUsd           = $customerPaymentMadeInUsd + $customerPaymentMadeInUsdConvertFromRiel;
                        
                // final result in total of USD
                $totalChangeValueInUsd   = $totalCustomerPaymentMadeInUsd - $amountAfterDiscount;
                $usdAndRielChangedAmount = self::calculatedChangedAmountInUsdAndRiel( $totalChangeValueInUsd, $rielCurrencyValue );

                $respond->data                    = true;
                $respond->message                 = 'All request are valid';
                $respond->currencyMode            = 'usd';
                $respond->totalChangeValueInUsd   = $totalChangeValueInUsd;
                $respond->totalPaymentMadeInUsd   = $totalCustomerPaymentMadeInUsd;
                $respond->usdAndRielChangedAmount = $usdAndRielChangedAmount;

                return $respond;
            }

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
        // Order Detail Helper Functions [END]

        // Website Customer Function [BEGIN]
            /**
             * Get all website customer records from database.
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getWebsiteCustomers(){
                $respond = (object)[];

                try {
                    $website_cus      = CustomerWebsite::all();
                    $respond->data    = $website_cus;
                    $respond->message = 'All product records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product records!';
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
        // Website Customer Function [END]

        // Over Sales Function [BEING]
            /**
             * Get over sales form db base on given id
            * @param int $id
            * @return ResponObject [ data: result_data, messange:result_messange ]
            */
            protected static function getOverSale($id){
                $respond = (object)[];
        
                try{
                    $oversales = OverSales::findOrFail($id);
                    $respond->data = $oversales;
                    $respond->messange = 'Over Sales record found';
                }catch(ModelNotFoundException $e) {
                    $respond->data = false;
                    $respond->messange = $e->getMessage();
                }
                return $respond;
            } 
        // Over Sales Function [ENG]
        
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

        // Product Variant Helper Function [BEING]
            /**
             * Get all product variant records from database.
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProductVariants(){
                $respond = (object)[];
            
                try {
                    $productvariants = ProductVariant::all();
                    $respond->data    = $productvariants;
                    $respond->message = 'All product records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Get all product variant records from database.
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getProductVariant($id){
                $respond = (object)[];
                $pvCollections = new Collection();

                // get shop
                $shop = Shop::getShop($id);
                if(!$shop->data){
                    return back()->with('error', $shop->message);
                }
                $shop = $shop->data;

                try {
                    $productvariants  =  $shop->product_variants;
                    foreach($productvariants as $productvariant){
                        $pv_qty = DB::table('product_variant_shops')
                        ->where('product_variant_id', $productvariant->id)
                        ->where('shop_id', $id)
                        ->first();
                        $pv = (object)[
                            'id'            => $productvariant->id,
                            'name'          => $productvariant->serial_number.' '.$productvariant->product_name,
                            'price'         => $productvariant->price,
                            'cost'          => $productvariant->cost,
                            'quantity'      => $pv_qty->quantity,
                            'serial_number' => $productvariant->serial_number,
                            'brand'         => $productvariant->product->brand == null ? 'No brand' : $productvariant->product->brand->name,
                            'unit'          => $productvariant->product->productUnit == null ? 'No Unit' : $productvariant->product->productUnit->title,
                        ];
                        $pvCollections->push($pv);
                    }

                    $respond->data    = $pvCollections;
                    $respond->message = 'All product records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;
            }

            /**
             * Get location of product variant records from database.
             * @return ObjectRespond [ data: data_result, message: message_result ]
             */
            public static function getOrderDetailsOPv($order){
                $respond = (object)[];
                $orderDetailCollections = new Collection();
                $totalQtyOfProduct = 0;

                try {
                    $orderDetails = $order->orderDetails;
                    foreach($orderDetails as $orderDetail){
                        $tempProductvariantPivots = DB::table('product_variant_shops')
                        ->where('shop_id', $order->shop_id)
                        ->where('product_variant_id', $orderDetail->product_variant_id)
                        ->first();
                        $pv = (object)[
                            'id'            => $orderDetail->id,
                            'thumbnail'     => $orderDetail->thumbnail,
                            'description'   => $orderDetail->detail_invoice != null ?  $orderDetail->detail_invoice :  $orderDetail->name,
                            'serial_number' => $orderDetail->productVariant->serial_number,
                            'quantity'      => $orderDetail->quantity,
                            'price'         => $orderDetail->price,
                            'discount'      => $orderDetail->discount,
                            'subTotal'      => $orderDetail->price - (($orderDetail->price * $orderDetail->discount) / 100),
                            'location'      => $tempProductvariantPivots->location,
                            'brand'         => $orderDetail->productVariant->product->brand->name,
                            'unit'          => $orderDetail->productVariant->product->productUnit->title,
                        ];
                        $orderDetailCollections->push($pv);
                        $totalQtyOfProduct += $orderDetail->quantity;
                    }
                    
                    $respond->data              = $orderDetailCollections;
                    $respond->customer_mode     = $order->customer_website == null ? 'Guest' : $order->customer_website->user->username;
                    $respond->location_shop     = Shop::findOrFail($order->shop_id)->address;
                    $respond->totalQtyOfProduct = $totalQtyOfProduct;
                    $respond->message = 'All order detail records found';
                } catch(Exception $ex) {
                    $respond->data    = false;
                    $respond->message = $ex->getMessage();
                }

                return $respond;
            }
            
            /**
             * Loop name of product to add space in pdf
             * @return String $result
            */
            // public static function addSpaceToOrderDetailNamePdf($text){
            //    $result = '';
            //    $number_of_text = str_split($text);
            //    $count = count($number_of_text);
            //    $number = 0;
            //    if($count > 33){
            //         foreach($number_of_text as $key => $item){
            //             if($number == 33){
            //                 $result .= ' ';
            //                 $number = 0;
            //             }
            //             $result .= $item;
            //             $number++;
            //         }
            //         return $result;
            //    }else{
            //        return $text;
            //    }
            // }
        // Product Variant Helper Function [END]

        // Download receipt Helper Function [BEING]
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
        // Download receipt Helper Function [END]

    /**
     * ########################
     *     Helper Functions
     * ########################
     */
        /**
         * Update quantity of product variant
         * @param int $pv_id
         * @param int $qty
         * @return nothing
         */
        public function updateQty($pv_id, $qty, $shop_id)
        {      
            DB::table('product_variant_shops')
                ->where('shop_id', $shop_id)
                ->where('product_variant_id', $pv_id)
                ->update(array('quantity' =>  $qty));
        }

        /**
         * Get quantity of product variant
         * @param int $qty
         */
        public static function getQtyOfPv(ProductVariant $productVariant, int $shop_id)
        {    
            $respond = (object)[];
            $pvs = DB::table('product_variant_shops')
            ->where('product_variant_id', $productVariant->id)
            ->where('shop_id', $shop_id)
            ->get()->first();
            $respond->qty = $pvs->quantity;
            $respond->pv = $pvs;

            return $respond;
        }

        /**
         * sync order stock quantity 
         * @return null
         * @return array $errorMessages
         */
        public function syncStock()
        {        
            $orderDetails = $this->orderDetails()->with('productVariant')->get();
            $errorMsg = '';
            
            //sync quantity
            foreach ($orderDetails as $item) {
                $productVariant = $item->productVariant;
                $pv = $this->getQtyOfPv($productVariant, $item->order->shop_id);
                if (empty($productVariant)) { // empty quantity 
                    $errorMsg = "Product Variant ({$item->name}) Not found Or Out of stocks";
                } else if ($pv->qty < $item->quantity) { // oversale  
                    $errorMsg = "Insufficient product({$item->name}) in stock. " . $pv->qty . ' in stock and ' . $item->quantity . ' was ordered';
                }
            }

            return $errorMsg;
        }

        /**
         * mark order as cancelled
         * @return boolean
         */
        public function markAsCancelled()
        {
            $this->order_status    = 'cancelled';
            $this->payment_status  = 'cancel';
            $this->delivery_status = 'cancel';

            return $this->update();
        }

        /**
         * mark order as completed
         * @return boolean
         */
        public function markAsCompleted()
        {
            $this->order_status    = 'completed';
            // $this->payment_status  = 'cash';
            // $this->delivery_status = 'delivered';

            return $this->update();
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
     * #####################
     *     Relationships
     * #####################
     */
        /**
         * Many orders to many product variants.
         * @return App/Model/ProductVariant
         */
        public function product_variants()
        {
            return $this->belongsToMany(
                ProductVariant::class,
                'order_details',
                'order_id',
                'product_variant_id',
            )->withPivot('name', 'thumbnail', 'quantity', 'return_quantity', 'cost', 'price');
        }

        /**
         * One to many relationship with order detail
         * @return App/Model/OrderDetail
         */
        public function orderDetails()
        {
            return $this->hasMany(
                OrderDetail::class, 
                'order_id', 
                'id'
            );
        }

        /**
         * One to one relationship with shipping detail
         * @return App/Models/ShippingDetail
        */
        public function shippingDetail()
        {
            return $this->hasOne(
                ShippingDetail::class, 
                'order_id', 
                'id'
            );
        }

        /**
         * One order to one guest relationship.
         * @return App\Models\Guest
         */
        public function guest(){
            return $this->hasOne(
                Guests::class,
                'order_id',
            );
        }

        /**
         * One to one relationship with invoice
         * @return App/Models/Invoice
        */
        public function invoice()
        {
            return $this->hasOne(Invoice::class, 
            'order_id', 
            'id'
            );
        }
        
        /**
         * Many to one relationship with customer
         * @return App/Models/CustomerWebsite
        */
        public function customer_website()
        {
            return $this->belongsTo(
                CustomerWebsite::class
            );
        }

        /**
         * Many to one relationship with user
         * @return App/Models/User
        */
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        /**
         * One order to many payment options.
         * @return App/Model/PaymentOptions
         */
        public function paymentOptions(){
            return $this->hasMany(
                PaymentOptions::class,
                'order_id',
            );
        }

        /**
         * One to one relationship with delivery
         * @return App/Model/Delivery
         */
        public function delivery(){
            return $this->hasOne(Delivery::class);
        }

        /**
         * Many to one relation with sales return.
         * @return App/Model/Orders
         */
        public function sales_returns(){
            return $this->hasMany(SalesReturn::class);
        }

        /**
         * One order to many miscellaneous charges.
         * @return App\Models\MiscellaneousCharge
         */
        public function miscellaneousCharges(){
            return $this->hasMany(
                MiscellaneousCharge::class,
                'order_id',
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
}
