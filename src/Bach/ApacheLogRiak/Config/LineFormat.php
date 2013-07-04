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


class LineFormat
{
    /**
     * @var string
     */
    private $formatRegex;

    /**
     * @var Field[]
     */
    private $fields;

    public function __construct($formatSpecs)
    {
        $regex = $formatSpecs['regex'];
        if (substr($regex, 0, 6) == "!!str ") {
            $regex = substr($regex, 6);
        }
        $this->formatRegex = $regex;
        $this->fields = array();
        foreach ($formatSpecs['fields'] as $fieldCfg) {
            $this->fields[] = new Field($fieldCfg);
        }
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getFormatRegex()
    {
        return $this->formatRegex;
    }
}