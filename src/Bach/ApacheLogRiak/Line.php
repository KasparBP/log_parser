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

namespace Bach\ApacheLogRiak;
use Bach\ApacheLogRiak\Config\LineFormat;

/**
 * Class LogLine
 * @package Bach\ApacheLogRiak
 */
class Line
{
    /**
     * @var Config\LineFormat
     */
    private $format;

    /**
     * @param LineFormat $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * @param string $lineData
     * @return array|null
     */
    public function parse($lineData)
    {
        if (strlen($lineData) > 0) {
            $unknownCnt = 0;
            $fieldNames = $this->format->getFieldNames();
            $matches = array();
            echo "$lineData".PHP_EOL;
            $matchCount = preg_match_all($this->format->getFormatRegex(), $lineData, $matches);
            if ($matchCount !== false && $matchCount > 0 && count($matches) > 1) {
                $result = array();
                for ($i=1; $i<count($matches); $i++) {
                    if (count($fieldNames) > $i) {
                        $name = $fieldNames[$i-1];
                    } else {
                        $name = "unknown$unknownCnt";
                        $unknownCnt++;
                    }
                    $result[$name] = $matches[$i];
                }
                return $result;
            } else {
                // TODO Log error
            }
        }
        return null;
    }
}