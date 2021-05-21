<?php

namespace srag\CustomInputGUIs\Litello\InputGUIWrapperUIInputComponent;

use ILIAS\Refinery\Constraint;
use ILIAS\Refinery\Custom\Constraint as CustomConstraint;

/**
 * Class InputGUIWrapperConstraint
 *
 * @package srag\CustomInputGUIs\Litello\InputGUIWrapperUIInputComponent
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class InputGUIWrapperConstraint extends CustomConstraint implements Constraint
{

    use InputGUIWrapperConstraintTrait;
}
