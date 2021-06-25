<?php

namespace minervis\Litello\ObjectSettings;

use minervis\Litello\ObjectSettings\Form\FormBuilder;
use minervis\Litello\Utils\LitelloTrait;
use ilLitelloPlugin;
use ilObjLitello;
use ilObjLitelloGUI;
use srag\DIC\Litello\DICTrait;

/**
 * Class Factory
 *
 *
 * @package minervis\Litello\ObjectSettings
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
final class Factory
{

    use DICTrait;
    use LitelloTrait;

    const PLUGIN_CLASS_NAME = ilLitelloPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
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
     * @param ilObjLitelloGUI $parent
     * @param ilObjLitello    $object
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(ilObjLitelloGUI $parent, ilObjLitello $object) : FormBuilder
    {
        $form = new FormBuilder($parent, $object);

        return $form;
    }


    /**
     * @return ObjectSettings
     */
    public function newInstance() : ObjectSettings
    {
        $object_settings = new ObjectSettings();

        return $object_settings;
    }
}
