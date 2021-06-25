<?php

namespace minervis\Litello;

use minervis\Litello\Config\Repository as ConfigRepository;
use minervis\Litello\ObjectSettings\Repository as ObjectSettingsRepository;
use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use srag\DIC\Litello\DICTrait;

/**
 * Class Repository
 *
 *
 * @package minervis\Litello
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
     * @return ConfigRepository
     */
    public function config() : ConfigRepository
    {
        return ConfigRepository::getInstance();
    }


    /**
     *
     */
    public function dropTables() : void
    {
        $this->config()->dropTables();
        $this->objectSettings()->dropTables();
    }


    /**
     *
     */
    public function installTables() : void
    {
        $this->config()->installTables();
        $this->objectSettings()->installTables();
    }


    /**
     * @return ObjectSettingsRepository
     */
    public function objectSettings() : ObjectSettingsRepository
    {
        return ObjectSettingsRepository::getInstance();
    }
}
