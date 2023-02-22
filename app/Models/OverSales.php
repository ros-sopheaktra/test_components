<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OverSales extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'over_sales';

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
        'over_sales_dashboard',
        'over_sales_pos',
    ];

    /**
      * Get over sales form db base on given id
      * @param int $id
      * @return ResponObject [ data: result_data, messange:result_messange ]
      */
      protected static function getOverSale($id){
        $respond = (object)[];
 
        try{
            $oversales = OverSales::findOrFail($id);
            $respond->data = $oversales;
            $respond->messange = 'Over Sales record found';
        }catch(ModelNotFoundException $e) {
            $respond->data = false;
            $respond->messange = $e->getMessage();
        }
        return $respond;
    } 

}
