<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubCategory extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'sub_category';

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
        'name',
        'category_id'

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

    /**
     * Get all sub category records from database.
     * @return ObjectRespond 
    */
    public static function getSubCategories(){
        $respond = (object)[];
        
        try {
            $subcategories = SubCategory::all();
            $respond->data    = $subcategories; 
            $respond->message = 'Sub Category records found!'; 
        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Problem while tying to get sub category records!'; 
        };

        return $respond;
    }

    /**
     * Get specific sub category records from database.
     * @param Integer $id
     * @return ObjectRespond 
    */
    public static function getSubCategory($id){
        $respond = (object)[];
        
        try {
            $subcategory = SubCategory::findOrFail($id);
            $respond->data    = $subcategory; 
            $respond->message = 'Sub Category record found!'; 
        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Problem while tying to get sub category record!'; 
        };

        return $respond;
    }

    /**
     * Check valid string that contain only string.
     * @param String value
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function checkValidString($value){
        $respond = (object)[];
        $value = preg_replace("/[\s$@_*]+/", " ", $value);
        
        if ( !preg_match("/^([a-zA-Z ])+$/i", $value) ) {
            $respond->data = false;
            $respond->message = "Value invalid, please noted that no space before and end of format!";
        } else {
            $respond->data = $value;
            $respond->message = 'value valid';
        }

        return $respond;
    }

    /**
    * Validation request data.
    * @param Form_Request_Value $name
    * @return RespondObject [ data: result_data, message: result_message ] 
    */
    public static function checkRequestValidation($name){
        $respond = (object)[];

        // check valid name
        $nameResult = Category::checkValidString($name);
        if(!$nameResult->data){
            $respond = $nameResult;
            return $respond;
        }

        $respond->data    = true;
        $respond->name    = $nameResult->data;
        $respond->message = 'All requests are valid!';

        return $respond;
    }

    /**
     * Get all category records from database.
     * @return ObjectRespond 
    */
    public static function getCategories(){
        $respond = (object)[];
        
        try {
            $categories = Category::all();
            $respond->data    = $categories; 
            $respond->message = 'Category records found!'; 
        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Problem while tying to get category records!'; 
        };

        return $respond;
    }

     /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * One category to many products.
     * @return App/Model/Product
     */
    public function products(){
        return $this->hasMany(
            Product::class,
            'category_id',
        );
    }

    /**
     * One category to many categories.
     * @return App/Model/Categories
     */
    public function category(){
        return $this->belongsTo(Category::class);
    }

    /**
     * Many categories to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

}
