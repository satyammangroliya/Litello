<?php

require_once __DIR__ . "/../vendor/autoload.php";

use minervis\Litello\Utils\LitelloTrait;
use ILIAS\DI\Container;
use srag\CustomInputGUIs\Litello\Loader\CustomInputGUIsLoaderDetector;
use srag\DevTools\Litello\DevToolsCtrl;
use srag\RemovePluginDataConfirm\Litello\RepositoryObjectPluginUninstallTrait;

/**
 * Class ilLitelloPlugin
 *
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
class ilLitelloPlugin extends ilRepositoryObjectPlugin
{

    use RepositoryObjectPluginUninstallTrait;
    use LitelloTrait;

    const PLUGIN_CLASS_NAME = self::class;
    const PLUGIN_ID = "xlto";
    const PLUGIN_NAME = "Litello";
    /**
     * @var self|null
     */

    protected static $instance = null;
    public static ?ilComponentRepositoryWrite $cached_component_repository = null;
    public static $cached_id = 0;
    /**
     * ilLitelloPlugin constructor
     */
    public function __construct(ilDBInterface $db, ilComponentRepositoryWrite $component_repository, string $id)
    {
        parent::__construct($db, $component_repository, $id);
        self::$cached_id = $id;
        self::$cached_component_repository = $component_repository;

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self(self::ildic()->database(), self::$cached_component_repository, self::$cached_id);
        }

        return self::$instance;
    }

    /**
     * Returns the Dependency Injection Container (DIC)
     *
     * @return \ILIAS\DI\Container
     */
    public static function ildic() : \ILIAS\DI\Container
    {
        global $DIC;

        return $DIC;
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }


    /**
     * @inheritDoc
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null) : void
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        DevToolsCtrl::installLanguages(self::plugin());
    }


    /**
     * @inheritDoc
     */
    protected function deleteData() : void
    {
        self::litello()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return false;
    }
}
