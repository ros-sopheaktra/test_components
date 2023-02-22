<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'invoices';

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
        'total_cost',
        'total_price',
        'grand_total',
        'delivery_fee',
        'sale_tax',
        'grand_total',
        
    ];

    /**======================
            Relationships
    =========================*/

    /**
     * many to one relationship
     */
    // public function saleReport()
    // {
    //     return $this->belongsTo(SaleReport::class, 'sale_report_id', 'sale_report_id');
    // }

    /**
     * One to one relationship with order
     * @return App/Models/Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

}
