<?php

use minervis\Litello\ObjectSettings\ObjectSettings;
use minervis\Litello\Utils\LitelloTrait;
use srag\DIC\Litello\DICTrait;

/**
 * Class ilObjLitello
 *
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
class ilObjLitello extends ilObjectPlugin implements ilLPStatusPluginInterface
{

    use DICTrait;
    use LitelloTrait;

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;

    //Learning Progress Plugin
	const LP_INACTIVE = 0;
	const LP_Passed = 1;
	const LP_Completed = 2;
	const LP_CompletedAndPassed = 3;
	const LP_CompletedOrPassed = 4;
	const LP_UseScore = 8;
	const LP_NotApplicable = 99;
	// const LP_Failed = 3;
	// const LP_ACTIVE = 1;
	protected $lp_mode = self::LP_INACTIVE;

    const MOVEON_COMPLETED = 'Completed';
    const MOVEON_PASSED = 'Passed';
    const MOVEON_COMPLETED_OR_PASSED = 'CompletedOrPassed';
    const MOVEON_COMPLETED_AND_PASSED = 'CompletedAndPassed';
    const MOVEON_NOT_APPLICABLE = 'NotApplicable';
    /**
     * @var ObjectSettings|null
     */
    protected $object_settings = null;


    /**
     * ilObjLitello constructor
     *
     * @param int $a_ref_id
     */
    public function __construct(/*int*/ $a_ref_id = 0)
    {
        parent::__construct($a_ref_id);
    }


    /**
     * @inheritDoc
     */
    public function doCreate() : void
    {
        $this->object_settings = self::litello()->objectSettings()->factory()->newInstance();

        $this->object_settings->setObjId($this->id);

        self::litello()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public function doDelete() : void
    {
        if ($this->object_settings !== null) {
            self::litello()->objectSettings()->deleteObjectSettings($this->object_settings);
        }
    }


    /**
     * @inheritDoc
     */
    public function doRead() : void
    {
        $this->object_settings = self::litello()->objectSettings()->getObjectSettingsById(intval($this->id));
    }


    /**
     * @inheritDoc
     */
    public function doUpdate() : void
    {
        self::litello()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public final function initType() : void
    {
        $this->setType(ilLitelloPlugin::PLUGIN_ID);
    }


    /**
     * @return bool
     */
    public function isOnline() : bool
    {
        return $this->object_settings->isOnline();
    }


    /**
     * @param bool $is_online
     */
    public function setOnline(bool $is_online = true) : void
    {
        $this->object_settings->setOnline($is_online);
    }

    public function getBookID()
    {
        return $this->object_settings->getBookID();
    }

    public function setBookID(int $bookID = 0){
        $this->object_settings->setBookID($bookID);
    }


    /**
     * @inheritDoc
     *
     * @param ilObjLitello $new_obj
     */
    protected function doCloneObject(/*ilObjLitello*/ $new_obj, /*int*/ $a_target_id, /*?int*/ $a_copy_id = null) : void
    {
        $new_obj->object_settings = self::litello()->objectSettings()->cloneObjectSettings($this->object_settings);

        $new_obj->object_settings->setObjId($new_obj->id);

        self::litello()->objectSettings()->storeObjectSettings($new_obj->object_settings);
    }

    public function getLPCompleted()
    {
        return ilLitelloLPStatus::getLPStatusDataFromDb($this->getId(), ilLPStatus::LP_STATUS_COMPLETED_NUM);        
    }
    /**
     * @inheritDoc
     */
    public function getLPNotAttempted()
    {
        return ilLitelloLPStatus::getLPStatusDataFromDb($this->getId(), ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM);
    }
    
    /**
     * @inheritDoc
     */
    public function getLPFailed()
    {
        return ilLitelloLPStatus::getLPStatusDataFromDb($this->getId(), ilLPStatus::LP_STATUS_FAILED_NUM);
    }
    
    /**
     * @inheritDoc
     */
    public function getLPInProgress(){
        return ilLitelloLPStatus::getLPStatusDataFromDb($this->getId(), ilLPStatus::LP_STATUS_IN_PROGRESS_NUM);
    }
    
    /**
     * @inheritDoc
     */
    public function getLPStatusForUser($a_user_id)
    {
        return ilLitelloLPStatus::getLPDataForUserFromDb($this->getId(), $a_user_id);        
    }
	/**
	 * Track access for learning progress
	 */
	public function trackAccess($track_external_access = false)
	{
        $user = self::dic()->user();
		// track access for learning progress
		if ($user->getId() != ANONYMOUS_USER_ID)
		{
			ilLitelloLPStatus::trackAccess($user->getId(),$this->getId(), $this->getRefId(), $track_external_access);
		}
	}

}
