<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryStockCount extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'temporary_stock_counts';

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
        'expected_qty',
        'counted_qty',
        'reconcile',
        'product_variant_id',
        'stock_count_id',
        
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */
     // Temporary Stock Count Helper Function [BEGIN]
        /**
         * Get all temporary stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getTemporaryStockCounts(){
            $respond = (object)[];
        
            try {
                $temporary_stock_counts = TemporaryStockCount::orderBy('id', 'DESC')->get();
                $respond->data = $temporary_stock_counts;
                $respond->message = 'Tempory stock count records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * Find temporary stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getTemporaryStockCount($id){
            $respond = (object)[];
        
            try {
                $temporary_stock_count = TemporaryStockCount::findOrFail($id);
                $respond->data = $temporary_stock_count;
                $respond->message = 'Tempory stock count records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

    // Temporary Stock Count Function [END]

    /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * One order temporary stock count to many product variants.
     * @return App/Models/ProductVariant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
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