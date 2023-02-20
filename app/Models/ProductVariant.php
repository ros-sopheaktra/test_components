<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'product_variants';

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
        'sku',
        'slug',
        'image',
        'cost',
        'price',
        'serial_number',
        'product_name',
        'detail_for_invioce',
        'pv_detail',
        'barcode',
        'alert_quantity',
        'barcode_id',
        'product_id',
        'size_id',
        'color_id',

    ];

    /**
     * ###############################
     *    Modules Helper Functions
     * ###############################
     */

    // Product Variant Helper Functions [BEGIN]
        /**
        * Get all product variant records form database.
        * @return ResponObject [ data: result_data, messange: result_messange ]
        */
        protected static function getProductVariants(){
            $respond = (object)[];
    
            try{
                $productVariants = ProductVariant::orderBy('id', 'DESC')->get();

                $respond->data     = $productVariants;
                $respond->messange = 'Successful getting all product variant records from database.';
            } catch ( Exception $e ) {
                $respond->data     = false;
                $respond->messange = 'Problem occured while trying to get product variant records from database!';
            }

            return $respond;
        }
        
        /**
         * Get specific product variant record 
         * based on given id form database.
         * @param Integer $id
         * @return ResponObject [ data: result_data, messange: result_messange ]
         */
        protected static function getProductVariant($id){
            $respond = (object)[];
    
            try{
                $productVariant = ProductVariant::findOrFail($id);

                $respond->data     = $productVariant;
                $respond->messange = 'Product variant record found';
            } catch ( ModelNotFoundException $e ) {
                $respond->data     = false;
                $respond->messange = 'Product variant record not found!';
            }

            return $respond;
        }
    // Product Variant Helper Functions [END]

}
