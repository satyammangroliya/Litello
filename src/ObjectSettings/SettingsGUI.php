<?php

namespace minervis\Litello\ObjectSettings;

use \srag\CustomInputGUIs\Litello\PropertyFormGUI\PropertyFormGUI;
use ilImageFileInputGUI;
use ilLitelloPlugin;
use ilNumberInputGUI;
use ilObjLitello;
use ilTextInputGUI;
use ilObjLitelloGUI;
use ilTextAreaInputGUI;
use ilCheckboxInputGUI;
use minervis\Litello\Utils\LitelloTrait;
use srag\CustomInputGUIs\Litello\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\Litello\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\DIC\Litello\DICTrait;

/**
 * Class SettingsGUI
 *
 *
 * @package minervis\Litello\ObjectSettings
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 * 
 * @ilCtrl_isCalledBy SettingsGUI: ilUIPluginRouterGUI, PropertyFormGUI
 * @ilCtrl_Calls ilUIPluginRouterGUI: SettingsGUI
 */

class SettingsGUI extends ObjectPropertyFormGUI
{
    
    use LitelloTrait;
    const SAVE = "save";
    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    const LANG_MODULE = ilObjLitelloGUI::LANG_MODULE_SETTINGS;


    protected $settings ;
    protected $object;

    public function __construct($parent, ObjectSettings $settings, ilObjLitello $object)
    {
        $this->settings = $settings;
        $this->litello_object = $object;
        parent::__construct($parent, $object);        
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilObjLitelloGUI::CMD_PROPERTIES_STORE, $this->txt("save"));
    }
    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            'title'     =>[
                self::PROPERTY_CLASS=>ilTextInputGUI::class,
                "setTitle"            =>$this->txt("title"),
                self::PROPERTY_REQUIRED => true
            ],
            'description'     =>[
                self::PROPERTY_CLASS=>ilTextAreaInputGUI::class,
                "setTitle"            =>$this->txt("description"),
                self::PROPERTY_REQUIRED => false
            ],
            "online"               =>[
                self::PROPERTY_CLASS=>ilCheckboxInputGUI::class,
                  "setTitle"             => $this->txt("online")
            ],
            'book_id'     =>[
                self::PROPERTY_CLASS=>ilNumberInputGUI::class,
                "setTitle"            =>$this->txt("book_id")
            ],
            'tile_image'  =>[
                self::PROPERTY_CLASS=>ilImageFileInputGUI::class,
                "setTitle"            => "Litello (Sign)",
                
            ]
        ];

    }

    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId("settings_form");
        
    }
    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->litello_object->getTitle());
    }
    /**
     * @inheritDoc
     *
     * @deprecated
     */
    protected function storeValue(string $key, $value)/*: void*/
    {

        switch ($key) {
            default:
                Items::setter($this->object, $key, $value);
                break;
        }
    }
}