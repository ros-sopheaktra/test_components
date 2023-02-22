<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TemporaryOrder extends Model
{
    use HasFactory;

    /**
     * Table names.
     * @var String
     */
    protected $table = 'temporary_orders';

    /**
     * Primary key.
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The Attribute that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'total',
        'customer_website_id',
        'delivery_charge_name',
        'delivery_charge_price',
        'has_custom_delivery_charge',
        'order_id',
        'guest_id',
    ];

    /**
     * ###################################
     *      Modules Helper Functions
     * ###################################
     */

    // Temporary Order Helper Functions [BEGIN]
        /**
         * Get all temporary order records from database.
         * @return ObjectRespond [ data: data_result, message: resul_message ]
         */
        public static function getTemporaryOrders() {
            $respond = (object)[];
            $temporaryDetails = new Collection();

            try {
                $temporaryOrders  = TemporaryOrder::all();
                foreach($temporaryOrders as $temporaryOrder){
                    $numberOfOrders = $temporaryOrder->temporaryOrderDetails->sum('order_quantity');
                    $temporaryOrder->has_custom_delivery_charge ? 
                        $numberOfOrders += 1 : 
                        $numberOfOrders ;

                    // get miscellaneous charge temporary order details if exist
                    $temporaryMiscOrderDetails = self::getTemporaryMicsOrderDetails( $temporaryOrder );
                    if( !$temporaryMiscOrderDetails->data ){
                        return $temporaryMiscOrderDetails;
                    }
                    $temporaryMiscOrderDetails = $temporaryMiscOrderDetails->data;
                    foreach( $temporaryMiscOrderDetails as $temporaryMiscOrderDetail ){
                        $numberOfOrders += $temporaryMiscOrderDetail['qtyOrdered'];
                    }

                    // if guest exist
                    if ($temporaryOrder->guest_id != null) {
                        $customer_id   = 1;
                        $isGuest       = 1;
                        $point         = 0;
                        $discount      = 0;
                        $customer_name = $temporaryOrder->guest->name;
                        $phone         = $temporaryOrder->guest->phone;
                    } else {
                        $isGuest       = 0;
                        $customer_id   = $temporaryOrder->customer_website->id;
                        $point         = $temporaryOrder->customer_website->point;
                        $discount      = $temporaryOrder->customer_website->cusgroup->discount;
                        $customer_name = $temporaryOrder->customer_website->user->username;
                        $phone         = $temporaryOrder->customer_website->phone;
                    }
                    //guest json
                    $guestData = (object)[
                        'guestName'  => $customer_name,
                        'guestPhone' => $phone,
                    ];
                        
                    $temp = (object)[
                        'id'                   => $temporaryOrder->id,
                        'order_id'             => $temporaryOrder->order_id == null ? 0 : $temporaryOrder->order->id,
                        'date'                 => date('d/m/y h:i A', strtotime($temporaryOrder->created_at)),
                        'point'                => $point,
                        'total'                => $temporaryOrder->total,
                        'discount'             => $discount,
                        'number_of_pv'         => $numberOfOrders,
                        'customer_name'        => $customer_name,
                        'customer_id'          => $customer_id,
                        'isGuest'              => $isGuest,
                        'jsonGuest'            => json_encode($guestData),
                        'product_variants'     => self::getTemporaryOrderDetail($temporaryOrder->temporaryOrderDetails),
                        'hasDeliveryCharge'    => $temporaryOrder->has_custom_delivery_charge,
                        'deliveryChargeName'   => $temporaryOrder->delivery_charge_name,
                        'deliveryChargePrice'  => $temporaryOrder->delivery_charge_price,
                        'miscellaneousCharges' => $temporaryMiscOrderDetails,
                    ];
                    $temporaryDetails->push($temp);
                }

                $respond->data    = $temporaryDetails;
                $respond->message = 'Successful getting all temporary order records';
            } catch( Exception $ex ) {
                $respond->data    = false;
                $respond->messaeg = 'Problem occured while trying to get temporary order records from database!';
            }

            return $respond;
        }

        /**
         * Get all temporary order records from database.
         * @return ObjectRespond [ data: data_result, message: resul_message ]
         */
        public static function getTemporaryOrderDetail($temporaryDetails) {
            $temporaryDetailPv = new Collection();
            foreach($temporaryDetails as $temporaryDetail){
                $temp = (object)[
                    'pvId'        => $temporaryDetail->productvariant->id,
                    'name'        => $temporaryDetail->productvariant->sku,
                    'price'       => $temporaryDetail->productvariant->price,
                    'discount'    => $temporaryDetail->discount,
                    'qtyOrdered'  => $temporaryDetail->order_quantity,
                ];
                $temporaryDetailPv->push($temp);
            }
            // dd($temporaryDetailPv);

            return $temporaryDetailPv;
        }

        /**
         * Get all temporary miscellaneous charge order detail records 
         * co-responding to current passed temporary order paramter if exist.
         * @param App\Models\TemporaryOrder $temporaryOrder
         * 
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function getTemporaryMicsOrderDetails( $temporaryOrder ){
            $respond = (object)[];

            try {
                $temporaryMiscOrderDetails = new Collection();

                if( count( $temporaryOrder->temporaryMiscellaneousOrderDetails ) > 0 ){
                    foreach( $temporaryOrder->temporaryMiscellaneousOrderDetails as $key => $temporaryMiscOrderDetail ){
                        $miscOrderDetail = new Collection([
                            'name'        => $temporaryMiscOrderDetail->name,
                            'price'       => $temporaryMiscOrderDetail->price,
                            'discount'    => 0,
                            'qtyOrdered'  => $temporaryMiscOrderDetail->order_quantity,
                        ]);

                        $temporaryMiscOrderDetails->push( $miscOrderDetail );
                    }
                }

                $respond->data     = $temporaryMiscOrderDetails;
                $respond->message = 'Successful getting temporary miscellaneous order detail records from database';
            } catch( Exception | ModelNotFoundException $ex ) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get temporary miscellaneous charge order detail from database!';
            }

            return $respond;
        }

        /**
         * Get specific temporary order record based on given
         * id paramter from database.
         * @param Integer $id
         * @return ObjectRespond [ data: data_result, message: resul_message ]
         */
        public static function getTemporaryOrder( $id ) {
            $respond = (object)[];

            try {
                $temporaryOrder   = TemporaryOrder::findOrFail( $id );
                $respond->data    = $temporaryOrder;
                $respond->message = 'Temporary order record found';
            } catch( ModelNotFoundException $ex ) {
                $respond->data    = false;
                $respond->messaeg = 'Temporary order record not found!';
            }

            return $respond;
        }
    // Temporary Order Helper Functions [END]

    // Product Variant Helper Functions [BEGIN]
        /**
         * Get specific product variant record based on given id
         * parameter from database.
         * @param Integer $id
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function getProductVariant( $id ) {
            $respond = (object)[];

            try {
                $productVariant   = ProductVariant::findOrFail( $id );
                $respond->data    = $productVariant;
                $respond->message = 'Product variant record found';
            } catch( ModelNotFoundException $ex ) {
                $respond->data    = false;
                $respond->message = 'Product variant record not found!';
            }

            return $respond;
        }
    // Product Variant Helper Functions [END]

    /**
     * ######################
     *     Relationships
     * ######################
     */

    /**
     * One temporary order to many temporary order details.
     * @return App\Model\TemporaryOrderDetail
     */
    public function temporaryOrderDetails(){
        return $this->hasMany(
            TemporaryOrderDetail::class,
            'temporary_order_id',
        );
    }

    /**
     * One temporary order to many temporary miscellaneous order details.
     * @return App\Model\TemporaryMiscellaneousOrderDetail
     */
    public function temporaryMiscellaneousOrderDetails(){
        return $this->hasMany(
            TemporaryMiscellaneousOrderDetail::class,
            'temporary_order_id',
        );
    }

    /**
     * One to many relationship with customer website
     * @return App\Model\CustomerWebsite
     */
    public function customer_website(){
        return $this->belongsTo(
            CustomerWebsite::class,
            'customer_website_id',
        );
    }

    /**
     * One to one relationship with order
     * @return App\Model\Order
     */
    public function order(){
        return $this->belongsTo(
            Order::class,
            'order_id',
        );
    }
    /**
     * one to one relationship with guest 
     * @return App\Modle\Guests
     */
    public function guest(){
        return $this->belongsTo(
            Guests::class,
            'guest_id',
        );
    }
}
