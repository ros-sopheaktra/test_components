<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class StockControl extends Model
{
    use HasFactory;

    /**
     * ########################
     *     Helper function
     * ########################
     */

// Shop Helper Function [BEGIN]
    /**
     * Get shop based on user
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getShops(){
        $respond = (object)[];
    
        try {
            $currentUser = User::findOrFail(Auth::user()->id);
            if ($currentUser->roles->first()->name == 'super admin') {
                $shops = Shop::all();
            } else {
                $shops = $currentUser->shops;
            }
            
            $respond->data = $shops;
            $respond->message = 'Shop records found.';             
        } catch(Exception $e) {
            $respond->data = false;
            $respond->message = $e->getMessage(); 
        }

        return $respond;
    }

    /**
     * Get specific shop records from database based on ginven id.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function findShop($id){
        $respond = (object)[];

        try {
            $shop = Shop::findOrFail($id);
            $respond->data    = $shop;
            $respond->message = 'Shop records found.';             
        } catch(Exception $e) {
            $respond->data    = false;
            $respond->message = 'Problem occurred while trying to get shop records!'; 
        }

        return $respond;
    }
// Shop Helper Function [END]

// Product variant Helper Function [BEGIN]

    /**
     * Get all product records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getAllProducts(){
        $respond = (object)[];

        try {
            $products = Product::all();
            $respond->data    = $products;
            $respond->message = 'Product records found';
        } catch( Exception $e ){
            $respond->data    = false;
            $respond->message = $e->getMessage();
        }

        return $respond;
    }

    /**
     * Get all product records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getProducts(){
        $respond = (object)[];
        $productsCollection = new Collection();
    
        // get shop
        $shops = self::getShops();
        if(!$shops->data){
            return back()->with('error', $shops->message);
        }
        $shops = $shops->data;

        // get shop
        $products = self::getAllProducts();
        if(!$products->data){
            return back()->with('error', $products->message);
        }
        $products = $products->data;

        try {
            foreach($products as $product){
                $totalQuantity = 0;
                foreach($product->product_variants as $product_variant){
                    $quantity = 0;
                    foreach($product_variant->shops as $shop){
                        $quantity += $shop->pivot->quantity;
                    }
                    $product_variant->quantity = $quantity;
                    $totalQuantity += $quantity;
                }
                $product->totalQuantity = $totalQuantity;
                $image = count($product->product_variants) == 0 ? null : $product->product_variants->first()->image;
                $product->image = $image;
            }
          
            $respond->data    = $products->paginate(25)->onEachSide(2);
            $respond->shops   = $shops;
            $respond->message = 'Stock control records found.';             
        } catch(Exception $e) {
            $respond->data    = false;
            $respond->shops   = false;
            $respond->message = $e->getMessage(); 
        }

        return $respond;
    }

    /**
     * Get all pv records from database.
     * @param {Int} $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getProductVariantsByShop($id){
        $respond = (object)[];

        // find shop
        $shop = self::findShop($id);
        if(!$shop->data){
            return back()->with('error', $shop->message);
        }
        $shop = $shop->data;
    
        try {
            $total = 0;
            foreach($shop->product_variants as $product_variant){
                $product_variant->quantity = $product_variant->pivot->quantity;
                $product_variant->location = $product_variant->pivot->location;
                $product_variant->remark   = $product_variant->pivot->remark;
                $product_variant->category = $product_variant->product->category->name;
                $product_variant->brand    = $product_variant->product->brand->name;
                $product_variant->unit     = $product_variant->product->productUnit->title;
                
                $total += $product_variant->pivot->quantity;      
            }

            $respond->data    = $shop->product_variants;
            $respond->shop    = $shop;
            $respond->total   = $total;
            $respond->message = 'Product variant records found.';             
        } catch(Exception $e) {
            $respond->data  = false;
            $respond->message = $e->getMessage(); 
        }

        return $respond;
    }

    /**
     * Save location based on given pv id and shop id
     * @param {Request} $request
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function saveLocationAndRemark(Request $request){
        $respond = (object)[];
     
        try{
            $shop_id  = $request['shopId'];
            $pv_id    = $request['pvId'];
            $location = $request['location'];
            $remark   = $request['remark'];
            $quantity = $request['quantity'];
            DB::table('product_variant_shops')
            ->where('shop_id', $shop_id)
            ->where('product_variant_id', $pv_id)
            ->update(array('location' =>  $location, 'remark' => $remark, 'quantity' => $quantity));

            $respond->data     = true;
            $respond->messange = 'Location add successfully...!';
        }catch(ModelNotFoundException $e) {
            $respond->data     = false;
            $respond->messange = 'Product variant record not found!';
        }

        return $respond;
    }

    // Product Variant Helper Funtion [END]

}
