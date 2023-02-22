<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'districts';

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
        'district_name',
        
    ];

    /**
     * ########################
     *      Helper Function
     * ########################
     */
        /**
         * Get all district records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getDistricts(){
            $respond = (object)[];
            
            try {
                $districts = District::with('sangkats')->paginate(10);
                $respond->data = $districts;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find district records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getDistrict($id){
            $respond = (object)[];
            
            try {
                $district = District::findOrFail($id);
                $respond->data = $district;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message =  $e->getMessage(); 
            }

            return $respond;
        }


    /**
     * ########################
     *      Relationship
     * ########################
     */
    
    /**
     * Many to one relationship with sangkat
     * @return App/Model/Sangkat
     */
    public function sangkats(){
        return $this->hasMany(Sangkat::class);
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
