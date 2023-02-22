<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FeaturedProduct extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'featured_products';

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
        'thumbnail',
        'product_variant_id',
        
    ];

    /**
     * ###############################
     *    Modules Helper functions
     * ###############################
     */
        // Featured Product Helper Functions [BEGIN]
            /**
             * Get all featured product records from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getFeaturedProducts() {
                $respond = (object)[];

                try {
                    $feattureProducts = FeaturedProduct::all();
                    $respond->data    = $feattureProducts;
                    $respond->message = 'Sucessful getting featured products from database';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying get featured product from database!';
                }

                return $respond;
            }

            /**
             * Get specific bset selling product record 
             * based on given id parameter from database.
             * @param  Integer $id
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getFeaturedProduct($id) {
                $respond = (object)[];

                try {
                    $feattureProduct = FeaturedProduct::findOrFail( $id );
                    $respond->data    = $feattureProduct;
                    $respond->message = 'featured product found';
                } catch( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'featured product not found!';
                }

                return $respond;
            }

            /**
             * Get all featured product records from database.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getFeaturedProductHomePages() {
                $respond = (object)[];

                try {
                    $numberOfFProduct = FeaturedProduct::count();
                    $numberofslide = $numberOfFProduct / 4;
                    if(is_float($numberofslide)){
                        $intNumber = (int)$numberofslide;
                        $numberofslide = $intNumber + 1;
                    }
                    
                    $feattureProducts = FeaturedProduct::all();
                    $readyToGoFeatureProdctCollection = new Collection(); // collection that contain 1x4 based use in front end
                    $tmpFourItemsBaseCollection       = new Collection(); // collection that contain 4 items each
                    for( $i = 0; $i < count( $feattureProducts ); $i++ ) {
                        $tmpFourItemsBaseCollection->push( $feattureProducts[$i] );
                        
                        // push every 4 items
                        if( ($i+1)%4 == 0 ) {
                            $readyToGoFeatureProdctCollection->push( $tmpFourItemsBaseCollection );
                            $tmpFourItemsBaseCollection = new Collection();
                        }
            
                        // push if reach to the end of loop
                        if( $i+1 == count( $feattureProducts ) ) {
                            $readyToGoFeatureProdctCollection->push( $tmpFourItemsBaseCollection );
                        }
            
                    }

                    $respond->data         = $readyToGoFeatureProdctCollection;
                    $respond->number_slide = $numberofslide;
                    $respond->message = 'Sucessful getting featured products from database';
                } catch( Exception $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying get featured product from database!';
                }

                return $respond;
            }
        // featured Product Helper Functions [END]

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
            * Store product variant on featured product
            * @return ResponObject [ data: result_data, messange: result_messange ]
            */
            protected static function storeProductVariant(Request $request){
                $respond = (object)[];
        
                try{
                    $productVariant_ids    = $request->get('pv_id');
                    foreach($productVariant_ids as $id){
                        $productVariant = ProductVariant::findOrFail($id);
                        $best_selling = new FeaturedProduct([
                            'thumbnail'          => $productVariant->image,
                            'product_variant_id' => $productVariant->id,
                        ]);
                        $best_selling->save();
                    }

                    $respond->data = true;
                    $respond->messange = 'Featured product success to save!';
                }catch(Exception $e) {
                    $respond->data     = false;
                    $respond->messange = 'Problem occured while trying to get data from database!';
                }

                return $respond;
            }

        // Product Variant Helper Functions [END]

    /**
     * ########################
     *      Relationship
     * ########################
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
}
