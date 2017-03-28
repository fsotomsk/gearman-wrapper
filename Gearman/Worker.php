<?php
/**
 * Gearman RPC client
 *
 * User: fso
 * Date: 02.02.2016
 * Time: 19:58
 */

namespace Deep\Gearman;


class Worker
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
    protected function getServerLink()
    {
        if ($this->serverLink instanceof \GearmanWorker) {
            return $this->serverLink;
        }
        return $this->serverLink = new \GearmanWorker();
    }

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

    /**
     * Register RPC object methods
     */
    protected function register()
    {
        $name    = $this->getClassName();
        $methods = $this->getClassMethods();
        $object  = $this->object;
        foreach ($methods as $method) {
            $this->getServerLink()->addFunction("$name::$method",
                function(\GearmanJob $job) use ($object, $method) {
                    $arguments = json_decode( $job->workload(), true );
                    $result = json_encode(
                        call_user_func_array([$object, $method], $arguments)
                    );
                    $job->sendComplete($result);
                    return $result;
                });
        }
    }

    /**
     * Register object methods and run worker
     */
    public function run()
    {
        $this->register();
        while ($this->getServerLink()->work());
    }
}