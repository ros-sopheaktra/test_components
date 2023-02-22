<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultUserDownloadPdf extends Model
{
    use HasFactory;

     /**
     * Table name.
     * @var String
     */
    protected $table = 'default_user_donwload_pdf';

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
        'user_id',

    ];

     /**
     * ########################
     *      Helper Functions
     * ########################
     */

        /**
         * Find default user download pdf setting
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function getDefaultUserSetting($id){
            $respond = (object)[];
            
            try {
                $user = DefaultUserDownloadPdf::findOrFail($id);
                $respond->data    = $user;
                $respond->message = 'Success...!';
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage();
            }

            return $respond;
        }

        /**
         * Store user download pdf for online sales.
         * @return RespondObject [ data: result_data, message: result_message ] 
         */
        public static function storeUser($id){
            $respond = (object)[];
            
            try {
                $user_onlines = DefaultUserDownloadPdf::all();
                if(count($user_onlines) == 0){
                    $online_user = new DefaultUserDownloadPdf([
                        'user_id' => $id,
                    ]);
                    $online_user->save();
                }else{
                    $online_user = DefaultUserDownloadPdf::findOrFail(1);
                    $online_user->user_id = $id;
                    $online_user->update();
                }
                $respond->message = 'Success...!';
                $respond->data    = true;
            } catch(Exception $e) {
                $respond->data    = false;
                $respond->message = $e->getMessage();
            }

            return $respond;
        }

     /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * One default user download pdf to one user relationship.
     * @return App\Models\User
     */
    public function user(){
        return $this->belongsTo(
            User::class,
            'user_id',
        );
    }

}
