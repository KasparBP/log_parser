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

class Field
{
    public static $FIELD_TYPE_STRING = 1;
    public static $FIELD_TYPE_DATE = 2;
    public static $FIELD_TYPE_INT = 3;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string|null
     */
    private $format;

    public function __construct($fieldConfig)
    {
        $this->name = $fieldConfig['name'];
        if (isset($fieldConfig['type'])) {
            $typeStr = $fieldConfig['type'];
            if (strcasecmp($typeStr, "string") === 0) {
                $this->type = self::$FIELD_TYPE_STRING;
            } else if (strcasecmp($typeStr, "date") === 0) {
                $this->type = self::$FIELD_TYPE_DATE;
            } else if (strcasecmp($typeStr, "int") === 0) {
                $this->type = self::$FIELD_TYPE_INT;
            }
        } else {
            $this->type = self::$FIELD_TYPE_STRING;
        }
        if (isset($fieldConfig['format'])) {
            $this->format = $fieldConfig['format'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getFormat()
    {
        return $this->format;
    }

}