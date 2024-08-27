<?php

require_once __DIR__ . "/../vendor/autoload.php";

use minervis\Litello\Config\ConfigCtrl;
use minervis\Litello\Utils\LitelloTrait;
use srag\DevTools\Litello\DevToolsCtrl;
use srag\DIC\Litello\DICTrait;

/**
 * Class ilLitelloConfigGUI
 *
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 * @version $Id$
 * @ilCtrl_IsCalledBy ilLitelloConfigGUI: ilObjComponentSettingsGUI
 * @ilCtrl_isCalledBy srag\DevTools\Litello\DevToolsCtrl: ilLitelloConfigGUI
 * @ilCtrl_Calls minervis\Litello\Config\ConfigCtrl: ilLitelloConfigGUI
 * @ilCtrl_Calls  ilLitelloConfigGUI: minervis\Litello\Config\ConfigCtrl 
 */
class ilLitelloConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use LitelloTrait;

    const CMD_CONFIGURE = "configure";
    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;


    /**
     * ilLitelloConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd) : void
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ConfigCtrl::class):
                self::dic()->ctrl()->forwardCommand(new ConfigCtrl());
                break;

            case strtolower(DevToolsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new DevToolsCtrl($this, self::plugin()));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function configure() : void
    {
        self::dic()->ctrl()->redirectByClass(ConfigCtrl::class, ConfigCtrl::CMD_CONFIGURE);
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        ConfigCtrl::addTabs();

        if (DEVMODE){
            DevToolsCtrl::addTabs(self::plugin());
        }

        self::dic()->locator()->addItem(ilLitelloPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }
}
