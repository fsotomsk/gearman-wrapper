<?php
/**
 * Gearman RPC client
 *
 * User: fso
 * Date: 02.02.2016
 * Time: 20:39
 */

namespace Deep\Gearman;


class Client extends RPC
{
    /**
     * Get gearman server instance Client
     * @inheritdoc
     * @return \GearmanClient
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
     * @throws \ErrorException
     */
    public function __call($method, array $arguments=[])
    {
        $name    = $this->getClassName();
        $methods = $this->getClassMethods();
        if (!in_array($method, $methods)) {
            throw new \ErrorException("Undeclared method $method in class $name");
        }
        $result  = $this->getServerLink()->doNormal("$name::$method", json_encode($arguments));
        return json_decode($result);
    }
}