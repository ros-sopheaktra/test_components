<?php

namespace App\Models;

use App\Models\CheckDeadLineNotification as ModelsCheckDeadLineNotification;

use Carbon\Carbon;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class CheckDeadLineNotification extends Model
{
    use HasFactory;

    /**
     * Get notification record from database based given option.
     * @param String $option [ after, before ]
     * @return RespondObject [ data:date_result, message: result_message ]
     */
    protected static function getNotification($option){
        $respond = (object)[];

        // valid parameter
        if ($option != 'before' && $option != 'after') {
            $respond->data    = false;
            $respond->message = 'Invalid option value, accept only [before or after] '.$option.' is passed!';
            return $respond;
        }

        // get notification based on option
        try {
            $notification = Notification::where('notification_status', $option)->first();
            $respond->data = $notification;
            $respond->message = 'Query found end, record found';
        } catch(QueryException $queryEx) {
            $respond->data = false;
            $respond->message = $queryEx->getMessage();
        }

        return $respond;
    }

    /**
     * Check given model deadline date, notify any record that has deadline date 
     * less than date on notification model.
     * [ before notification deadline condition ]
     * @param ModelCollection $modelCollection
     * @return ModelCollection $respondNotifications
     */
    public static function checkDeadline($modelCollection){
        $currentDate = Carbon::now();

        // get notification record
        $notification = ModelsCheckDeadLineNotification::getNotification('before');
        if(!$notification->data){
            return $notification->message;
        }
        $notification = $notification->data;

        // confirm models deadline with notification
        $respondNotifications = new Collection();
        foreach($modelCollection as $data){
            if( $data ->type === 'stockQuantity' ){
                $respondNotifications->push((object)[
                    'id'                 => $data->id ,
                    'shopName'           => $data->referenceNumber,
                    'productVariantName' => $data->status,
                    'stockQuantities'    => $data->remainDays,
                    'type'               => 'quantities',
                    'colorCode'          => '#ff0000',
            ]);
            } else {
                $tempDataDeadline = $data->deadline_at;
                // compare two dates
                $deadLineDateRemain = $currentDate->diffInDays($tempDataDeadline);
                
                if( $deadLineDateRemain <= $notification->days ) {
                    $tempModelObject = (object)[
                        'statusType'      => 'status',
                        'id'              => $data->id,
                        'type'            => $data->type,
                        'overdue'         => '(' .$data->created_at->diffForHumans().')',
                        'status'          => $data->type === 'sales' ? $data->order_status : $data->status,
                        'referenceNumber' => $data->type === 'sales' ? $data->reference_no : $data->reference_number,
                        'colorCode'       => $notification->color_code,
                        'remainDays'      => $deadLineDateRemain == 0 ? 'today' : ($deadLineDateRemain.' days'),
                    ];
                    $respondNotifications->push($tempModelObject);
                }
            }
        }

        return $respondNotifications;
    }

    /**
     * Check given model deadline date, notify any record that has deadline date 
     * less than date on notification model.
     * [ after notification deadline condition apply for model with payment status ]
     * @param ModelCollection $modelCollection
     * @return ModelCollection $respondNotifications
     */
    public static function checkPaymentDeadline($modelCollection){
        $currentDate = Carbon::now();
        
        // get notification record
        $notification = ModelsCheckDeadLineNotification::getNotification('after');
        if(!$notification->data){
            return $notification->message;
        }
        $notification = $notification->data;

        // confirm models deadline with notification
        $respondNotifications = new Collection();
        foreach($modelCollection as $data){
            $tempPaymentDataDeadline = $data->payment_deadline_at;
            // compare two dates
            $deadLineDateRemain = $currentDate->diffInDays($tempPaymentDataDeadline);
            
            if( $deadLineDateRemain <= $notification->days ) {
                $tempModelObject = (object)[
                    'id'              => $data->id,
                    'type'            => $data->type,
                    'statusType'      => 'payment status',
                    'status'          => $data->payment_status,
                    'referenceNumber' => $data->reference_number,
                    'colorCode'       => $notification->color_code,
                    'remainDays'      => $deadLineDateRemain == 0 ? 'today' : ($deadLineDateRemain.' days'),
                ];
                $respondNotifications->push($tempModelObject);
            }

        }
        
        return $respondNotifications;
    }

    /**
     * Check given model deadline date, notify any record that has deadline date 
     * less than date on notification model.
     * @param ModelCollection $modelCollection
     * @return ModelCollection $respondNotifications
     */
    public static function checkShipmentDeadLine($modelCollection){
        $currentDate = Carbon::now();

        // get notification record
        $notification = ModelsCheckDeadLineNotification::getNotification('before');
        if(!$notification->data){
            return $notification->message;
        }
        $notification = $notification->data;

        // confirm models deadline with notification
        $respondNotifications = new Collection();
        foreach($modelCollection as $data){
            $tempDataDeadline = $data->expected_arrival_date;

            // compare two dates
            $deadLineDateRemain = $currentDate->diffInDays($tempDataDeadline);

            if( $deadLineDateRemain <= $notification->days ) {
                $tempModelObject = (object)[
                    'statusType'      => 'shipment status',
                    'id'              => $data->id,
                    'type'            => $data->type,
                    'status'          => $data->shipment_status,
                    'referenceNumber' => $data->reference_number,
                    'colorCode'       => $notification->color_code,
                    'remainDays'      => $deadLineDateRemain == 0 ? 'today' : ($deadLineDateRemain.' days'),
                ];
                $respondNotifications->push($tempModelObject);
            
            }

        }

        return $respondNotifications;
    }

    /**
     * check stock of product to alert notification
     * less than 50
     * @param stockCollettion $stockColletion
     * 
     * @return stockCollection $respondNotifications 
     */
    public static function checkStockQuantityproduct($modelCollection){

        $respondNotifications = new Collection();
        $currentUser = auth()->user();
        $shops = $currentUser->shops;
        foreach( $shops as $shop ){
            $productVariants = $shop->product_variants;
            $shopId = $shop->id;
            foreach($productVariants as $productVariant){
                $pvId = $productVariant->id;
                $pvAlertQuantity = $productVariant->alert_quantity;
                $productVariantQuantity = DB::table('product_variant_shops')
                                                    ->where('product_variant_id', $pvId)
                                                    ->where('shop_id', $shopId)
                                                    ->first();
                if( ($productVariantQuantity->quantity <= $pvAlertQuantity) &&  $pvAlertQuantity != null){
                    $stockData =  (object)[
                        'referenceNumber' => $shop->name,
                        'status'          => $productVariant->product_name,
                        'remainDays'      => $productVariantQuantity->quantity,
                        'id'              => $shopId,
                        'type'            => $productVariant->type,
                    ];
                    $respondNotifications->push($stockData);
                }
            }
        }
        return $respondNotifications;
    }

// type = stockQuantity, colorCode = #ff0000 , shopName = home , pvName = .. , pvQuantity= 10, pvId = 1
}
