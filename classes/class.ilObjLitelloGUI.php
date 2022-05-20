<?php
include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php"); 

use minervis\Litello\Utils\LitelloTrait;
use srag\DIC\Litello\DICTrait;
use minervis\Litello\ObjectSettings\ObjectSettings;

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
 * @ilCtrl_Calls      ilObjLitelloGUI: ilLearningProgressGUI
 * @ilCtrl_Calls      ilObjLitelloGUI: ilLPListOfObjectsGUI
 */
class ilObjLitelloGUI extends ilObjectPluginGUI
{

    use DICTrait;
    use LitelloTrait;

    const LP_SESSION_ID = "xlto_lp_session_state";
    const CMD_MANAGE_CONTENTS = "manageContents";
    const CMD_PERMISSIONS = "perm";
    const CMD_SETTINGS = "settings";
    const CMD_SETTINGS_STORE = "settingsStore";
    const CMD_PROPERTIES_STORE = "propertiesStore";
    const CMD_SHOW_CONTENTS = "showContents";
    const CMD_PROPERTIES = "properties";
    const CMD_LEARNING_PROGRESS = "showLearningProgress";
    const CMD_LAUNCH = "launch";
    const CMD_LP_UPDATE = "updateProgressData";
    const LANG_MODULE_OBJECT = "object";
    const LANG_MODULE_SETTINGS = "settings";
    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    const TAB_CONTENTS = "contents";
    const TAB_PERMISSIONS = "perm_settings";
    const TAB_SETTINGS = "settings";
    const TAB_SHOW_CONTENTS = "show_contents";
    const TAB_PROPERTIES = "properties";
    const TAB_INFO = 'info_short';
    const TAB_LP = 'learning_progress';
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
            case 'illearningprogressgui':
                self::dic()->tabs()->setTabActive("learning_progress");
                include_once './Services/Tracking/classes/class.ilLearningProgressGUI.php';
                $new_gui = new ilLearningProgressGUI(
                    ilLearningProgressGUI::LP_CONTEXT_REPOSITORY,
                    $this->object->getRefId(),
                    $_GET['user_id'] ? $_GET['user_id'] : $GLOBALS['ilUser']->getId()
                );
                $this->ctrl->forwardCommand($new_gui);
                break;
            case ilLPListOfObjectsGUI::class:
                ilLitelloLPStatus::getProgressData($this->object);
                break;
            default:
                switch ($cmd) {
                    case self::CMD_SHOW_CONTENTS:
                    case self::CMD_LAUNCH:
                    
                        // Read commands
                        if (!ilObjLitelloAccess::hasReadAccess()) {
                            ilObjLitelloAccess::redirectNonAccess(ilRepositoryGUI::class);
                        }
                        $this->{$cmd}();
                        break;
                    case self::CMD_LEARNING_PROGRESS:
                        $this->setSubTabs(self::TAB_LP);
                        self::dic()->tabs()->activateTab(self::TAB_LP);
                        $this->{$cmd}();
                    case self::CMD_MANAGE_CONTENTS:
                    case self::CMD_SETTINGS:
                    case self::CMD_SETTINGS_STORE:
                    case self::CMD_PROPERTIES_STORE:
                    case self::CMD_PROPERTIES:
                    case self::CMD_LP_UPDATE:
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
         if (ilObjLitelloAccess::hasWriteAccess() || $this->checkPermissionBool('edit_learning_progress')){
            $this->setLPTab();
         }

        if (ilObjLitelloAccess::hasEditPermissionAccess()) {
            self::dic()->tabs()->addTab(self::TAB_PERMISSIONS, self::plugin()->translate(self::TAB_PERMISSIONS, "", [], false), self::dic()->ctrl()
                ->getLinkTargetByClass([
                    self::class,
                    ilPermissionGUI::class
                ], self::CMD_PERMISSIONS));
        }
        self::dic()->tabs()->manual_activation = false; // Show all tabs as links when no activation
        
    }

    private function setLPTab()
    {
        $dic = self::dic();
        if (ilObjUserTracking::_enabledLearningProgress() && ($this->checkPermissionBool("edit_learning_progress") || $this->checkPermissionBool("read_learning_progress")))
        {
            if ($this->checkPermissionBool("read_learning_progress"))
            {
				if (ilObjUserTracking::_enabledUserRelatedData())
				{
					self::dic()->tabs()->addTab(self::TAB_LP, 
                    self::plugin()->translate(self::TAB_LP, self::LANG_MODULE_OBJECT), 
                    $dic->ctrl()->getLinkTargetByClass(array(ilObjLitelloGUI::class,'ilLearningProgressGUI','ilLPListOfObjectsGUI')));//, 'showObjectSummary'
                    $dic->tabs()->activateTab(self::TAB_LP);
				}
				else
				{
					self::dic()->tabs()->addTab(
                        self::TAB_LP, self::plugin()->translate(self::TAB_LP), 
                        $dic->ctrl()->getLinkTargetByClass(array(ilObjLitelloGUI::class,ilLearningProgressGUI::class, ilLPListOfObjectsGUI::class), 
                        'showObjectSummary'
                    ));
                    $dic->tabs()->activateTab(self::TAB_LP);
				}
            }
			elseif ($this->checkPermissionBool("edit_learning_progress")) {
				self::dic()->tabs()->addTab(self::TAB_LP, self::plugin()->translate(self::TAB_LP), $dic->ctrl()->getLinkTarget($this,'showLearningProgress'));
                $dic->tabs()->activateTab(self::TAB_LP);
			}
            $dic->tabs()->activateTab(self::TAB_LP);

        }
    }
    /**
     * Set the sub tabs
     *
     * @param string	main tab identifier
     */
    protected function setSubTabs($a_tab)
    {
        $dic = self::dic();
        $lng = $dic->language();
        $lng->loadLanguageModule('trac');
        switch ($a_tab)
    	{
            case "learning_progress":
                $lng->loadLanguageModule('trac');
				
                if ($this->checkPermissionBool("read_learning_progress"))
                {

                    include_once("Services/Tracking/classes/class.ilObjUserTracking.php");
                    if (ilObjUserTracking::_enabledUserRelatedData())
                    {
                        $dic->tabs()->addSubTab("trac_objects", $lng->txt('trac_objects'), $ilCtrl->getLinkTargetByClass(array('ilObjLitelloGUI','ilLearningProgressGUI','ilLPListOfObjectsGUI')));
                    }
                    $dic->tabs()->addSubTab("trac_summary", $lng->txt('trac_summary'), $ilCtrl->getLinkTargetByClass(array(ilObjLitelloGUI::class,'ilLearningProgressGUI', 'ilLPListOfObjectsGUI'), 'showObjectSummary'));
                }
                break;
        }

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
        $btn = $this->getViewButton();
        self::dic()->toolbar()->addButtonInstance($btn);
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

    public function launch() : void
    {
        try{
            $this->object->trackAccess(true);
            $helper= new ilLitelloAPI($this->object, true);
            $auth_link= $helper->getAuthenticatedWebreaderURL();
            self::dic()->ctrl()->redirectToURL($auth_link);
            
        }catch(\Exception $e){
            ilUtil::sendFailure("E-Buch nicht erreichbar. Bitte dem Administrator Beschied sagen", true);
            self::dic()->ctrl()->redirectByClass(ilInfoScreenGUI::class);                      
        }

    }
    public function updateProgressData(){
        try {
            ilLitelloLPStatus::getProgressData($this->object);
            ilUtil::sendSuccess(self::plugin()->translate("learning_progress_updated", self::LANG_MODULE_SETTINGS), true);
        } catch (Exception $e) {
            ilUtil::sendFailure($e->getMessage(), true);
        }
        self::dic()->ctrl()->redirectByClass(ilInfoScreenGUI::class);        
    }
    /**
     *
     */
    protected function showContents() : void
    {
        $dic = self::dic();
        
        $dic->tabs()->activateTab(self::TAB_SHOW_CONTENTS);
        $btn = $this->getViewButton();
        $dic->toolbar()->addButtonInstance($btn);
       
    }
    public function getViewButton(){
        $dic = self::dic();
        $btn = ilLinkButton::getInstance();
        $btn->setUrl($dic->ctrl()->getLinkTargetByClass(self::class, self::CMD_LAUNCH));
        $btn->setTarget('_blank');
        $btn->setCaption(self::plugin()->translate("show", self::LANG_MODULE_SETTINGS), false);
        return $btn;
    }
    public function getSettingsLPButton(){
        $dic = self::dic();
        $btn = ilLinkButton::getInstance();
        $btn->setUrl($dic->ctrl()->getLinkTargetByClass(self::class, self::CMD_LP_UPDATE));
        $btn->setCaption(self::plugin()->translate("update_learning_progress", self::LANG_MODULE_SETTINGS), false);
        return $btn;
    }
    public function infoScreen()
    {
        $dic = self::dic();
        $lng = $this->lng;
        $ilTabs = $this->tabs;
        
        $ilTabs->activateTab("info_short");
        
        $this->checkPermission("visible");

        include_once("./Services/InfoScreen/classes/class.ilInfoScreenGUI.php");
        $info = new ilInfoScreenGUI($this);
        //$info->enablePrivateNotes();

        //add view button

        $btn = $this->getViewButton();
        
        $dic->toolbar()->addButtonInstance($btn);
        if(ilObjLitelloAccess::hasWriteAccess($this->object->getRefId())){
            $dic->toolbar()->addButtonInstance($this->getSettingsLPButton());
        }
        $this->addInfoScreenLPDetails($info);
        // general information
        $lng->loadLanguageModule("meta");

        $this->addInfoItems($info);

        // forward the command
        $dic->ctrl()->forwardCommand($info);
    }
    public function addInfoScreenLPDetails(&$info)
    {

        $dic = self::dic();
        $lng = $dic->language();
        $lng->loadLanguageModule('trac');

        $user_id = $dic->user()->getId();
        if ($user_id === ANONYMOUS_USER_ID){
            return;
        }
        $obj_id =$this->object->getId();
        $info->addSection(self::plugin()->translate(self::TAB_LP, self::LANG_MODULE_OBJECT));
        $status = ilLearningProgressBaseGUI::__readStatus($obj_id, $user_id);
        $status_path = ilLearningProgressBaseGUI::_getImagePathForStatus($status);
        $status_text = ilLearningProgressBaseGUI::_getStatusText($status, $lng);
        $info->addProperty(
            $lng->txt('trac_status'),
            ilUtil::img($status_path, $status_text) . " " . $status_text
        );

        include_once 'Services/Tracking/classes/class.ilLearningProgress.php';
                $progress = ilLearningProgress::_getProgress($user_id, $obj_id);
            
                if ($progress['access_time']) {
                    $info->addProperty(
                        $lng->txt('last_access'),
                        ilDatePresentation::formatDate(new ilDateTime($progress['access_time'], IL_CAL_UNIX))
                    );
                } else {
                    $info->addProperty($lng->txt('last_access'), $lng->txt('trac_not_accessed'));
                }
                $info->addProperty($lng->txt('trac_visits'), (int) $progress['visits']);
                if (ilObjectLP::supportsSpentSeconds($type)) {
                    $info->addProperty($lng->txt('trac_spent_time'), ilDatePresentation::secondsToString($progress['spent_seconds']));
                }

    }

    public function showLearningProgress()
    {
        $dic = self::dic();
        $dic->tabs()->activateTab(self::TAB_SHOW_CONTENTS);
        if (ilObjLitelloAccess::hasWriteAccess()){
            ilLitelloLPStatus::getProgressData($this->object);
        }
        $dic->ctrl()->redirectByClass(array(self::class, ilLearningProgressGUI::class));
       
    }
    private function setStatusToCompleted() {
		$this->setStatusAndRedirect(ilLPStatus::LP_STATUS_COMPLETED_NUM);
	}

	private function setStatusAndRedirect($status) {
		global $ilUser;
		$_SESSION[self::LP_SESSION_ID] = $status;
		ilLPStatusWrapper::_updateStatus($this->object->getId(), $ilUser->getId());
		$this->ctrl->redirect($this, $this->getStandardCmd());
	}

	protected function setStatusToFailed() {
		$this->setStatusAndRedirect(ilLPStatus::LP_STATUS_FAILED_NUM);
	}

	protected function setStatusToInProgress() {
		$this->setStatusAndRedirect(ilLPStatus::LP_STATUS_IN_PROGRESS_NUM);
	}

	protected function setStatusToNotAttempted() {
		$this->setStatusAndRedirect(ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM);
	}
}
