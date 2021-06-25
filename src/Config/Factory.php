<?php

namespace minervis\Litello\Config;

use minervis\Litello\Config\Form\FormBuilder;
use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use srag\ActiveRecordConfig\Litello\Config\AbstractFactory;

/**
 * Class Factory
 *
 *
 * @package minervis\Litello\Config
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
final class Factory extends AbstractFactory
{

    use LitelloTrait;

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    protected function __construct()
    {
        parent::__construct();
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
     * @param ConfigCtrl $parent
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(ConfigCtrl $parent) : FormBuilder
    {
        $form = new FormBuilder($parent);

        return $form;
    }
}
