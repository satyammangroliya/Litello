<?php

namespace minervis\Litello\Config;

use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use ilUtil;
use srag\DIC\Litello\DICTrait;

/**
 * Class ConfigCtrl
 *
 *
 * @package           minervis\Litello\Config
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 *
 * @ilCtrl_isCalledBy minervis\Litello\Config\ConfigCtrl: ilLitelloConfigGUI
 */
class ConfigCtrl
{

    use DICTrait;
    use LitelloTrait;

    const CMD_CONFIGURE = "configure";
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    const LANG_MODULE = "config";
    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    const TAB_CONFIGURATION = "configuration";


    /**
     * ConfigCtrl constructor
     */
    public function __construct()
    {
        error_log("ConfigCtrl class initialized");
    }


    /**
     *
     */
    public static function addTabs() : void
    {
        self::dic()->tabs()->addTab(self::TAB_CONFIGURATION, self::plugin()->translate("configuration", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
                    case self::CMD_UPDATE_CONFIGURE:
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
        self::dic()->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = self::litello()->config()->factory()->newFormBuilderInstance($this);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }


    /**
     *
     */
    protected function updateConfigure() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = self::litello()->config()->factory()->newFormBuilderInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("configuration_saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_CONFIGURE);
    }
}
