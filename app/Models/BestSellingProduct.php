<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BestSellingProduct extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'best_selling_products';

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
        'image_path',
        'product_variant_id',
        
    ];

    /**
     * ###############################
     *    Modules Helper functions
     * ###############################
     */

        // Best Selling Product Helper Functions [BEGIN]
            /**
             * Get all best selling product records from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getBestSellingProducts() {
                $respond = (object)[];

                try {
                    $bestSellingProducts = BestSellingProduct::all();
                    $respond->data    = $bestSellingProducts;
                    $respond->message = 'Sucessful getting best selling products from database';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying get best selling product from database!';
                }

                return $respond;
            }

            /**
             * Get specific bset selling product record 
             * based on given id parameter from database.
             * @param  Integer $id
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getBestSellingProduct( $id ) {
                $respond = (object)[];

                try {
                    $bestSellingProduct = BestSellingProduct::findOrFail( $id );
                    $respond->data    = $bestSellingProduct;
                    $respond->message = 'Best selling product found';
                } catch( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Best selling product not found!';
                }

                return $respond;
            }

        // Best Selling Product Helper Functions [END]

        // Product Variant Helper Functions [BEGIN]
            /**
             * Get all product variant records from database.
             * @return ObjectRespond [ data: data_result,  message: result_message ]
             */
            public static function getProductVariants(){
                $respond = (object)[];

                try {
                    $productVariants = ProductVariant::all();
                    $respond->data    = $productVariants;
                    $respond->message = 'Sucessfuly getting all product variant records';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get product variant records from database!';
                }

                return $respond;
            }

            /**
            * Store product variant on best selling
            * @return ResponObject [ data: result_data, messange: result_messange ]
            */
            protected static function storeProductVariant(Request $request){
                $respond = (object)[];
        
                try{
                    $productVariant_ids    = $request->get('pv_id');
                    $best_selling_products = BestSellingProduct::count();
                    foreach($productVariant_ids as $key => $id){
                        if($best_selling_products >= 4){
                            BestSellingProduct::first()->delete();
                        }
                        if($key+1 < 5){
                            $productVariant = ProductVariant::findOrFail($id);
                            $best_selling = new BestSellingProduct([
                                'image_path'         => $productVariant->image,
                                'product_variant_id' => $productVariant->id,
                            ]);
                            $best_selling->save();
                        }
                    }

                    $respond->data = true;
                    $respond->messange = 'Best selling product success to save!';
                }catch(Exception $e) {
                    $respond->data     = false;
                    $respond->messange = 'Problem occured while trying to get data from database!';
                }

                return $respond;
            }

        // Product Variant Helper Functions [END]

        // ... Helper Functions [BEGIN]
        // ... Helper Functions [END]

    /**
     * ##########################
     *      Helper functions
     * ##########################
     */

    
    /**
     * #####################
     *      Relationship
     * #####################
     */

    /**
     * Many best selling products to one product variant.
     * @return App\Model\ProductVariant
     */
    public function productVariants(){
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id',
        );
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
