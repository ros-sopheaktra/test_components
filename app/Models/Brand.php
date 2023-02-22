<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Brand extends Model
{
    use HasFactory;

    /**
     * table name
     * @var String
     */
    protected $table = 'brands';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that mass assignable
     * @var Array
     */
    protected $fillable = [
        'name',
        'image',
        'description',

    ];

    /**
     * ########################
     *      Relationship
     * ########################
     */

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
     * Get all product brand record from database.
     * @param Int $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getProductBrand($id){
        $respond = (object)[];

        try {
            $productBrand = Brand::findOrFail($id);
            $respond->data = $productBrand;
            $respond->message = 'Records found';
        } catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->message = 'Brand record not found!';
        }

        return $respond;
    }

    /**
     * Check valid string that contain only string.
     * @param String value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidStringOnly($value){
        $respond = (object)[];
        $value = preg_replace("/[\s$@_*]+/", " ", $value);
        
        if ( !preg_match("/^([0-9a-zA-Z ])+$/i", $value) ) {
            $respond->data = false;
            $respond->message = "Value invalid, please noted that no space before and end of format!";
        } else {
            $respond->data = $value;
            $respond->message = 'value valid';
        }

        return $respond;
    }

    /**
     * validation request data.
     * @param Form_Request_Value $name
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkReuqestValidation($name){
        $respond = (object)[];

        // check valid name
        $nameResult = Brand::checkValidStringOnly($name);
        if(!$nameResult->data){
            $nameResult->message = 'Brand name is invalid!';
            $respond = $nameResult;
            return $respond;
        }

        $respond->name = $nameResult->data;

        $respond->data = true;
        $respond->message = 'All data are valided!';

        return $respond;
    }

     /**
     * ########################
     *      Relationship
     * ########################
     */

     /**
     * One brand to many products.
     * @return App/Model/Product
     */
    public function products(){
        return $this->hasMany(
            Product::class,
            'brand_id',
        );
    }

    /**
     * Many product brands to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
