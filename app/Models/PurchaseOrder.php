<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Throwable;

class PurchaseOrder extends Model
{
    use HasFactory;
    
    /**
     * Table name
     * @var String 
     */
    protected $table = 'purchase_orders';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assingable
     * @var Array
     */
    protected $fillable = [
        'date', 
        'reference_no',
        'purchase_officer',
        'tax',
        'other_cost',
        'shipping',
        'attach_document',
        'purchase_order_status',
        'payment_term',
        'payment_status',
        'shipment_status',
        'purchase_order_note',
        'staff_note',
        'grand_total',
        'price_before_discount',
        'supplier_id',
        'biller_id',
        'arrival_date',
        'cash',
        'credit',
        'balance',
    ];

    /**===================
     *  Helper Functions
     *====================*/
    // Purchase Order Helper Functions [BEGIN]
        /**
        * Get all purchase order records form database.
        * @return ResponObject [ data: result_data, message: result_message ]
        */
        protected static function getPurchaseOrders(){
            $respond = (object)[];
    
            try{
                $purchaseOrders    = PurchaseOrder::orderBy('id', 'DESC')->get()->paginate(25);
                $respond->data     = $purchaseOrders;
                $respond->message = 'Purchase order found';
            }catch(Exception $e) {
                $respond->data     = false;
                $respond->message = 'Problem occured while trying to get purchase order from database!';
            }

            return $respond;
        }
        
        /**
         * Get specific purchase order record based on given id form database.
         * @param Integer $id
         * @return ResponObject [ data: result_data, message: result_message ]
         */
        protected static function getPurchaseOrder($id){
            $respond = (object)[];
    
            try{
                $purchaseOrder     = PurchaseOrder::findOrFail($id);
                $document_name     = substr($purchaseOrder->attach_document, 33);
                $purchaseOrder->attach_document = $document_name;
                $respond->data     = $purchaseOrder;
                $respond->message  = 'Record found';
            }catch(ModelNotFoundException $e) {
                $respond->data    = false;
                $respond->message = 'Record not found!';
            }
            return $respond;
        }
    // Purchase Order Helper Functions [END]

    // Supplier Helper Functions [BEING]
        /**
         * Get all supplier records from database
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSuppliers(){
            $respond = (object)[];
            
            try {
                $suppliers = Supplier::all();
                $respond->data    = $suppliers; 
                $respond->message = 'Supplier records found'; 

            } catch(Exception $e) {
                $respond->data    = false; 
                $respond->message = 'Problem occured while trying to get supplier records!'; 
            }

            return $respond;
        }

        /**
         * Get specific supplier record from database
         * @param Integer $id
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSupplier($id){
            $respond = (object)[];
            
            try {
                $supplier = Supplier::findOrFail($id);
                $respond->data    = $supplier; 
                $respond->message = 'Supplier record found'; 

            } catch(ModelNotFoundException $e) {
                $respond->data    = false; 
                $respond->message = 'Supplier record not found!'; 
            }

            return $respond;
        }
    // Supplier Helper Functions [END]

    // Biller Helper Functions [BEGIN]
        /**
         * Get all biller records from database.
         * @return RespondObject [ data: data_result, message: message_result ]
         */
        protected static function getBillers(){
            $respond = (object)[];
            
            try {
                $billers = Biller::all();
                $respond->data    = $billers;
                $respond->message = 'All biller records found';
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get biller records!'; 
            }

            return $respond;
        }

        /**
         * Get specific biller record from database.
         * @param Integer $id
         * @return RespondObject [ data: data_result, message: message_result ]
         */
        protected static function getBiller($id){
            $respond = (object)[];
            
            try {
                $biller = Biller::findOrFail($id);
                $respond->data    = $biller;
                $respond->message = 'Biller records found';
            } catch(ModelNotFoundException $ex) {
                $respond->data    = false;
                $respond->message = 'Biller records not found!'; 
            }

            return $respond;
        }
    // Biller Helper Functions [END] 

    // Product Variant Helper Functions [BEGIN]
        /**
        * Get all product variant records form database.
        * @return ResponObject [ data: result_data, message: result_message ]
        */
        protected static function getProductVariants(){
            $respond = (object)[];
    
            try{
                $productVariants   = ProductVariant::orderBy('id', 'DESC')->get();
                $respond->data     = $productVariants;
                $respond->message = 'Product variants found';
            }catch(Exception $e) {
                $respond->data     = false;
                $respond->message = 'Problem occured while trying to get product variants from database!';
            }

            return $respond;
        }
    // Product Variant Helper Functions [END]

      /**
     * Valida request data.
     * @param Form_Request_Value $purchaseOrderStatus
     * @param Form_Request_Value $paymentStatus
     * @param Form_Request_Value $shipmentStatus
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkRequestValidationStatus($purchaseOrderStatus, $paymentStatus, $shipmentStatus) {
        $respond       = (object)[];
        $respond->data = false;

        // purchase order status
        if($purchaseOrderStatus != 'pending' && $purchaseOrderStatus != 'confirmed' && $purchaseOrderStatus != 'completed' && $purchaseOrderStatus != 'cancelled'){
            return $respond->message = 'Invalided purchase order status!';
        }
        
        // payment status
        if($paymentStatus != 'pending' && $paymentStatus != 'partial' && $paymentStatus != 'paid' && $paymentStatus != 'cancel'){
            return $respond->message = 'Invalided payment status!';
        }

        // shipment status
        if($shipmentStatus != 'pending' && $shipmentStatus != 'delivering' && $shipmentStatus != 'delivered' && $shipmentStatus != 'pickup' && $shipmentStatus != 'cancel'){
            return $respond->message = 'Invalided shipment status!';
        }

        $respond->data                = true;
        $respond->purchaseOrderStatus = $purchaseOrderStatus;
        $respond->paymentStatus       = $paymentStatus;
        $respond->shipmentStatus      = $shipmentStatus;
        $respond->message             = 'All requests valided!';

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
    public static function getProductVariantInJsonFormat( $productVariants ){
        $respond = (object)[];

        $orderOfProductVariants = array();

        try {
            foreach( $productVariants as $productVariant ) {
                array_push( $orderOfProductVariants, (object)[
                    'id'             => $productVariant->id,
                    'name'           => $productVariant->product_name,
                    'price'          => $productVariant->price,
                    'quantity'       => $productVariant->pivot->quantity,
                    'serial_number'  => $productVariant->serial_number,
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
     * ########################
     *      Relationship
     * ########################
     */
    
    /**
     * One purchase order to many payment options.
     * @return App/Model/PurchaseOrderPaymentOptions
     */
    public function purchaseOrderPaymentOptions(){
        return $this->hasMany(
            PurchaseOrderPaymentOptions::class,
            'purchase_order_id',
        );
    }

    /**
     * Many to one relationship with supplier
     * @return App/Models/Supplier
    */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Many to one relationship with biller
     * @return App/Models/Biller
    */
    public function biller()
    {
        return $this->belongsTo(Biller::class);
    }

    /**
     * Many purchase orders to many product variants.
     * @return App/Model/ProductVariant
     */
    public function product_variants()
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'purchase_order_product_variants',
            'purchase_order_id',
            'product_variant_id',
        )->withPivot('quantity');
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
}
