<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sangkat extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'sangkats';

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
        'sangkat_name',
        'delivery_fee',
        'district_id'
    ];

    /**
     * ########################
     *      Helper Function
     * ########################
     */
        /**
         * Get all sangkats records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSangkats(){
            $respond = (object)[];
            
            try {
                $sangkats = Sangkat::with('district')->orderBy('district_id')->paginate(10);
                $respond->data = $sangkats;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find sangkats records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getSangkat($id){
            $respond = (object)[];
            
            try {
                $sangkat = Sangkat::findOrFail($id);
                $respond->data = $sangkat;
                $respond->message = 'Records found.';             
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Get all district records from database.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getDistricts(){
            $respond = (object)[];
            
            try {
                $district = District::all();
                $respond->data = $district;
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
     * Many to one relationship with district
     * @return App/Model/District
     */
    public function district(){
        return $this->belongsTo(District::class);
    }

    /**
     * Many to one relationship with shipping address
     * @return App/Models/ShippingAddress
     */
    public function shippingAddresses(){
        return $this->hasMany(ShippingAddress::class);
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
