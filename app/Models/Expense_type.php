<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Expense_type extends Model
{
    use HasFactory;

     /**
     * Table name 
     * @var String
     */
    protected $table = 'expense_type';

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
        'type',
        
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */

     /**
      * Get expenses all 
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
      public static function getExpenseTypes(){
        $respond = (object)[];
        try{
            $expenses = self::all();
            $respond->data = $expenses;
            $respond->messange = 'expense type records found';
        }catch(Exception $e) {
             $respond->data = false;
            $respond->messange = 'Problem while trying to get expense type table missing or migration!';
         }
         return $respond;
      }

      
    /**
      * Get expense type form db base on given id
      * @param int $id
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
     protected static function getExpenseTypeId($id){
        $respond = (object)[];
 
        try{
            $expenseType = self::findOrFail($id);
            $respond->data = $expenseType;
            $respond->messange = 'expense type record found';
        }catch(ModelNotFoundException $e) {
             $respond->data = false;
            $respond->messange = 'expense type record not found!';
         }
         return $respond;
     } 


    /**
     * ########################
     *     relationship 
     * ########################
     */

    /**
     * Many to one relationship with expense
     * @return App/Model/Expense
     */
    public function Expense(){
        return $this->hasMany(Expense::class);
    }

}
