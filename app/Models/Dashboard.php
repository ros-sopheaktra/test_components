<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;
use Throwable;

class Dashboard extends Model
{
    use HasFactory;

    /**
     * ########################
     *      Helper Function
     * ########################
     */

        /**
         * Get all product variant lower of stock records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getProductVariantLowerOfStock(){
            $respond = (object)[];
            $productvariant_colletion = new Collection();
            
            try {
                $shop_pvs = DB::table('product_variant_shops')->get();
                foreach($shop_pvs as $shop_pv){
                    $product_variant = ProductVariant::find($shop_pv->product_variant_id);
                    $alert_quantity = $product_variant->product->alert_quantity;
                    if($shop_pv->quantity <= $alert_quantity){
                        $tmp_pv = (object) [
                            'id'        => $product_variant->id,
                            'sku'       => $product_variant->sku,
                            'qty'       => $shop_pv->quantity,
                            'shop_name' => Shop::find($shop_pv->shop_id)->name,
                        ];
                        $productvariant_colletion->push($tmp_pv);

                    }
                }

                $respond->data =  $productvariant_colletion;             
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Get total price of sales records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getTotalPriceOfSales(){
            $respond = (object)[];

            $date = Carbon::now();
            $totalAllPriceOfSales = 0;
            $totalPriceSalesOfWebsite = 0;
        
            try {
                $orders = Order::whereDate('created_at', $date)->get();
                foreach($orders as $item){
                    if($item->order_status != 'cancelled' && $item->order_status != 'pending'){
                        $totalAllPriceOfSales += $item->grand_total;
                        if($item->sale_type == 'Online'){
                            $totalPriceSalesOfWebsite += $item->grand_total;
                        }
                    }
                }

                $respond->data                     = true;
                $respond->totalAllPriceOfSales     =  $totalAllPriceOfSales;
                $respond->totalPriceSalesOfWebsite =  $totalPriceSalesOfWebsite;
            } catch(Exception | Throwable $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();

            }
            return $respond;
        }

        /**
         * Get total  Daily Products sold  
         * @return RespondObject [data:result_data, massage: result_message ]
         */
        public static function getTotalProductSaled(){
            $respond = (object)[];

            $date = Carbon::now();
            $totalPtoductsaled = 0; 
            try {
                $orders = OrderDetail::whereDate('created_at', $date)->get();
                foreach($orders as $order){
                    $totalPtoductsaled += $order->quantity;
                }

                $respond->data              = false;
                $respond->totalPtoductsaled = $totalPtoductsaled;
                $respond->message           ='Have product saled';

            } catch(Exception | Throwable $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();
            }
            return $respond;
        }

        /**
         * Get padding delivery and payment sales records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getPandding(){
            $respond = (object)[];
            
            try {
                $pandding_delivery = Order::where('delivery_status', 'pending')->get();
                $pandding_payment = Order::where('payment_status', 'pending')->get();

                $respond->data              = true;
                $respond->pandding_delivery =  count($pandding_delivery);
                $respond->pandding_payment  =  count($pandding_payment);
            } catch(Exception | Throwable $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();

            }
            return $respond;
        }

        /**
         * Get padding delivery and payment sales records from database.
         * 
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getLastFiveOrders(){
            $respond = (object)[];
            
            try {
                $orders = Order::orderBy('id', 'desc')->take(5)->get();
                $respond->data = $orders;
            } catch(Exception | Throwable $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();

            }
            return $respond;
        }
}
