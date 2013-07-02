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
namespace Bach\ApacheLogRiak\Config;

use Symfony\Component\Yaml\Parser;

class Config
{
    /**
     * @var string
     */
    public $logDirectory;

    /**
     * @var SingleLogConfig[]
     */
    public $logs = array();

    /**
     * @var string
     */
    public $riakHost = "localhost";

    /**
     * @var int
     */
    public $riakPort = 8087;

    /**
     * @var null|string
     */
    public $statusFile = null;

    /**
     * @param string $filepath
     */
    public function loadFromYaml($filepath)
    {
        $parser = new Parser();
        $value = $parser->parse(file_get_contents($filepath));
        $this->statusFile = $value['status_file'];

        $this->logDirectory = $value['input']['directory'];
        $this->logs = array();
        foreach ($value['input']['logs'] as $log) {
            $scnf = new SingleLogConfig($log);
            $scnf->logtype = $log['type'];
            $scnf->filemask = $log['mask'];
            $scnf->bucket = $log['bucket'];
            $this->logs[] = $scnf;
        }
        $this->riakHost = $value['riak']['host'];
        $this->riakPort = intval($value['riak']['port']);

    }
}