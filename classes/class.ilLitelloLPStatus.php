<?php
require_once 'Services/Tracking/classes/status/class.ilLPStatusPlugin.php';

use Codeception\Extension\Logger;
use minervis\Litello\Utils\LitelloTrait;
use srag\DIC\Litello\DICTrait;
use ilLitelloAPI;
use minervis\Litello\ObjectSettings\ObjectSettings;

/**
 * Class ilLitelloLPStatus
 * 
 * 
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
class ilLitelloLPStatus extends ilLPStatusPlugin
{
    use DICTrait;
    use LitelloTrait;
    /**
     * Get the LP status data directly from the database table
     * This can be called from ilObjLitello::getLP* methods avoiding loops
     *
     * @param $a_obj_id
     * @param $a_status
     * @return mixed
     */
    public static function getLPStatusDataFromDb($a_obj_id, $a_status)
    {
        return self::getLPStatusData($a_obj_id, $a_status);
    }

    /**
     * Get the LP data directly from the database table
     * This can be called from ilObjLitello::getLP* methods avoiding loops
     *
     * @param $a_obj_id
     * @param $a_user_id
     * @return int
     */
    public static function getLPDataForUserFromDb($a_obj_id, $a_user_id)
    {
        return self::getLPDataForUser($a_obj_id, $a_user_id);
    }


    /**
     * Track read access to the object
     * Prevents a call of determineStatus() that would return "not attempted"
     * @see ilLearningProgress::_tracProgress()
     *
     * @param $a_user_id
     * @param $a_obj_id
     * @param $a_ref_id
     * @param string $a_obj_type
     */
    public static function trackAccess($a_user_id, $a_obj_id, $a_ref_id, $track_external_access = false)
    {
        require_once('Services/Tracking/classes/class.ilChangeEvent.php');
        ilChangeEvent::_recordReadEvent('xlto', $a_ref_id, $a_obj_id, $a_user_id);

        $status = self::getLPDataForUser($a_obj_id, $a_user_id);
        if ($status == self::LP_STATUS_NOT_ATTEMPTED_NUM || $status == self::LP_STATUS_IN_PROGRESS_NUM)
        {
            //$status = $track_external_access ? self::LP_STATUS_IN_PROGRESS_NUM : self::LP_STATUS_NOT_ATTEMPTED_NUM;
            self::writeStatus($a_obj_id, $a_user_id, $status);
            self::raiseEventStatic($a_obj_id, $a_user_id, $status,
                self::getPercentageForUser($a_obj_id, $a_user_id));
        }
    }

    /**
     * Track result from Litello
     *
     * @param $a_user_id
     * @param $a_obj_id
     * @param $a_status
     * @param $a_percentage
     */
    public static function trackResult($a_user_id, $a_obj_id, $a_status = self::LP_STATUS_IN_PROGRESS_NUM, $a_percentage)
    {
        self::writeStatus($a_obj_id, $a_user_id, $a_status, $a_percentage, true);
        self::raiseEventStatic($a_obj_id, $a_user_id, $a_status, $a_percentage);
    }

    /**
     * Static version if ilLPStatus::raiseEvent
     * This function is just a workaround for PHP7 until ilLPStatus::raiseEvent is declared as static
     *
     * @param $a_obj_id
     * @param $a_usr_id
     * @param $a_status
     * @param $a_percentage
     */
    protected static function raiseEventStatic($a_obj_id, $a_usr_id, $a_status, $a_percentage)
    {
        global $ilAppEventHandler;

        $ilAppEventHandler->raise("Services/Tracking", "updateStatus", array(
            "obj_id" => $a_obj_id,
            "usr_id" => $a_usr_id,
            "status" => $a_status,
            "percentage" => $a_percentage
        ));
    }

    public static function getProgressData($object, $usr_id = 0, $obj_id = 0, $update_all = 0)
    {
        if(!$object && $obj_id == 0 && $usr_id = 0){
            throw new Exception("E-Book or User should be provided first");
        }
        
        $api = new ilLitelloAPI($object);
        $data = $api->getProgressData();
        foreach($data as $userlp){
            $login = $userlp->userId;
            $usr_id = self::dic()->user()->getUserIdByLogin($login);
            $obj_id = ObjectSettings::where(["book_id" => $userlp->bookId])->first()->getObjId();
            if(!$obj_id || !$usr_id || $usr_id == 0) continue;
            $status = boolval($userlp->opened) ? self::LP_STATUS_COMPLETED_NUM : self::LP_STATUS_IN_PROGRESS_NUM;
            $ilias_lp_status = self::getLPDataForUser($obj_id, $usr_id);
            if ($ilias_lp_status == self::LP_STATUS_NOT_ATTEMPTED_NUM || $ilias_lp_status == self::LP_STATUS_IN_PROGRESS_NUM){
                //
                self::trackAccess($usr_id,$obj_id, $object->getRefId(),true);
            }
            $percentage = ($status == self::LP_STATUS_COMPLETED_NUM) ? 1.0 : 0.0;
            self::trackResult($usr_id, $obj_id, $status, $percentage);
                    
        }
    }
}