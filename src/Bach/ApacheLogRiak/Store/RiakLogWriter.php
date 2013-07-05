<?php
/*
   Copyright 2013: Kaspar Bach Pedersen

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
namespace Bach\ApacheLogRiak\Store;

use Bach\ApacheLogRiak\Config\Config;
use Riak\Connection;
use Riak\Exception\RiakException;
use Riak\Object;

class RiakLogWriter implements LogWriter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Config $config
     */
    public function __construct($config)
    {
        $this->connection = new Connection($config->riakHost, $config->riakPort);
    }

    public function write($bucketName, $key, $data)
    {
        $retries = 3;
        $bucket = $this->connection->getBucket($bucketName);
        $obj = new Object($key);
        $obj->setContentType('application/json');
        $obj->setContent($data);

        $done = false;
        while (!$done) {
            try {
                $bucket->put($obj);
                $done = true;
            } catch (RiakException $e) {
                echo "Failed to put object, exc: ".$e->getMessage().PHP_EOL;
                sleep(1);
                if (--$retries <= 0) {
                    break;
                }
                echo "Retrying".PHP_EOL;
            }
        }
    }
}