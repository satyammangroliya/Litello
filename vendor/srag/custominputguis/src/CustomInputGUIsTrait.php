<?php

namespace srag\CustomInputGUIs\Litello;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\Litello
 */
trait CustomInputGUIsTrait
{

    /**
     * @return CustomInputGUIs
     */
    protected static final function customInputGUIs() : CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
