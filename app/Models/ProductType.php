<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductType extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var string
     */
    protected $table = 'product_types';

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * @var array
     */
    protected $fillable = [
        'name',
        'header',
        'description',

    ];

    /**
     * #################################
     *     Helper Functions
     * #################################
     */

        // ProductType Helper Functions get all producType (BEGIN)

            /**
             * Get all product type records from database.
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductTypes(){
                $respond = (object)[];

                try {
                    $productTypes     = ProductType::all();
                    $respond->data    = $productTypes;
                    $respond->message = 'Records found';
                } catch( Exception $e ){
                    $respond->data    = false;
                    $respond->message = 'There is a problem while trying to get product types!';
                }

                return $respond;
            }

        // ProductType Helper Functions get all producType (END)

        // ProductType Helper Functions get producType bast on id (BEGIN)

            /**
             * Get specific product type record from database.
             * @param Integer $id
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function getProductType($id){
                $respond = (object)[];

                try {
                    $productType      = ProductType::findOrFail($id);
                    $respond->data    = $productType;
                    $respond->message = 'Records found';
                } catch( ModelNotFoundException $e ){
                    $respond->data    = false;
                    $respond->message = 'Product type record not found!';
                }

                return $respond;
            }

        // ProductType Helper Functions get producType bast on id (END)

        // ProductType Helper Functions check Valid string only (BEGIN)

            /**
             * Check valid string that contain only string.
             * @param String value
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function checkValidStringOnly($value){
                $respond = (object)[];
                $value   = preg_replace("/[\s$@_*]+/", " ", $value);
                
                if( !preg_match("/^([a-zA-Z ])+$/i", $value) ){
                    $respond->data    = false;
                    $respond->message = "Value invalid, please noted that no space before and end of format!";
                }else{
                    $respond->data    = $value;
                    $respond->message = 'value valid';
                }

                return $respond;
            }

        // ProductType Helper Functions check Valid String Only (END)

        // ProductType Helper Functions check Valid string Number Dot Only (BEGIN)

            /**
             * Check valid name value.
             * @param String $value
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function checkValidStringnNumberDotOnly($value){
                $value = preg_replace("/[\s$@_*]+/", " ", $value);
                if (!preg_match("/^[a-zA-Z0-9 ,.]*$/", $value)) {
                    $respond = (object) [
                        'data'    => false,
                        'message' => 'String is invalid!',
                    ];

                    return  $respond;
                } else {
                    $respond = (object) [
                        'data'    => $value,
                        'message' => 'String is valid!',
                    ];

                    return  $respond;
                }
            }

        // ProductType Helper Functions check Valid string Number Dot Only (END)

        // ProductType Helper Functions check Reuqest Validation (BEGIN)

            /**
             * validation request data.
             * @param Form_Request_Value $name
             * @param Form_Request_Value $heading
             * 
             * @return RespondObject [ data: result_data, message: result_message ] 
             */
            public static function checkReuqestValidation( $name, $heading ){
                $result = (object)[];

                // check valid name
                $nameResult = ProductType::checkValidStringOnly($name);
                if( !$nameResult->data ){
                    $nameResult->message = 'Product type name is invalid!';
                    $result = $nameResult;
                    return $result;
                }

                // check valid heading
                $headingResult = ProductType::checkValidStringnNumberDotOnly($heading);
                if( !$headingResult->data ){
                    $headingResult->message = 'Product type description heading is invalid!';
                    $result = $headingResult;
                    return $result;
                }

                $result->name    = $nameResult->data;
                $result->heading = $headingResult->data;
                $result->data    = true;
                $result->message = 'All data are valided!';

                return $result;
            }

        // ProductType Helper Functions check Reuqest Validation (END)

    /**
     * #################################
     *      Relationship
     * #################################
     */
    
        /**
         * One product type to many products.
         * @return App/Model/Product
         */
        public function products(){
            return $this->hasMany(
                Product::class,
                'product_type_id',
            );
        }

        /**
         * Many product types to many log histories (Polymorphic)
         * @return App/Model/LogHistory
         */
        public function logHistories(){
            return $this->morphToMany(
                LogHistory::class,
                'historyables',
            );
        }

}
