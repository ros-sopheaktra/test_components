<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Category extends Model
{
    use HasFactory;
    
    /**
     * Table name
     * @var String
     */
    protected $table = 'categories';

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
        'image',

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

    /**
     * Get all category records from database.
     * @return ObjectRespond 
    */
    public static function getCategoriesByBrands(){
        $respond = (object)[];
        
        try {
            $last_data        = Category::where('name', 'BY BRAND')->first();
            $categories       = Category::whereNotIn('id', [$last_data->id])->get();
            $respond->data    = $categories; 
            $respond->message = 'Category records found!'; 
        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Problem while tying to get category records!'; 
        };

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
     * Get specific category records from database.
     * @param Integer $id
     * @return ObjectRespond 
    */
    public static function getCategory($id){
        $respond = (object)[];
        
        try {
            $category = Category::findOrFail($id);
            $respond->data    = $category; 
            $respond->message = 'Category record found!'; 
        } catch(ModelNotFoundException $e) {
            $respond->data    = false; 
            $respond->message = 'Problem while tying to get category record!'; 
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
     * Many categories to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

    /**
     * One category to many sub categories.
     * @return App/Model/SubCategory
     */
    public function sub_categories(){
        return $this->hasMany(SubCategory::class);
    }

}
