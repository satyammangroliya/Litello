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
class ilObjLitello extends ilObjectPlugin
{

    use DICTrait;
    use LitelloTrait;

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
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
}
