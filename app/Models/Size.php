<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;
    
    /**
     * Table name 
     * @var String
     */
    protected $table = 'sizes';

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
        'name',
        
    ];

    /**
     * Check validate request
     * @param String $name
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function checkValidateRequests(
        $name
    ) {
        $validate = (object)[];

        // check size name allow only number and letter
        $isNumerLetter = self::checkValidAllowOnlyNumberAndLetter($name);
        if (!$isNumerLetter) {
            $validate->message = "Size name should be number and letter only...!";
            return $validate;
        }

    }

    /**
     * ########################
     *     Helper function
     * ########################
     */

     // Size Helper Function [BEGIN]
        /**
         * Get all size records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSizes(){
            $respond = (object)[];

            try {
                $sizes = Size::all();
                $respond->data    = $sizes;
                $respond->message = 'Size records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get size records!'; 
            }

            return $respond;
        }

        /**
         * Get specific size records from database based on ginven id.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSize($id){
            $respond = (object)[];

            try {
                $size = Size::findOrFail($id);
                $respond->data    = $size;
                $respond->message = 'Size records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get size records!'; 
            }

            return $respond;
        }

         /**
         * Check allow only number and letter.
         * @param string $text 
         * @return Boolean
         */
        protected static function checkValidAllowOnlyNumberAndLetter($text)
        {
            if(!preg_match('/^[a-zA-Z0-9_.-]*$/', $text)){
                return false;
            }
            return true;
        }

     // Size Helper Function [END]


    /**
     * ########################
     *      Relationship
     * ########################
     */

     /**
     * Many to one relationship with product variant
     * @return App/Model/ProductVariant
     */
    public function product_variants(){
        return $this->hasMany(ProductVariant::class);
    }

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
