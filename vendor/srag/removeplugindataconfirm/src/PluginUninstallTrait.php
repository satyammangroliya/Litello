<?php

namespace srag\RemovePluginDataConfirm\Litello;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\Litello
 */
trait PluginUninstallTrait
{

    use BasePluginUninstallTrait;

    /**
     * @internal
     */
    protected final function afterUninstall()/*: void*/
    {

    }


    /**
     * @return bool
     *
     * @internal
     */
    protected final function beforeUninstall() : bool
    {
        return $this->pluginUninstall();
    }
}
