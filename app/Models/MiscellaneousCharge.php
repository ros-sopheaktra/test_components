<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Exception;
use Throwable;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MiscellaneousCharge extends Model
{
    use HasFactory;

    /**
     * Table name.
     * @var String
     */
    protected $table = 'miscellaneous_charges';

    /**
     * Primary key.
     * @var Integer
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assingable.
     * @var Array
     */
    protected $fillable = [
        'name',
        'price',
        'remark',
        'quantity',
        'order_id',

    ];

    /**
     * #############################
     *    Module Helper Functions
     * #############################
     */
        // Miscellaneous charge hellper functions [BEGIN]
            /**
             * Get 15 per call of miscellaneous charge records 
             * from database working best with pagination appraoch.
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getMiscellaneousCharges(){
                $respond = (object)[];

                try {
                    $miscellaneousCharges = MiscellaneousCharge::paginate(15);
                    $respond->data    = $miscellaneousCharges;
                    $respond->message = 'Successful getting miscellaneous charge records from database';
                } catch( Exception | Throwable $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Problem occured while trying to get miscellaneous charge records from database!';
                }

                return $respond;
            }

            /**
             * Get specific miscellaneous charge 
             * record based on given id parameter from database. 
             * @param Integer $id
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getMiscellaneousCharge( $id ){
                $respond = (object)[];

                try {
                    $miscellaneousCharge = MiscellaneousCharge::findOrFail( $id );
                    $respond->data    = $miscellaneousCharge;
                    $respond->message = 'Miscellaneous charge record found';
                } catch( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'Miscellaneous charge record not found!';
                }

                return $respond;
            }

            /**
             * Get miscellaneous charge records based 
             * on given array of id parameters from database. 
             * @param Array $ids
             * @return ObjectRespond [ data: data_result, message: result_message ]
             */
            public static function getMiscellaneousChargesBasedOnIds( $ids ){
                $respond = (object)[];

                // validate empty arrays
                if( sizeof( $ids ) < 0 ){
                    return (object)[
                        'data   ' => false,
                        'message' => 'Empty miscellaneous ids deteched, can not get miscellaneous on empty array!',
                    ];
                }

                try {
                    $miscellaneousCharges = new Collection();
                    foreach( $ids as $id ){
                        $tmpData = MiscellaneousCharge::findOrFail( $id );
                        $miscellaneousCharges->push( $tmpData );
                    }
                    $respond->data    = $miscellaneousCharges;
                    $respond->message = 'All miscellaneous charge records found';
                } catch( ModelNotFoundException $ex ) {
                    $respond->data    = false;
                    $respond->message = 'One of miscellaneous charge record not found!';
                    $respond->detailMessage = $ex->getMessage();
                }

                return $respond;
            }
        // Miscellaneous charge hellper functions [END]

    /**
     * #######################
     *    Helper Functions
     * #######################
     */

    /**
     * ###################
     *    Relationships
     * ###################
     */
        /**
         * Many miscellaneous charges to one order.
         * @return App\Models\Order
         */
        public function order(){
            return $this->belongsTo(
                Order::class,
                'order_id',
            );
        }
}
