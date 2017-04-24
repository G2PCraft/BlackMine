<?php
/**
 * src/pocketmine/Thread.php
 *
 * @package default
 */


/*
 *
 *  _                       _           _ __  __ _
 * (_)                     (_)         | |  \/  (_)
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___|
 *                     __/ |
 *                    |___/
 *
 * This program is a third party build by ImagicalMine.
 *
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalmine.net/
 *
 *
*/

namespace pocketmine;

/**
 * This class must be extended by all custom threading classes
 */
abstract class Thread extends \Thread
{

    /** @var \ClassLoader */
    protected $classLoader;
    protected $isKilled = false;

    /**
     *
     * @return unknown
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }


    /**
     *
     * @param ClassLoader $loader (optional)
     */
    public function setClassLoader(\ClassLoader $loader = null)
    {
        if ($loader === null) {
            $loader = Server::getInstance()->getLoader();
        }
        $this->classLoader = $loader;
    }


    /**
     *
     */
    public function registerClassLoader()
    {
        if (!interface_exists("ClassLoader", false)) {
            require \pocketmine\PATH . "src/spl/ClassLoader.php";
            require \pocketmine\PATH . "src/spl/BaseClassLoader.php";
            require \pocketmine\PATH . "src/pocketmine/CompatibleClassLoader.php";
        }
        if ($this->classLoader !== null) {
            $this->classLoader->register(true);
        }
    }


    /**
     *
     * @param int     $options (optional)
     * @return unknown
     */
    public function start(int $options = PTHREADS_INHERIT_ALL)
    {
        ThreadManager::getInstance()->add($this);

        if (!$this->isRunning() and !$this->isJoined() and !$this->isTerminated()) {
            if ($this->getClassLoader() === null) {
                $this->setClassLoader();
            }
            return parent::start($options);
        }

        return false;
    }


    /**
     * Stops the thread using the best way possible. Try to stop it yourself before calling this.
     */
    public function quit()
    {
        $this->isKilled = true;

        $this->notify();

        if (!$this->isJoined()) {
            if (!$this->isTerminated()) {
                $this->join();
            }
        }

        ThreadManager::getInstance()->remove($this);
    }


    /**
     *
     * @return unknown
     */
    public function getThreadName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
