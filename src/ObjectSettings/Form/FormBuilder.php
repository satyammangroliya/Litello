<?php

namespace minervis\Litello\ObjectSettings\Form;

use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use ilObjLitello;
use ilObjLitelloGUI;
use srag\CustomInputGUIs\Litello\FormBuilder\AbstractFormBuilder;

/**
 * Class FormBuilder
 *
 * @package minervis\Litello\ObjectSettings\Form
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use LitelloTrait;

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    /**
     * @var ilObjLitello
     */
    protected $object;


    /**
     * @inheritDoc
     *
     * @param ilObjLitelloGUI $parent
     * @param ilObjLitello    $object
     */
    public function __construct(ilObjLitelloGUI $parent, ilObjLitello $object)
    {
        $this->object = $object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ilObjLitelloGUI::CMD_SETTINGS_STORE  => self::plugin()->translate("save", ilObjLitelloGUI::LANG_MODULE_SETTINGS),
            ilObjLitelloGUI::CMD_MANAGE_CONTENTS => self::plugin()->translate("cancel", ilObjLitelloGUI::LANG_MODULE_SETTINGS)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "title"       => $this->object->getTitle(),
            "description" => $this->object->getLongDescription(),
            "online"      => $this->object->isOnline()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "title"       => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("title", ilObjLitelloGUI::LANG_MODULE_SETTINGS))->withRequired(true),
            "description" => self::dic()->ui()->factory()->input()->field()->textarea(self::plugin()->translate("description", ilObjLitelloGUI::LANG_MODULE_SETTINGS)),
            "online"      => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("online", ilObjLitelloGUI::LANG_MODULE_SETTINGS))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("settings", ilObjLitelloGUI::LANG_MODULE_SETTINGS);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data) : void
    {
        $this->object->setTitle(strval($data["title"]));
        $this->object->setDescription(strval($data["description"]));
        $this->object->setOnline(boolval($data["online"]));

        $this->object->update();
    }
}
