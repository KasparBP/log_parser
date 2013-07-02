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

namespace Bach\ApacheLogRiak\Status;

use \Bach\ApacheLogRiak\Config\Config;

class ImportStatus
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $status = array();

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->loadStatus($config->statusFile);
    }

    /**
     * @param string $statusFileName
     */
    private function loadStatus($statusFileName)
    {
        if (isset($statusFileName) && file_exists($statusFileName)) {
            $statusJson = file_get_contents($statusFileName);
            $this->status = json_decode($statusJson);
        }
    }

    /**
     * @param string $statusFileName
     */
    private function saveStatus($statusFileName)
    {
        if (isset($statusFileName)) {
            $statusJson = json_encode($this->status);
            file_put_contents($statusFileName, $statusJson, LOCK_EX);
        }
    }

    /**
     * @param string $type
     * @return Entry
     */
    private function getEntryFor($type)
    {
        $entry = $this->status[$type];
        if (is_null($entry)) {
            $entry = new Entry();
        }
        return $entry;
    }

    /**
     * @param string $type
     * @param \DateTime|null $time
     */
    public function setLastImportTime($type, $time)
    {
        $entry = $this->getEntryFor($type);
        $entry->latestImportTime = $time;
        $this->saveStatus($this->config->statusFile);
    }

    /**
     * @param $type
     * @return \DateTime|null
     */
    public function getLastImportTime($type)
    {
        $entry = $this->getEntryFor($type);
        return $entry->latestImportTime;
    }
}