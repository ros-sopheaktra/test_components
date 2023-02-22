<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

     /**
     * Table name.
     * @var String
     */
    protected $table = 'bank_accounts';

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
        'app_name',
        'account_name',
        'account_number',
        'icon',
        'qrcode',
        'is_show_in_pos_invoice',
        'is_show_in_pos_receipt',
    ];

    /**===================
     *  Helper Functions
    *====================*/

     /**
     * Get all bank account records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getBankAccounts(){
        $respond = (object)[];

        try {
            $bankAccounts = BankAccount::all();
            $respond->data = $bankAccounts;
            $respond->message = 'Records found';
        } catch(Exception $e) {
            $respond->data = false;
            $respond->message = $e->getMessage();
        }

        return $respond;
    }

     /**
     * Get bank account records from database.
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getBankAccount($id){
        $respond = (object)[];

        try {
            $bankAccount = BankAccount::findOrFail($id);
            $respond->data = $bankAccount;
            $respond->message = 'Records found';
        } catch(Exception $e) {
            $respond->data = false;
            $respond->message = $e->getMessage();
        }

        return $respond;
    }

    /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * Many bank account to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }
}
