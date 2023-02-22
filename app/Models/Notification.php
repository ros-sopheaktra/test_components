<?php

namespace App\Models;

use Exception;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Notification extends Model
{
    use HasFactory;

    /**
     * Table name
     * @var String
     */
    public $table = 'notifications';

    /**
     * Primary key
     * @var String
     */
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     * @var Array
     */
    public $fillable = [
        'type',
        'description',
        'days',
        'notification_status',
        'color_code',
    ];
    
    /**
     * ############################
     *      Helper functions
     * ############################
     */

    /**
     * Notofication Type
     * @var Const_Array
     */
    protected const NOTIFICATION_TYPE = [
        1 => 'pre-alert',
        2 => 'closing-alert',
    ];

    /**
     * Notification Status
     * @var Const_Array
     */
    protected const NOTIFICATION_STATUS = [
        1 => 'before',
        2 => 'after',
    ];

    /**
     * Return the id of notificaation type
     * @param String $notificationType
     * @return Integer $notificationTypeId
     */
    protected static function getNotificationTypeId($notificationType) {
        return array_search($notificationType, self::NOTIFICATION_TYPE);
    }

    /**
     * Return the id of notification status
     * @param String $notificationStatus
     * @return Integer $notificationStatusId
     */
    protected static function getNotificationStatusId($notificationStatus){
        return array_search($notificationStatus, self::NOTIFICATION_STATUS);
    }

    /**
     * Check valid notification type.
     * @param String $notificationType
     * @param String $options [ type, status ]
     * @return Boolean false | String statusValue
     */
    public static function checkValidNotificationTypeOrStatus($notificationType, $options){
        $resultRespond = false;

        switch ($options) {
            case 'type':{
                $resultRespond = Notification::getNotificationTypeId($notificationType);
            } 
                break;
            case 'status': {
                $resultRespond = Notification::getNotificationStatusId($notificationType);
            }
                break;
        }

        if(!$resultRespond) {
            return $resultRespond;
        } else {
            return $notificationType;
        }
    }

    // Notification Helper Functions [BEGIN]

        /**
         * Get all notification records from database.
         * @return ObjectRespond [ data: data_result; message: result_message ]
         */
        public static function getNotifications(){
            $respond = (object)[];

            try {
                $notifications    = Notification::all();
                $respond->data    = $notifications;
                $respond->message = 'Notification records found';
            } catch(Exception $ex) {
                $respond->data = false;
                $respond->message = 'Problem occured while trying to get notification records!';
            }

            return $respond;
        }

        /**
         * Get specific notification record from database based on given id.
         * @param Integer $id
         * @return ObjectRespond [ data: data_result; message: result_message ]
         */
        public static function getNotification($id){
            $respond = (object)[];

            try {
                $notification = Notification::findOrFail($id);
                $respond->data = $notification;
                $respond->message = 'Notification record found';
            } catch(ModelNotFoundException $ex) {
                $respond->data = false;
                $respond->message = 'Notification record not found!';
            }

            return $respond;
        }

    // Notification Helper Functions [END]

    /**
     * Check number of day in between range of starting day set by function [01] 
     * to maximum number of days with value to check provide by parameters.
     * @param Integer $maxDays
     * @param Integer $days
     * @return ObjectRespond [ data: data_result; message: result_message ]
     */
    public static function validNumberOfDaysInBetween($maxDays, $days) {
        $minDays = 1;
        $respond = (object)[];

        if( ($minDays <= $days) && ($days >=$maxDays) ){
            $respond->data    = false;
            $respond->message = 'Days out of range, number of day should be inside the range of '.$minDays.'-'.$maxDays;
        } else {
            $respond->data    = $days;
            $respond->message = 'Days value is valid, in the range of '.$minDays.'-'.$maxDays;
        }

        return $respond;
    }
    
    /**
     * ########################
     *      Relationship
     * ########################
     */

    /**
     * Many notifications to many log histories (Polymorphic)
     * @return App/Model/LogHistory
     */
    public function logHistories(){
        return $this->morphToMany(
            LogHistory::class,
            'historyables',
        );
    }

    /**
     * 
     */

    /**
     * ##################################
     *      Fast Validation Functions
     * ##################################
     */

    /**
     * Validation request data
     * @param Integer $days
     * @param String  $notification_type
     * @param String  $notification_status
     * @return ObjectRespond [ data: data_result, message: result_message ]
     */
    public static function checkReuqestValidation($days, $notification_type, $notification_status){
        $respond = (object)[];

        // check valid number of day ( 01 - 365 )
        $daysResult = Notification::validNumberOfDaysInBetween(365, $days);
        if(!$daysResult->data){
            $respond = $daysResult;
            return $respond;
        }

        // check valid notification type
        $notificationTypeResult = Notification::checkValidNotificationTypeOrStatus($notification_type, 'type');
        if(!$notificationTypeResult){
            $respond->data    = false;
            $respond->message = 'Notification type not found or incorrect provided!';
            return $respond;
        }

        // check valid notification status
        $notificationStatusResult = Notification::checkValidNotificationTypeOrStatus($notification_status ,'status');
        if(!$notificationStatusResult){
            $respond->data = false;
            $respond->message = 'Notification status not found or incorrect provided!';
            return $respond;
        }

        $respond->data               = true;
        $respond->days               = $daysResult->data;
        $respond->notificationType   = $notificationTypeResult;
        $respond->notificationStatus = $notificationStatusResult;
        $respond->message            = 'All request values are valided.';

        return $respond;
    }

}
