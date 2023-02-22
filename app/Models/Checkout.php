<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    /**
     * Check validate request
     * @param String $payment_methods
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function checkValidateRequest($payment_methods) {
        $validate = (object)[];

        // validate check is invalid payment methods.
        $isPaymentMethodValid = self::checkValidPaymentMethods($payment_methods);
        if (!$isPaymentMethodValid) {
            $validate->message = 'Invalid Payment Methods!';
            return $validate;
        }

    }

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
     * Check is invalid payment methods.
     * @param String $payment_methods 
     * @return Boolean
     */
    protected static function checkValidPaymentMethods($payment_methods){

        // get all bank account 
        $bankAccounts = self::getBankAccounts();
        if(!$bankAccounts->data){
            return back()->with('error', $bankAccounts->message);
        }
        $bankAccounts = $bankAccounts->data;

        foreach($bankAccounts as $bankAccount){
            if($payment_methods == $bankAccount->account_name){
                return ucwords($bankAccount->app_name);
            }
        }
        return false;
    }
}
