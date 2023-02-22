<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackInvoice extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'track_invoces';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'invoce_number',
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */
        /**
         * Find temporary stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getInvoiceName($id, $mode){
            $respond = (object)[];
        
            try {
                $invoiceNumber = TrackInvoice::findOrFail($id);
                // store invoice or recipe number
                if ($mode == 'increase') {
                    $updatedDateInvoice = $invoiceNumber->updated_at->format('Y-m-d');
                    if ($updatedDateInvoice == Carbon::now()->format('Y-m-d')) {
                        $invoiceNumber->invoce_number += 1;
                    } else {
                        $invoiceNumber->invoce_number = 1;
                    }
                    $number = $invoiceNumber->invoce_number;
                } else {
                    $number = $invoiceNumber->invoce_number;
                }
                $date = Carbon::now();
                $name_invoice  = $date->format('dmY').'-'.sprintf("%03d", $number);
                $respond->data = $name_invoice;
                $respond->message = 'found.';
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage();
            }
            return $respond;
        }

        /**
         * Find temporary stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getInvoiceNumber($id){
            $respond = (object)[];
        
            try {
                $invoiceNumber = TrackInvoice::findOrFail($id);
                $respond->data = $invoiceNumber;
                $respond->message = 'found.';
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage();
            }
            return $respond;
        }
}
