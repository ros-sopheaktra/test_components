<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShowPriceBarcode extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'show_barcode_prices';

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
        'show_price',
        'show_logo_in_invoice',
        'show_logo_in_receipt',
        'show_shop_address_in_invoice',
        'show_shop_address_in_receipt',
        'receipt_footer',
        'invoice_footer',
        'is_show_shop_name_receipt',
        'is_show_shop_name_invoice',
    ];

    /******************
     * Helper Funtion
    *******************/

    /**
      * Get show barcode price form db base on given id
      * @param int $id
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
      protected static function getData($id){
        $respond = (object)[];
 
        try{
            $showBarcodePrice = ShowPriceBarcode::findOrFail($id);
            $respond->data = $showBarcodePrice;
            $respond->messange = 'Barcode price record found';
        }catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->messange = $e->getMessage();
        }
        return $respond;
    } 

    /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * Many size to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
