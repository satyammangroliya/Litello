<?php

namespace minervis\Litello\Utils;

use minervis\Litello\Repository;

/**
 * Trait LitelloTrait
 *
 *
 * @package minervis\Litello\Utils
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
trait LitelloTrait
{

    /**
     * @return Repository
     */
    protected static function litello() : Repository
    {
        return Repository::getInstance();
    }
}
