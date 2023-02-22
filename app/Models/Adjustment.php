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

class Adjustment extends Model
{
    use HasFactory;
    
     /**
     * Table name.
     * @var String
     */
    protected $table = 'adjustments';

    /**
     * Primary key.
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that are mass assignable.
     * @var Array
     */
    protected $fillable = [
        'datetime',
        'reference_no',
        'document',
        'address',
        'created_by',
        'note',
        'shop_id'

    ];

    /**===================
     *  Helper Functions
     *====================*/

    /**
     * Get all adjustment record form db
     * @param none
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function getAdjustments(){
        $respond = (object)[];
 
        try{
            $adjustments = Adjustment::orderBy('id', 'DESC')->get();
            $respond->data = $adjustments;
            $respond->messange = 'Adjustments records found';
        }catch(Exception $e) {
             $respond->data = false;
            $respond->messange = 'Problem while trying to get adjustments, table missing or migration!';
         }
         return $respond;
     }

     /**
     * Get adjustment record form db base on given id
     * @param int $id
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function getAdjustment($id){
        $respond = (object)[];
 
        try{
            $adjustment = Adjustment::findOrFail($id);
            $respond->data = $adjustment;
            $respond->messange = 'Ajustment record found';
        }catch(ModelNotFoundException $e) {
             $respond->data = false;
            $respond->messange = 'Adjustment record not found!';
         }
         return $respond;
     }

    /**
     * Get all shop records from database.
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
            $respond->data    = false;
            $respond->message = 'Problem occurred while trying to get shop records!'; 
        }

        return $respond;
    }

    /**
     * Get all pv records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getProductVariants(Request $request){
        $respond = (object)[];

        try {
            $shop = Shop::findOrFail($request->get('shopId'));
            $respond->data = $shop->product_variants;
            $respond->message = 'Stock control records found.';             
        } catch(Exception $e) {
            $respond->data    = false;
            $respond->message = 'Problem occurred while trying to get stock control records!'; 
        }

        return $respond;
    }

    /**
     * Get the user create adjustment and returm the mame of user
     * @param $user_name
     * @return $result (return the name of user are created adjustment)
     */
    protected static function getCreatedBy(){
        $firstName = Auth::user()->firstname;
        $lastName = Auth::user()->lastname;
        $fullName = $firstName.' '.$lastName;

        return $fullName;
     }

    /**
     * upload ducument into project path and return the name of file 
     * @param $files
     * @return $filename (return the name of file)
     */
    protected static function uploadDocument($files){
       if ($files) {
            $destinationPath = 'file/'; // upload path
            $filename = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $filename);
        }else {
            $filename = null;
        }
        return $filename;
     }

    /**
     * Get product variant ids and it's coresponding order quantities based on 
     * given order module as a paramter, and return in JSON format.
     * @param Array $productVariantIds
     * @param Array $productVariantName
     * @param Array $countedQty
     * @return ObjectRespond [ data: data_result, message: result_message ]
     */
    public static function bindStructureOfPvForStockCountConvertToJson($productVariantIds, $productVariantName, $differences){
        $respond = (object)[];
        $productVariants = new Collection();

        try {
            foreach($productVariantIds as $key => $productVariantId){
                if($differences[$key] != 0){
                    $tmpPv = (object)[
                        'id'       => $productVariantId,
                        'pv_name'  => $productVariantName[$key],
                        'quantity' => $differences[$key],
                    ];
                    $productVariants->push($tmpPv);
                }
            }      
            $respond->data = json_encode($productVariants);
        } catch(Exception $e) {
            $respond->data    = false;
            $respond->message = $e->getMessage(); 
        }

        return $respond;
    }

    // Stock Count Function [BEING]
        /**
         * Find stock count based on user
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getStockCount($id){
            $respond = (object)[];
        
            try {
                $stock_count = StockCount::findOrFail($id);
                $respond->data = $stock_count;
                $respond->message = 'Stock count records found.';             
            } catch(Exception $e) {
                $respond->data = false;
                $respond->message = $e->getMessage(); 
            }

            return $respond;
        }

        /**
         * @param Object $stockCount
         * @return $void
         */
        public static function updateStockCount($stockCount){
           // get temporary stock count
            $temporaryStockCounts = $stockCount->temporaryStockCounts;
            
            // update rcord
            foreach($temporaryStockCounts as $temporaryStockCount){
                $temporaryStockCount = TemporaryStockCount::findOrFail($temporaryStockCount->id);
                $pvShop = DB::table('product_variant_shops')
                ->where('shop_id', $stockCount->shop->id)
                ->where('product_variant_id', $temporaryStockCount->productVariant->id)
                ->first();

                $temporaryStockCount->expected_qty = $pvShop->quantity;
                $expected_qty = ($pvShop->quantity) >= 0 ? ($pvShop->quantity) : -($pvShop->quantity);
                $temporaryStockCount->reconcile = (($temporaryStockCount->counted_qty) - ($expected_qty)) * ($temporaryStockCount->productVariant->price);
                $temporaryStockCount->update();
            }
        }
    // Stock Count Function [END]

    /**===============
     *  Relationships
     *=================*/
    
    /**
     * Many relationship with product variants
     * @param none
     * @return product varinat
     */
    public function product_variants()
    {
        return $this->belongsToMany(
            ProductVariant::class, 
            'product_variant_adjustments',
            'adjustment_id',
            'product_variant_id'
        )
        ->withPivot('quantity', 'type');
    }

    /**
     * Many to one with shop
     * @return App/Model/Shop
     */
    public function shop(){
        return $this->belongsTo(Shop::class);
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
