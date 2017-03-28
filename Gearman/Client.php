<?php
/**
 * Gearman RPC client
 *
 * User: fso
 * Date: 02.02.2016
 * Time: 20:39
 */

namespace Deep\Gearman;


class Client extends Worker
{
    /**
     * Get gearman client instance
     * @inheritdoc
     * @return \GearmanClient|\GearmanWorker
     */
    protected function getServerLink()
    {
        if ($this->serverLink instanceof \GearmanClient) {
            return $this->serverLink;
        }
        return $this->serverLink = new \GearmanClient();
    }

    /**
     * @param $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments=[])
    {
        $name   = $this->getClassName();
        $result = $this->getServerLink()->doNormal("$name::$method", json_encode($arguments));
        return json_decode($result);
    }
}