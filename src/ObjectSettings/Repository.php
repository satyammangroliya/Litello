<?php

namespace minervis\Litello\ObjectSettings;

use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use srag\DIC\Litello\DICTrait;

/**
 * Class Repository
 *
 *
 * @package minervis\Litello\ObjectSettings
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
final class Repository
{

    use DICTrait;
    use LitelloTrait;

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var ObjectSettings[]
     */
    protected $object_settings_by_id = [];


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param ObjectSettings $object_settings
     *
     * @return ObjectSettings
     */
    public function cloneObjectSettings(ObjectSettings $object_settings) : ObjectSettings
    {
        return $object_settings->copy();
    }


    /**
     * @param ObjectSettings $object_settings
     */
    public function deleteObjectSettings(ObjectSettings $object_settings) : void
    {
        $object_settings->delete();

        unset($this->object_settings_by_id[$object_settings->getObjId()]);
    }


    /**
     * @internal
     */
    public function dropTables() : void
    {
        self::dic()->database()->dropTable(ObjectSettings::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_id
     *
     * @return ObjectSettings|null
     */
    public function getObjectSettingsById(int $obj_id) : ?ObjectSettings
    {
        if ($this->object_settings_by_id[$obj_id] === null) {
            $this->object_settings_by_id[$obj_id] = ObjectSettings::where([
                "obj_id" => $obj_id
            ])->first();
        }

        return $this->object_settings_by_id[$obj_id];
    }


    /**
     * @internal
     */
    public function installTables() : void
    {
        ObjectSettings::updateDB();
    }


    /**
     * @param ObjectSettings $object_settings
     */
    public function storeObjectSettings(ObjectSettings $object_settings) : void
    {
        $object_settings->store();

        $this->object_settings_by_id[$object_settings->getObjId()] = $object_settings;
    }
}
