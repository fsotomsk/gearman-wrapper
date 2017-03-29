<?php
/**
 * Gearman RPC client
 *
 * User: fso
 * Date: 02.02.2016
 * Time: 19:58
 */

namespace Deep\Gearman;


class Worker extends RPC
{
    /**
     * Get gearman server instance Worker
     * @inheritdoc
     * @return \GearmanWorker
     */
    protected function getServerLink()
    {
        if ($this->serverLink instanceof \GearmanWorker) {
            return $this->serverLink;
        }
        return $this->serverLink = new \GearmanWorker();
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