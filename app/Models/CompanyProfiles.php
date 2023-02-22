<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfiles extends Model
{
    use HasFactory;

    /**
     * Table name 
     * @var String
     */
    protected $table = 'company_profiles';

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
        'logo_image',
        'home_logo_image',
        'favicon_image',
        'company_name',
        'url_name',
    ];

    /**
     * ########################
     *     Helper function
     * ########################
     */

     /**
     * Get logo records from database
     * @return RespondObject [ data: result_data, message: result_message ] 
     */
    public static function getLogoShop(){
        $respond = (object)[];
        
        try {
            $logo     = CompanyProfiles::latest()->first();
            if($logo == null){
                $logo = (object)[
                    'id'              => 0,
                    'company_name'    => 'Company',
                    'logo_image'      => 'img/icons/Little Me logo-02.png',
                    'home_logo_image' => 'img/icons/Little Me logo.png',
                    'favicon_iamge'   => 'img/icons/Little Me logo-02.png',
                ];
            }
            $respond->data = $logo;
            $respond->message = 'records found';
        } catch(Exception $ex) {
            $respond->data = false;
            $respond->message = $ex->getMessage();
        }

        return $respond;
    }  
}