<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Throwable;

class Report extends Model
{
    use HasFactory;

    // Report Helper Functions [BEGIN]

        /**
         * Get all category records from database.
         * @return ObjectRespond 
        */
        public static function getCategories(){
            $respond = (object)[];
            
            try {
                $categories       = Category::whereNotIn('name', ['BY BRAND'])->get();
                $respond->data    = $categories; 
                $respond->message = 'Category records found!'; 
            } catch(ModelNotFoundException $e) {
                $respond->data    = false; 
                $respond->message = 'Problem while tying to get category records!'; 
            };

            return $respond;
        }

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
         * Filter product variants from order detail based on given specific date or 
         * date in between from database.
         * 
         * @param Date   $data
         * @param Date   $startDate
         * @param Date   $endDate
         * @param String $mode
         * @return ObjectRespond [ data: data_result, message: message_result ]
         */
        public static function reportsFilterByDate($date, $startDate, $endDate, $mode) {
            $respond = (object)[];
            
            // dialy pv ordered
            if ( $mode == 'Daily' ) {
                try {
                    $orderDetails = OrderDetail::whereDate('created_at', $date)->get();
                    $orders = Order::whereDate('created_at', $date)
                            ->where('order_status', '!=', 'cancelled')
                            ->where('order_status', '!=', 'pending')
                            ->get();

                } catch(Exception | Throwable $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to filter report based on given date!';

                    return $respond;
                }

            }elseif( $mode == 'Weekly - Monthly' ) {
                try {
                    $orderDetails = OrderDetail::query()
                        ->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate)
                        ->get();
                    $orders = Order::query()
                        ->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate)
                        ->where('order_status', '!=', 'cancelled')
                        ->where('order_status', '!=', 'pending')
                        ->get();

                } catch(Exception | Throwable $ex) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to filter report based on given between two date!';

                    return $respond;
                }

            } else {
                $orderDetails = new Collection();
            }
            
            $calculatePvs = self::calculate($orderDetails, $orders);

            $respond->data                     = true;
            $respond->pvOrderdetail            = $calculatePvs->pvOrderColection;
            $respond->pvOrcerCollectionByBrand = $calculatePvs->pvOrcerCollectionByBrand;
            $respond->calculatePvs             = $calculatePvs->calculatePvOrders;
            $respond->message                  = 'Successed getting product variants';

            return $respond;
        }

        /**
         * Calculate cost, revenue and profit based on data inside order details collection.
         * @param OrderDetail_Model_Collection $orderDetails
         * @return Collection $calculatePvOrders
         */
        public static function calculate($orderDetails, $orders){
            $respond = (object)[];

            $calculatePvOrders        = new Collection();
            $pvOrderColection         = new Collection();
            $addPvOrders              = new Collection();
            
            // get category
            $categories = self::getCategories();
            if(!$categories->data){
                return back()->with('error', $categories->message);
            }
            $categories = $categories->data;

            // get all brand
            $productBrands = self::getProductBrands();
            if(!$productBrands->data){
                return view('dashboard/index')
                    ->with('error', $productBrands->message);
            }
            $productBrands = $productBrands->data;

            // miscellaneous service charge
            $miscellaneousQuantities = 0;
            $miscellaneousProfit = 0;
            $miscellaneousTotalPrice = 0;
            $miscellaneousTotalCost = 0;

            // calulate cost, price, revenew of pv
            foreach($categories as $category){
                $quantities = 0;
                $totalPrice = 0;
                $totalCost  = 0;
                $pvOrderedBasedCatName = new Collection();
                foreach ( $orderDetails as $orderDetail ) {
                    if($orderDetail->order->order_status != 'cancelled' && $orderDetail->order->order_status != 'pending'){
                        if($orderDetail->product_variant_id != null || $orderDetail->product_variant_id != ''){
                            $productvaraint = $orderDetail->productVariant;                    
                            $categoryName = $productvaraint->product->category->name;
                            if ( $categoryName == $category->name ) {
                                $quantities   += $orderDetail->quantity;
                                $priceiscount = (($orderDetail->quantity * $orderDetail->price) * $orderDetail->discount) / 100;
                                $totalPrice   += ($orderDetail->quantity * $orderDetail->price) - $priceiscount;
                                $totalCost    += $orderDetail->quantity * $orderDetail->cost;
        
                                // create object for pv ordered of each category 
                                $tmpPv = (object) [
                                    'id'       => $productvaraint->id,
                                    'name'     => $orderDetail->name,
                                    'quantity' => $orderDetail->quantity,
                                    'brand'    => $productvaraint->product->brand->name,
                                ]; 
                                $pvOrderedBasedCatName->push($tmpPv);
                                $addPvOrders->push($tmpPv);
                            }
                        }else{
                            // if user delete product variant so we convert it to miscellaneous
                            $miscellaneousQuantities += $orderDetail->quantity;
                            $priceiscount = (($orderDetail->quantity * $orderDetail->price) * $orderDetail->discount) / 100;
                            $miscellaneousTotalPrice += ($orderDetail->quantity * $orderDetail->price) - $priceiscount;
                            $miscellaneousTotalCost  += $orderDetail->quantity * $orderDetail->cost;
                            $miscellaneousProfit     += $miscellaneousTotalPrice - $miscellaneousTotalCost;
                        }
                    }
                }

                // push object
                $tpm = (object) [
                    'name'       => 'Total '.$category->name.' Sales',
                    'quantities' => $quantities,
                    'totalprice' => $totalPrice,
                    'totalcost'  => $totalCost,
                    'profit'     => $totalPrice - $totalCost,
                ]; 
                $calculatePvOrders->push($tpm);

                // sum ordered quantity
                $pvOrderedBasedCatName->groupBy('id')->flatMap( function ($items) {
                    $quantity = $items->sum('quantity');
                    
                    return $items->map( function ($item) use ($quantity) {
                        $item->quantity = $quantity;
                        return $item;
                    });
                    
                });
                // create object for pv ordered of each category 
                $tmpcat = (object) [
                    'id'              => $category->id,
                    'name'            => $category->name,
                    'productvariants' => $pvOrderedBasedCatName->unique('id'),
                ]; 
                $pvOrderColection->push($tmpcat);
            }

            // get product by brand
            $pvOrcerCollectionByBrand = new Collection();
            foreach($productBrands as $productBrand){
                $byBrand = new Collection();
                foreach($addPvOrders as $addPvOrder){
                    if($addPvOrder->brand == $productBrand->name){
                        $byBrand->push($addPvOrder);
                    }
                }
                // sum ordered quantity
                $byBrand->groupBy('id')->flatMap( function ($items) {
                    $quantity = $items->sum('quantity');
                    
                    return $items->map( function ($item) use ($quantity) {
                        $item->quantity = $quantity;
                        return $item;
                    });
                    
                });
                // create object for pv ordered of each category 
                $tmpbrand = (object) [
                    'id'              => $productBrand->id,
                    'name'            => $productBrand->name,
                    'productvariants' => $byBrand->unique('id'),
                ]; 
                $pvOrcerCollectionByBrand->push($tmpbrand);
            }

            // delovery and miscellaneous
            $deliveryProfit = 0;
            $deliveryQuantities = 0;
            foreach($orders as $order){
                $miscellaneousCharges = $order->miscellaneousCharges;
                foreach($miscellaneousCharges as $miscellaneousCharge){
                    $miscellaneousQuantities += $miscellaneousCharge->quantity;
                    $miscellaneousProfit     += ($miscellaneousCharge->price * $miscellaneousCharge->quantity);
                }

                // delivery
                if($order->delivery_fee != 0){
                    $deliveryQuantities++;
                    $deliveryProfit += $order->delivery_fee;
                }
            }

            // push miscellaneous
            $tpmMiscellaneous = (object) [
                'name'       => 'Total Miscellaneous',
                'quantities' => $miscellaneousQuantities,
                'totalprice' => $miscellaneousProfit,
                'totalcost'  => 0,
                'profit'     => $miscellaneousProfit,
            ]; 
            $calculatePvOrders->push($tpmMiscellaneous);

            // delivery
            $tpmDelivery = (object) [
                'name'       => 'Total Deliveries',
                'quantities' => $deliveryQuantities,
                'totalprice' => $deliveryProfit,
                'totalcost'  => 0,
                'profit'     => $deliveryProfit,
            ]; 
            $calculatePvOrders->push($tpmDelivery);

            $respond->calculatePvOrders        = $calculatePvOrders;
            $respond->pvOrderColection         = $pvOrderColection;
            $respond->pvOrcerCollectionByBrand = $pvOrcerCollectionByBrand;
            return $respond;
        }

        /**
         * Filter report by customer name or customer contact.
         * @param String $customerName
         * @param String $customerContact
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function reportFilterByCustomer($customerName, $customerContact){
            $respond = (object)[];

            try {
                // query shipping detail based on given customer name or contact
                $shippingDetails = ShippingDetail::where('name', '=', $customerName)
                                            ->orWhere('contact', '=', $customerContact)
                                            ->get();

                $orderDetailsCollection = new Collection();
                $ordersCollection = new Collection();
                if( count($shippingDetails) > 0 ) {
                    // bind shipping detail to orderDetail                     
                    foreach($shippingDetails as $shippingDetail){
                        $tmpOrderDetails = $shippingDetail->order->orderDetails;
                        if($shippingDetail->order->order_status != 'cancelled' && $shippingDetail->order->order_status != 'pending'){
                            $ordersCollection->push($shippingDetail->order);
                        }
                        foreach($tmpOrderDetails as $orderDetail){
                            if($orderDetail->order->order_status != 'cancelled' && $orderDetail->order->order_status != 'pending'){
                                $orderDetailsCollection->push($orderDetail);
                            }
                        }
                    }

                    // getting all possible order product variants
                    $calculatePvs = self::calculate($orderDetailsCollection, $ordersCollection);

                    $respond->data                     = true;
                    $respond->pvOrderdetail            = $calculatePvs->pvOrderColection;
                    $respond->pvOrcerCollectionByBrand = $calculatePvs->pvOrcerCollectionByBrand;
                    $respond->calculatePvs             = $calculatePvs->calculatePvOrders;
                    $respond->message                  = 'Successed getting product variants';
                    
                    return $respond;

                } else {
                    $respond->data    = false;
                    $respond->message = 'No record found';
                    return $respond;
                }

            } catch (Exception $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();
                return $respond;
            }
        }

        /**
         * Filter report by customer name or customer contact.
         * @param String $customerName
         * @param String $customerContact
         * @return ObjectRespond [ data: data_result, message: result_message ]
         */
        public static function reportFilterBySalesDetail($startdate, $enddate){
            $respond = (object)[];

            try {
                $orders = Order::query()
                    ->whereDate('created_at', '>=', $startdate)
                    ->whereDate('created_at', '<=', $enddate)
                    ->where('order_status', '!=', 'cancelled')
                    ->where('order_status', '!=', 'pending')
                    ->get();

                $respond->data = $orders;
            } catch(Exception | Throwable $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();
            }
            return $respond;
        }

    // Report Helper Functions [END]
}
