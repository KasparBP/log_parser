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

/**
 * Class Factory
 * @package Bach\ApacheLogRiak\Store
 */
class Factory {

    /**
     * Creates log writers as configured in config
     * @param \Bach\ApacheLogRiak\Config\Config $config
     * @return LogWriter[]
     */
    public static function createLogWriters(Config $config)
    {
        $result = array();
        $output = $config->output;
        foreach ($output as $currentName => $currentConfig) {
            // This can be done smart... but why bother all I wan't is riak for now :)
            if ($currentName == 'riak') {
                $result[] = new RiakLogWriter($currentConfig);
            }
        }
        return $result;
    }

}