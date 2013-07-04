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
use Bach\ApacheLogRiak\Config\Field;
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
            $fields = $this->format->getFields();
            $matches = array();
            echo "$lineData".PHP_EOL;
            $matchCount = preg_match_all($this->format->getFormatRegex(), $lineData, $matches);
            if ($matchCount !== false && $matchCount > 0 && count($matches) > 1) {
                $result = array();
                // Loop through all found matches
                for ($i=1; $i<count($matches); $i++) {
                    // Do we gave a field descriptor from config
                    if (count($fields) >= $i) {
                        // Yes, then get the name and parse the value
                        $name = $fields[$i-1]->getName();
                        if (isset($matches[$i][0])) {
                            $value = $this->parseField($fields[$i-1], $matches[$i][0]);
                        } else {
                            $value = null;
                        }
                    } else {
                        // There was no descriptor for this field, make an unknown entry
                        $name = "unknown$unknownCnt";
                        $value = $matches[$i];
                        $unknownCnt++;
                    }
                    $result[$name] = $value;
                }
                return $result;
            } else {
                // TODO Log error
            }
        }
        return null;
    }

    /**
     * @param Field $field
     * @param string $value
     * @return mixed
     */
    private function parseField(Field $field, $value)
    {
        $type = $field->getType();
        switch ($type) {
            case Field::$FIELD_TYPE_STRING:
                return $value;
            case Field::$FIELD_TYPE_INT:
                return intval($value);
            case Field::$FIELD_TYPE_DATE:
                $format = $field->getFormat();
                if (isset($format)) {
                    $result = \DateTime::createFromFormat($format, $value);
                } else {
                    $result = new \DateTime($value);
                }
                if ($result !== false) {
                    return $result;
                } else {
                    // TODO Log error
                    return null;
                }
            default:
                return $value;
        }
    }
}