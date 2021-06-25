<?php

namespace srag\DIC\Litello\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\Litello\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
