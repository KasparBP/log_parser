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

/**
 * Class SingleLogConfig
 * @package Bach\ApacheLogRiak\Config
 */
class SingleLogConfig
{
    /**
     * @var string
     */
    private $logtype;

    /**
     * @var string
     */
    private $filemask;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var LineFormat
     */
    private $format;

    /** This logs config array
     * @param array $logConfigEntry
     */
    public function __construct($logConfigEntry)
    {
        $this->logtype = $logConfigEntry['type'];
        $this->filemask = $logConfigEntry['mask'];
        $this->bucket = $logConfigEntry['bucket'];
        $this->format = new LineFormat($logConfigEntry['line_format']);
    }

    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return string
     */
    public function getFilemask()
    {
        return $this->filemask;
    }

    /**
     * @return \Bach\ApacheLogRiak\Config\LineFormat
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getLogtype()
    {
        return $this->logtype;
    }

}