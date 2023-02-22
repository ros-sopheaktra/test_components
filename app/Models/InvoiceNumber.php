<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceNumber extends Model
{
    use HasFactory;

    /**
     * ########################
     *     Helper function
     * ########################
     */
    // Invoice Number Helper Functions [BEGIN]
        /**
         * Count order of invoice and return the name of invoice
         * @param int $id
         * @param date $date
         * @return ObejectRespond [ data: date_result, message: message_result ]
        */
        public static function orderNumberInvoicesPdf($id, $date,$referenceNo){
            $respond = (object)[];

            try {
                $order_by_dates = Order::query()
                    ->whereDate('created_at', '>=', $date)
                    ->where('reference_no', $referenceNo)
                    ->get();
                $number = 0;
                foreach($order_by_dates as $key => $order_by_date){
                    if($order_by_date->id == $id){
                        $number = $key + 1;
                    }
                }

                $name_invoice = $referenceNo;
                $respond->data = $name_invoice;
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();
            }

            return $respond;
        }

        /**
         * Count order of invoice and return the name of invoice
         * @param int $id
         * @param date $date
         * @param String $mode
         * @return ObejectRespond [ data: date_result, message: message_result ]
        */
        public static function orderNumberInvoices($id, $date, $mode = 'increase', $saleTypeMode){
            $respond = (object)[];

            try {
                $order_by_dates = Order::query()
                ->whereDate('created_at', '>=', $date)
                ->where('sale_type', $saleTypeMode)
                ->get();
                $number = 0;
                foreach($order_by_dates as $key => $order_by_date){
                    if($order_by_date->id == $id){
                        $number = $key + 1;
                    }
                }
                
                $mode === 'increase' ? $number+=1 : $number;
                $name_invoice  = $date->format('dmY').'-'.sprintf("%03d", $number);
                $respond->data = $name_invoice;
            } catch(Exception $ex) {
                $respond->data    = false;
                $respond->message = $ex->getMessage();
            }

            return $respond;
        }
    // Invoice Number Helper Functions [END]
}
