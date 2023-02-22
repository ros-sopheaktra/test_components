<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ourcodeworld\NameThatColor\ColorInterpreter;

class Color extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'colors';

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
        'hex_code',
        
    ];

    /**
     * Check validate request
     * @param String $name
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    // protected static function checkValidateRequests(
    //     $name
    // ) {
    //     $validate = (object)[];

    //     check size name allow only number and letter
    //     $isNumerLetter = self::checkValidAllowOnlyNumberAndLetter($name);
    //     if (!$isNumerLetter) {
    //         $validate->message = "Size name should be number and letter only...!";
    //         return $validate;
    //     }

    // }

    /**
     * ########################
     *     Helper function
     * ########################
     */

     // Color Helper Function [BEGIN]
        /**
         * Get the name of color based on given hex code
         * @return string color name
         */
        public static function getNameOfColor($hex_code){
            $instance = new ColorInterpreter();
            if($hex_code == 'One Color Only'){
                $colorName = $hex_code;
            }else{
                $color = $instance->name($hex_code);
                $colorName = $color["name"];
            }
            return $colorName;
        }

        /**
         * Get all color records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getColors(){
            $respond = (object)[];
            $colorCollection = new Collection();
            
            try {
                $colors = Color::all();
                foreach($colors as $color){
                    $colortmp = (object) [
                        'id'         => $color->id,
                        'hex_code'   => $color->hex_code,
                        'name'       => self::getNameOfColor($color->hex_code),
                        'created_at' => $color->created_at,
                    ];
                    $colorCollection->push($colortmp);
                }

                $respond->message = 'Color records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get color records!'; 
            }

            $respond->data = $colorCollection;
            return $respond;
        }

        /**
         * Get specific color records from database based on ginven id.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getColor($id){
            $respond = (object)[];

            try {
                $color = Color::findOrFail($id);
                $respond->data    = $color;
                $respond->message = 'Color records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = 'Problem occurred while trying to get color records!'; 
            }

            return $respond;
        }

        //  /**
        //  * Check allow only number and letter.
        //  * @param string $text 
        //  * @return Boolean
        //  */
        // protected static function checkValidAllowOnlyNumberAndLetter($text)
        // {
        //     if(!preg_match('/^[a-zA-Z0-9_.-]*$/', $text)){
        //         return false;
        //     }
        //     return true;
        // }

     // Color Helper Function [END]

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
     * Many color to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }
    
}
