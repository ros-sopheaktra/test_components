<?php

namespace App\Models;

use Exception;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PanoramaImage extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    protected $table = 'panorama_images';

    /**
     * Primary key
     * @var String
     */
    protected $primaryKey = 'id';

    /**
     * The attributes tha are mass assingable.
     * @var Array
     */
    protected $fillable = [
        'mobile_thumbnail',
        'desktop_thumbnail',

    ];

    /**
     * ############################
     *      Helper functions
     * ############################
     */

    // Panorama Image Helper Functions [BEGIN]
        /**
         * Get all panorama image records from databased.
         * @return ObjectRespond [ dara: data_result, message: result_message ]
         */
        public static function getPanoramaImages() {
            $respond = (object)[];

            try {
                $panoramaImages = PanoramaImage::all();
                $respond->data    = $panoramaImages;
                $respond->message = 'Successfully get panorama image records from database';
            } catch( Exception $ex ) {
                $respond->data    = false;
                $respond->message = 'Problem occured while trying to get hero image records from database!';
            }

            return $respond;
        }

        /**
         * Get specific panorama image record based on given id from databased.
         * @param  Integer $id
         * @return ObjectRespond [ dara: data_result, message: result_message ]
         */
        public static function getPanoramaImage( $id ) {
            $respond = (object)[];

            try {
                $panoramaImage = PanoramaImage::findOrFail( $id );
                $respond->data    = $panoramaImage;
                $respond->message = 'Hero image record found';
            } catch( Exception $ex ) {
                $respond->data    = false;
                $respond->message = 'Hero image record not found!';
            }

            return $respond;
        }

        /**
         * 
         */

    // Panorama Image Helper Functions [END]

    /**
     * #####################
     *      Relationship
     * #####################
     */

    /**
     * 
     */

    /**
     * ####################################
     *      Fast Validation Functions
     * ####################################
     */

    /**
     * 
     */

}
