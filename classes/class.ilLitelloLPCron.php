<?php

use minervis\Litello\ObjectSettings\ObjectSettings;
require_once "Services/Cron/classes/class.ilCronJob.php";

/**
 * Class ilLitelloLPCron
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
class ilLitelloLPCron extends ilCronJob
{

    const CRON_JOB_ID = ilLitelloPlugin::PLUGIN_ID;
    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;

    private $client;
    private $settings;
    private $pl;
    
    public function __construct( ) 
    {
        
        $this->pl = ilLitelloPlugin::getInstance();
    }


    /**
     * @return string
     */
    public function getId() : string
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @return string
     */
    public function getTitle() : string
    {
        
        return ilLitelloPlugin::PLUGIN_NAME . ": " .  $this->pl->txt("cron_title");
    }


    /**
     * @return string
     */
    public function getDescription() : string
    {
        return ilLitelloPlugin::PLUGIN_NAME . ": " .  $this->pl->txt("cron_description");
    }


    /**
     * @return bool
     */
    public function hasAutoActivation() : bool
    {
        return true;
    }


    /**
     * @return bool
     */
    public function hasFlexibleSchedule() : bool
    {
        return true;
    }


    /**
     * @return int
     */
    public function getDefaultScheduleType() : ILIAS\Cron\Schedule\CronJobScheduleType
    {
        return ilCronJob::SCHEDULE_TYPE_DAILY;
    }


    /**
     * @return null
     */
    public function getDefaultScheduleValue() : ?int 
    {
        return 1;
    }



    /**
     * @inheritDoc
     */
    public function run() : ilCronJobResult
    {
        global $DIC;
        $cron_result = new ilCronJobResult();

        try {
            $obj_id = ObjectSettings::where(["book_id" => 314])->first()->getObjId();
            $object = ilObjectFactory::getInstanceByObjId($obj_id);
            if(!$object){
                throw new Exception("Object does not exist");
            }
            ilLitelloLPStatus::getProgressData($object);
        } catch (Exception $e) {
            $cron_result->setStatus(ilCronJobResult::STATUS_FAIL);
        }
        $cron_result->setStatus(ilCronJobResult::STATUS_OK);

        return $cron_result;
    }

}