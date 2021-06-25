<?php

namespace minervis\Litello\Config\Form;

use minervis\Litello\Config\ConfigCtrl;
use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use srag\CustomInputGUIs\Litello\FormBuilder\AbstractFormBuilder;

/**
 * Class FormBuilder
 *
 * @package minervis\Litello\Config\Form
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
class FormBuilder extends AbstractFormBuilder
{

    use LitelloTrait;

    const KEY_CUSTOMER = "customer";
    const KEY_ACCESS_KEY= "access_key";
    const KEY_SECRET_KEY="secret_key";
    const KEY_PROXY_HOST = "proxy_host";
    const KEY_PROXY_PORT = "proxy_port";
    const KEY_WEBREADER = "webreader";

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;


    /**
     * @inheritDoc
     *
     * @param ConfigCtrl $parent
     */
    public function __construct(ConfigCtrl $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ConfigCtrl::CMD_UPDATE_CONFIGURE => self::plugin()->translate("save", ConfigCtrl::LANG_MODULE)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $config=self::litello()->config();
        $data = [
            self::KEY_CUSTOMER => $config->getValue(self::KEY_CUSTOMER),
            self::KEY_ACCESS_KEY => $config->getValue(self::KEY_ACCESS_KEY),
            self::KEY_SECRET_KEY => $config->getValue(self::KEY_SECRET_KEY),
            self::KEY_PROXY_HOST => $config->getValue(self::KEY_PROXY_HOST),
            self::KEY_PROXY_PORT => $config->getValue(self::KEY_PROXY_PORT),
            self::KEY_WEBREADER  => $config->getValue(self::KEY_WEBREADER)
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $input_fields=self::dic()->ui()->factory()->input()->field();
        $fields = [
            self::KEY_CUSTOMER => $input_fields->text($this->configTranslate(self::KEY_CUSTOMER))->withRequired(true)
                                    ->withByline($this->configTranslate("customer_info")),
            self::KEY_ACCESS_KEY => $input_fields->text($this->configTranslate(self::KEY_ACCESS_KEY))->withRequired(true),
            self::KEY_SECRET_KEY => $input_fields->text($this->configTranslate(self::KEY_SECRET_KEY))->withRequired(true),
            self::KEY_PROXY_HOST  => $input_fields->text($this->configTranslate( self::KEY_PROXY_HOST))
                                    ->withByLine($this->configTranslate("proxy_info")),
            self::KEY_PROXY_PORT => $input_fields->numeric($this->configTranslate(self::KEY_PROXY_PORT))
                                    ->withByline($this->configTranslate("proxy_info")),
            self::KEY_WEBREADER  => $input_fields->text($this->configTranslate(self::KEY_WEBREADER))
                                    ->withByline($this->configTranslate("webreader_info")),                       
            

        ];

        return $fields;
    }
    private function configTranslate(string $key)
    {
        return self::plugin()->translate($key, ConfigCtrl::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("configuration", ConfigCtrl::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data) : void
    {
        foreach($data as $key=>$value){
            self::litello()->config()->setValue($key, strval($value));
        }
        
    }
}
