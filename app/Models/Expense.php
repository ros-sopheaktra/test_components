<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpParser\Node\Expr\FuncCall;

class Expense extends Model
{
    use HasFactory;
    
  /**
     * Table name.
     * @var String
     */
    protected $table = 'expense';

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
        'date',
        'expense_code',
        'expense_name',
        'type',
        'total_cost',
        'description',
        'expense_type_id',
        'shop_id',
    ];

    /**===================
     *  Helper Functions
     *====================*/
    
    /**
      * Get expenses all 
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
      public static function getExpenses(){
        $respond = (object)[];
        try{
            $expenses = self::all();
            $respond->data = $expenses;
            $respond->messange = 'expense records found';
        }catch(Exception $e) {
             $respond->data = false;
            $respond->messange = 'Problem while trying to get expense table missing or migration!';
         }
         return $respond;
      }

    /**
      * Get expenses form db base on given id
      * @param int $id
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
      protected static function getExpense($id){
          $respond = (object)[];

          try{
              $expenseType = Expense_type::getExpenseTypes();
              $expense = self::findOrFail($id);
              $respond->data = $expense;
              $respond->expenseType = $expenseType;
              $respond->messange = 'expense record found';
          }catch(ModelNotFoundException $e) {
              $respond->data = false;
              $respond->messange = 'expense record not found!';
          }
          return $respond;
      } 

    /**
     * ########################
     *     relationship 
     * ########################
     */

    /**
     * Many to expense to one expense type relationship.
     * @return App\Models\Expense_type
     */
    public function ExpenseType(){
      return $this->belongsTo(
          Expense_type::class,
          'expense_type_id',
      );
    }

     /**
     * Many to one with shop
     * @return App/Model/Shop
     */
    public function shop(){
      return $this->belongsTo(
        Shop::class,
        'shop_id',
      );
  }
}
