<?php
include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php"); 

use minervis\Litello\Utils\LitelloTrait;
use srag\DIC\Litello\DICTrait;

/**
 * Class ilObjLitelloGUI
 *
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 *
 * @ilCtrl_isCalledBy ilObjLitelloGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjLitelloGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjLitelloGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLitelloGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjLitelloGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjLitelloGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjLitelloGUI: ilCommonActionDispatcherGUI
 */
class ilObjLitelloGUI extends ilObjectPluginGUI
{

    use DICTrait;
    use LitelloTrait;

    const CMD_MANAGE_CONTENTS = "manageContents";
    const CMD_PERMISSIONS = "perm";
    const CMD_SETTINGS = "settings";
    const CMD_SETTINGS_STORE = "settingsStore";
    const CMD_PROPERTIES_STORE = "propertiesStore";
    const CMD_SHOW_CONTENTS = "showContents";
    const CMD_PROPERTIES = "properties";
    const LANG_MODULE_OBJECT = "object";
    const LANG_MODULE_SETTINGS = "settings";
    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    const TAB_CONTENTS = "contents";
    const TAB_PERMISSIONS = "perm_settings";
    const TAB_SETTINGS = "settings";
    const TAB_SHOW_CONTENTS = "show_contents";
    const TAB_PROPERTIES = "properties";
    const TAB_INFO = 'info_short';
    /**
     * @var ilObjLitello
     */
    public $object;


    /**
     * @return string
     */
    public static function getStartCmd() : string
    {
        return self::CMD_SHOW_CONTENTS;
    }


    /**
     * @inheritDoc
     *
     * @param ilObjLitello $a_new_object
     */
    public function afterSave(/*ilObjLitello*/ ilObject $a_new_object) : void
    {
        parent::afterSave($a_new_object);
    }


    /**
     * @inheritDoc
     */
    public function getAfterCreationCmd() : string
    {
        return self::CMD_PROPERTIES;
    }


    /**
     * @inheritDoc
     */
    public function getStandardCmd() : string
    {
        return self::getStartCmd();
    }


    /**
     * @inheritDoc
     */
    public final function getType() : string
    {
        return ilLitelloPlugin::PLUGIN_ID;
    }


    /**
     * @inheritDoc
     */
    public function initCreateForm(/*string*/ $a_new_type) : ilPropertyFormGUI
    {
        $form = parent::initCreateForm($a_new_type);

        return $form;
    }


    /**
     * @param string $cmd
     */
    public function performCommand(string $cmd) : void
    {
       
        self::dic()->help()->setScreenIdComponent(ilLitelloPlugin::PLUGIN_ID);
        self::dic()->ui()->mainTemplate()->setPermanentLink(ilLitelloPlugin::PLUGIN_ID, $this->object->getRefId());

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                switch ($cmd) {
                    case self::CMD_SHOW_CONTENTS:
                        // Read commands
                        if (!ilObjLitelloAccess::hasReadAccess()) {
                            ilObjLitelloAccess::redirectNonAccess(ilRepositoryGUI::class);
                        }

                        $this->{$cmd}();
                        break;

                    case self::CMD_MANAGE_CONTENTS:
                    case self::CMD_SETTINGS:
                    case self::CMD_SETTINGS_STORE:
                    case self::CMD_PROPERTIES_STORE:
                    case self::CMD_PROPERTIES:
                        // Write commands
                        if (!ilObjLitelloAccess::hasWriteAccess()) {
                            ilObjLitelloAccess::redirectNonAccess($this);
                        }

                        $this->{$cmd}();
                        break;

                    default:
                        // Unknown command
                        ilObjLitelloAccess::redirectNonAccess(ilRepositoryGUI::class);
                        break;
                }
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function afterConstructor() : void
    {

    }


    /**
     *
     */
    protected function manageContents() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        // TODO: Implement manageContents
        $this->showContents();
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        self::dic()->tabs()->addTab(self::TAB_SHOW_CONTENTS, self::plugin()->translate("show_contents", self::LANG_MODULE_OBJECT), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_SHOW_CONTENTS));
        self::dic()->tabs()->addTab(self::TAB_INFO, self::plugin()->translate(self::TAB_INFO, self::LANG_MODULE_OBJECT),self::dic()->ctrl()
        ->getLinkTargetByClass(ilInfoScreenGUI::class));        

        if (ilObjLitelloAccess::hasWriteAccess()) {
            self::dic()->tabs()->addTab(self::TAB_PROPERTIES, self::plugin()->translate("settings", self::LANG_MODULE_SETTINGS), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_PROPERTIES));
        }

        if (ilObjLitelloAccess::hasEditPermissionAccess()) {
            self::dic()->tabs()->addTab(self::TAB_PERMISSIONS, self::plugin()->translate(self::TAB_PERMISSIONS, "", [], false), self::dic()->ctrl()
                ->getLinkTargetByClass([
                    self::class,
                    ilPermissionGUI::class
                ], self::CMD_PERMISSIONS));
        }

        self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
    }


    /**
     *
     */
    protected function settings() : void
    {
        
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::litello()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        self::output()->output($form);
    }
    /**
     *
     */
    protected function properties() : void
    {
        
        self::dic()->tabs()->activateTab(self::TAB_PROPERTIES);
        $settings = self::litello()->objectSettings()->factory()->newInstance();

        $form = self::litello()->objectSettings()->factory()->newPropertyFormInstance($this, $settings, $this->object);
        if ($this->object && $form){
            $form = $this->object_service->commonSettings()->legacyForm($form, $this->object)->addTileImage();
        }
        

        self::output()->output($form);
    }

    /**
     *
     */
    protected function settingsStore() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::litello()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE_SETTINGS), true);

        self::dic()->ctrl()->redirect($this, self::CMD_SETTINGS);
    }

    /**
     *
     */
    protected function propertiesStore() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_PROPERTIES);

        $settings = self::litello()->objectSettings()->factory()->newInstance();

        $form = self::litello()->objectSettings()->factory()->newPropertyFormInstance($this, $settings, $this->object);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }
        if ($this->object && $form){
            $this->object_service->commonSettings()->legacyForm($form, $this->object)->saveTileImage();
        }
        

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE_SETTINGS), true);

        self::dic()->ctrl()->redirect($this, self::CMD_PROPERTIES);
    }


    /**
     * @param string $html
     */
    protected function show(string $html) : void
    {
        if (!self::dic()->ctrl()->isAsynch()) {
            self::dic()->ui()->mainTemplate()->setTitle($this->object->getTitle());

            self::dic()->ui()->mainTemplate()->setDescription($this->object->getDescription());

            if (!$this->object->isOnline()) {
                self::dic()->ui()->mainTemplate()->setAlertProperties([
                    [
                        "alert"    => true,
                        "property" => self::plugin()->translate("status", self::LANG_MODULE_OBJECT),
                        "value"    => self::plugin()->translate("offline", self::LANG_MODULE_OBJECT)
                    ]
                ]);
            }
        }

        self::output()->output($html);
    }


    /**
     *
     */
    protected function showContents() : void
    {
        global $tpl;
        self::dic()->tabs()->activateTab(self::TAB_SHOW_CONTENTS);
        require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/Litello/classes/class.ilLitelloAPI.php";
        $helper= '';
        try{
            $helper= new ilLitelloAPI($this->object);
            $auth_link= $helper->getAuthenticatedWebreaderURL();
            $t =new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/Litello/templates/tpl.litello.html",true, true);
            $t->setVariable("LITELLO_AUTHENTICATE_LINK", $auth_link);
            $t->setVariable("OPEN_BOOK_LINK_TEXT", self::plugin()->translate("show", self::LANG_MODULE_SETTINGS));
            $this->show($t->get());
        }catch(\Exception $e){
            ilUtil::sendFailure("E-Buch nicht erreichbar. Bitte dem Administrator Beschied sagen", true);            
            $this->show('');

        }
       
    }
}
