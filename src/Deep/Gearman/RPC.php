<?php
/**
 * User: fso
 * Date: 29.03.2017
 * Time: 13:07
 */

namespace Deep\Gearman;


abstract class RPC
{
    /**
     * @var \GearmanWorker|\GearmanClient
     */
    protected $serverLink = null;

    /**
     * Target object
     * @var null
     */
    protected $object     = null;

    /**
     * @var array
     */
    protected $reflection = [];

    /**
     * Disabled public methods
     * @var array
     */
    protected $rpcDisabledMethods   = ['__construct', '__call', 'run'];

    /**
     * Worker constructor.
     * @param $object
     * @param array $serverNames
     */
    public function __construct($object, array $serverNames=[])
    {
        $this->setObject($object);
        $this->setServerNames($serverNames ?: ['localhost']);
    }

    /**
     * Get gearman server instance (Worker or Client)
     * @return \GearmanClient|\GearmanWorker
     */
    abstract protected function getServerLink();

    /**
     * Add gearman servers
     * @param array $serverNames
     */
    protected function setServerNames(array $serverNames)
    {
        shuffle($serverNames);
        $this->getServerLink()->addServers(
            implode(',', $serverNames)
        );
    }

    /**
     * Set RPC target object
     * @param $object
     * @throws \Exception
     */
    protected function setObject($object)
    {
        if (!is_object($object)) {
            throw new \Exception("setObject failed: invalid object");
        }
        $this->object = $object;
    }

    /**
     * Get reflection for object class
     * @return \ReflectionClass
     */
    protected function getReflection()
    {
        if ($this->reflection instanceof \ReflectionClass) {
            return $this->reflection;
        }
        return $this->reflection = new \ReflectionClass($this->object);
    }

    /**
     * Get object class name
     * @return mixed
     */
    protected function getClassName()
    {
        return $this->getReflection()->name;
    }

    /**
     * Get available public methods
     * @return array
     */
    protected function getClassMethods()
    {
        $methods = $this->getReflection()->getMethods(\ReflectionMethod::IS_PUBLIC);
        $list = [];
        foreach ($methods as $method) {
            $list[] = $method->name;
        }
        return array_diff($list, $this->rpcDisabledMethods);
    }
}