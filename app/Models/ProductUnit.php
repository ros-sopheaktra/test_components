<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductUnit extends Model
{
    use HasFactory;
    
    /**
     * Table name
     * @var String 
     */
    protected $table = 'product_units';

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
        'title',
        'main_unit',
        'sale_unit',
        'increment',

    ];

    /**
     * #################################
     *     Helper Functions
     * #################################
     */

        // Product Unit Helper Functions get product unit using query all  (BEGIN)

            /**
             * Get all product unit records from database.
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductUnits(){
                $respond = (object)[];

                try {
                    $productUnits     = ProductUnit::all();
                    $respond->data    = $productUnits;
                    $respond->message = 'Records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'There is a problem while trying to get product units!';
                }

                return $respond;
            }

        //  Product Unit Helper Functions get product unit using query all  (END)

        // Product Unit Helper Functions get product unit ask on id  (BEGIN)

            /**
             * Get specific product unit record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductUnit($id){
                $respond = (object)[];

                try {
                    $productUnit      = ProductUnit::findOrFail($id);
                    $respond->data    = $productUnit;
                    $respond->message = 'Records found';
                } catch( ModelNotFoundException $e ){
                    $respond->data    = false;
                    $respond->message = 'Product unit record not found!';
                }

                return $respond;
            }

        // Product Unit Helper Functions get product unit ask on id   (END)

        // Product Unit Helper Functions check Valid String Only   (BEGIN)

            /**
             * Check valid string that contain only string.
             * @param String value
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function checkValidStringOnly($value){
                $respond = (object)[];
                $value = preg_replace("/[\s$@_*]+/", " ", $value);
                
                if( !preg_match("/^([a-zA-Z ])+$/i", $value) ){
                    $respond->data = false;
                    $respond->message = "Value invalid, please noted that no space before and end of format!";
                }else{
                    $respond->data = $value;
                    $respond->message = 'value valid';
                }

                return $respond;
            }

        // Product Unit Helper Functions check Valid String Only  (END)

        // Product Unit Helper Functions check Valid number   (BEGIN)

            /**
             * Check valid number type and value must be greater than 0.
             * @param String $value
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function checkValidNumber($value){
                $respond = (object)[];
                $value   = str_replace(" ", "", $value);

                if( is_numeric($value) && $value>0 ){
                    $respond->data      = $value;
                    $respond->message   = 'Value is a number';

                    return $respond;
                }else{
                    $respond->data      = false;
                    $respond->message   = 'Value is not a number or value is samller than 0!';

                    return $respond;
                }
            }

        // Product Unit Helper Functions check Valid number   (END)

        // Product Unit Helper Functions check Reuqest Validation (BEGIN)

            /**
             * validation request data.
             * @param Form_Request_Value $title
             * @param Form_Request_Value $main_unit
             * @param Form_Request_Value $sale_unit
             * @param Form_Request_Value $increment
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function checkReuqestValidation($title, $main_unit, $sale_unit, $increment){
                $respond = (object)[];
                
                // check valid title
                $titleResult = ProductUnit::checkValidStringOnly($title);
                if(!$titleResult->data){
                    $titleResult->message = 'Unit title value is invalid!';
                    $respond = $titleResult;
                    return $respond;
                }

                // check valid main unit
                $mainUnitResult = ProductUnit::checkValidStringOnly($main_unit);
                if(!$mainUnitResult->data){
                    $mainUnitResult->message = 'Main unit value is invalid!';
                    $respond = $mainUnitResult;
                    return $respond;
                }

                // check valid sale unit
                $saleUnitResult = ProductUnit::checkValidStringOnly($sale_unit);
                if(!$saleUnitResult->data){
                    $saleUnitResult->message = 'Sale unit value is invalid!';
                    $respond = $saleUnitResult;
                    return $respond;
                }

                // check main unit and sale unit
                if($mainUnitResult->data === $saleUnitResult->data){
                    $respond->data = false;
                    $respond->message = 'Main Unit and Sale Unit value should not be the equalt!';
                    return $respond;
                }

                // check valid increment
                $incrementResult = ProductUnit::checkValidNumber($increment);
                if(!$incrementResult){
                    $incrementResult->message = 'Increment value is not a number or value smaller than 0!';
                    $respond = $incrementResult;
                    return $respond;
                }

                $respond->data      = true;
                $respond->message   = 'All data are valided';

                $respond->title     = strtolower($titleResult->data);
                $respond->mainUnit  = strtolower($mainUnitResult->data);
                $respond->saleUnit  = strtolower($saleUnitResult->data);
                $respond->increment = $incrementResult->data;

                return $respond;
            }

        // Product Unit Helper Functions check Reuqest Validation (END)

     /**
     * #################################
     *      Relationship
     * #################################
     */

        /**
         * One product unit to many products.
         * @return App\Model\Product
         */
        public function products(){
            return $this->hasMany(
                Product::class,
                'product_unit_id',
            );
        }

        /**
         * Many product units to many log histories (Polymorphic)
         * @return App\Model\LogHistory
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

}
