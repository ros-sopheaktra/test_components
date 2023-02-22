<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineStock extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String 
     */
    protected $table = 'online_stores';

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
        'shop_id',
        'user_id',

    ];

    /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * One to one with shop
     * @return App/Model/Shop
     */
    public function shop(){
        return $this->hasOne(Shop::class);
    }

     /**
     * ########################
     *      Helper Functions
     * ########################
     */

    // Online Stock Setting Helper Function (BEGIN)
        /**
         * Find online stock setting
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getOnlineStockSetting($id){
            $respond = (object)[];
            
            try {
                $online_stock = OnlineStock::findOrFail($id);
                $respond->data    = $online_stock;
                $respond->message = 'Success...!';
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage();
            }

            return $respond;
        }

        /**
         * Store shop online.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function storeShop($id){
            $respond = (object)[];
            
            try {
                $online_stocks = OnlineStock::all();
                if(count($online_stocks) == 0){
                    $online_stock = new OnlineStock([
                        'shop_id' => $id,
                    ]);
                    $online_stock->save();
                }else{
                    $online_stock = OnlineStock::findOrFail(1);
                    $online_stock->shop_id = $id;
                    $online_stock->update();
                }
                $respond->message = 'Success...!';
                $respond->data    = true;
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage();
            }

            return $respond;
        }
    // Online Stock Setting Helper Function (END)
}
