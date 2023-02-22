<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExchangeRate extends Model
{
    use HasFactory;

  /**
     * Table name.
     * @var String
     */
    protected $table = 'exchange_rates';

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
        'currency_name',
        'currency_code',
        'exchange_rate',
        'symbol',
    ];

    /**
     * Check validate request
     * @param String $currency_code
     * @param String $currency_name
     * @param Int $exchange_rate
     * @param String $symbol
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function checkValidateRequests(
        $currency_code,
        $currency_name,
        $exchange_rate,
        $symbol
    ) {
        $validate = (object)[];

        // Check correct data type of currency code, currency name, exchange rate and symbol.
        $isNumeric = ExchangeRate::checkValidQPC($currency_code, $currency_name, $exchange_rate, $symbol);
        if (!$isNumeric) {
            $validate->message = "Currency code, Currency name, Symbol should be string and Exchange Rate should be number!";
            return $validate;
        }

        // currency code allow only letter
        $currencyCodeValidate = ExchangeRate::checkValidAllowOnlyLetter($currency_code);
        if (!$currencyCodeValidate) {
            $validate->message = "Currency code value should be letter only and no spece!";
            return $validate;
        }

        // symbol allow only letter and sybol
        $symbolValidate = ExchangeRate::checkValidNotAllowNumber($symbol);
        if (!$symbolValidate) {
            $validate->message = "Symbol value should be letter and sybol only and no spece!";
            return $validate;
        }

        // currency name allow only letter and letter with dot or dash
        $currencyNameValidate = ExchangeRate::checkValidAllowOnlyLetterDotDash($currency_name);
        if (!$currencyNameValidate) {
            $validate->message = "Currency name value should be letter and letter with dot(.) or dash(-) only!";
            return $validate;
        }

        // Check character of exchange rate not more than 10 characters
        $exrateNameValidate = ExchangeRate::checkCharacterExrate($exchange_rate);
        if (!$exrateNameValidate) {
            $validate->message = "The exchange rate may not be greater than 10 characters!";
            return $validate;
        }

    }

    /**===================
     *  Helper Functions
     *====================*/

     /**
     * Check correct data type of currency code, currency name, exchange rate and symbol.
     * @param String $currency_code 
     * @param String $currency_name 
     * @param Int $exchange_rate 
     * @param String $symbol 
     */
    protected static function checkValidQPC($currency_code, $currency_name, $exchange_rate, $symbol){
        if( !(is_string($currency_code) && is_string($currency_name) && is_numeric($exchange_rate) && is_string($symbol) )){
            return false;
        }
        return true;
    }

     /**
     * Check allow only letter.
     * @param string $text 
     * @return Boolean
     */
    protected static function checkValidAllowOnlyLetter($text)
    {
        if (!(preg_match("/^[a-zA-Z]+$/u", $text) == 1)) {
            return false;
        }
        return true;
    }

     /**
     * Check not allow number.
     * @param string $text 
     * @return Boolean
     */
    protected static function checkValidNotAllowNumber($var)
    {
        if(preg_match('/^[0-9 +-]*$/', $var)){
            return false;
        }
        return true;
    }

     /**
     * Check allow only letter or leeter with dot and dash.
     * @param string $string 
     * @return Boolean
     */
    protected static function checkValidAllowOnlyLetterDotDash($string)
    {
        if (!(preg_match("/^[A-Za-z. -]+$/", $string) == 1)) {
            return false;
        }
        return true;
    }

     /**
     * Check character of exchange rate not more than 10 characters.
     * @param Int $exrate 
     * @return Boolean
     */
    protected static function checkCharacterExrate($exrate)
    {
        if (!(strlen($exrate) <= 10 )) {
            return false;
        }
        return true;
    }

     /**
     * Get all exchange rates record form db
     * @param none
     * @return ResponObject [ data: result_data, messange:result_messange ]
     */
    protected static function getExchangeRates(){
        $respond = (object)[];
 
        try{
            $exchangeRates = ExchangeRate::all();
            $respond->data = $exchangeRates;
            $respond->messange = 'Exchange rate records found';
        }catch(Exception $e) {
             $respond->data = false;
            $respond->messange = 'Problem while trying to get exchange rate table missing or migration!';
         }
         return $respond;
     }
 
     /**
      * Get exchange rate form db base on given id
      * @param int $id
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
     protected static function getExchangeRate($id){
        $respond = (object)[];
 
        try{
            $exchangeRate = ExchangeRate::findOrFail($id);
            $respond->data = $exchangeRate;
            $respond->messange = 'Exchange rate record found';
        }catch(ModelNotFoundException $e) {
             $respond->data = false;
            $respond->messange = 'Exchange rate record not found!';
         }
         return $respond;
     } 

     /**===============
     *  Relationships
     *=================*/

    /**
     * one to many relationship with quotation
     * @param none
     * @return quotations
     */
    
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'exchange_rate_id');
    }
}
