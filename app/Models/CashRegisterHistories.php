<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CashRegisterHistories extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'cash_register_histories';

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
        'user_name',
        'full_name',
        'cash_in_hand',
        'status',
        'balance',
        'user_id',
        'cash_register_id',
    ];

    /**
     * ########################
     *      Helper Functions
     * ########################
    */   
  
    /**
     * Get all cash register history records from database
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getCashRegisterHistories(){
        $respond = (object)[];
        
        try {
            $cash_register_histories = CashRegisterHistories::all();
            $respond->data           = $cash_register_histories;
            $respond->message        = 'Cash register records found';
        } catch(Exception $ex) {
            $respond->data = false;
            $respond->message = 'Problem occured while trying to get cash register history records!';
        }

        return $respond;
    }

    /**
     * Get specific cash register history record from database.
     * @param Int $id
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function  getCashHistory($id) {
        $respond = (object)[];

        try {
            $cash_register_history = CashRegisterHistories::findOrFail($id);
            $respond->data    = $cash_register_history;
            $respond->message = 'Cash register history record found';
        } catch(ModelNotFoundException $e) {
            $respond->data    = false;
            $respond->message = 'Cash register history record not round!';
        }

        return $respond;
    }

    /**
     * ########################
     *      Relationship
     * ########################
    */

    /**
     * One cash register to one user relationship.
     * @return App\Model\User
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * One customer to one user relationship.
     * @return App\Model\CashRegister
     */
    public function cash_register(){
        return $this->belongsTo(CashRegister::class);
    }
}
