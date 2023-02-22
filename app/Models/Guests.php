<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guests extends Model
{
  use HasFactory;

  /**
   * Table name
   * @var String
   */
  protected $table = 'order_guest';

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
    'name',
    'phone',
    'order_id',
  ];

  /**
   * ############################
   *       Relationship 
   * ############################
   */
    /**
      * One guest to one order relationship.
      * @return App\Models\Order
      */
      public function order(){
        return $this->belongsTo(
          Order::class,
          'order_id',
        );
      }
}
