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

use Bach\ApacheLogRiak\Config\Config;
use Bach\ApacheLogRiak\Config\SingleLogConfig;
use Bach\ApacheLogRiak\Status\ImportStatus;
use Bach\ApacheLogRiak\Store\LogWriter;

class FileParser
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Status\ImportStatus
     */
    private $importStatus;

    /**
     * @var LogWriter[]
     */
    private $writers;

    /**
     * @param Config $config
     * @param LogWriter[] $writers
     */
    public function __construct($config, $writers)
    {
        $this->config = $config;
        $this->importStatus = new ImportStatus($config);
        $this->writers = $writers;
    }

    public function processLogs()
    {
        foreach ($this->config->logs as $log) {
            $this->processLogGroup($log);
        }
    }

    private function processLogGroup(SingleLogConfig $logConfig)
    {
        $lastLogTime = $this->importStatus->getLastImportTime($logConfig->getLogtype());
        $logLineCount = $this->importStatus->getProcessedLineCount($logConfig->getLogtype());
        $lastLogTimeForThisRun = $lastLogTime;
        $logDir = $this->config->logDirectory;

        foreach (glob($logDir . DIRECTORY_SEPARATOR . $logConfig->getFilemask()) as $logFilename) {
            // Find last modified time of this log file
            $lastModified = new \DateTime();
            $lastModifiedTimestamp = filemtime($logFilename);
            if ($lastModifiedTimestamp !== false) {
                $lastModified->setTimestamp($lastModifiedTimestamp);
            } else {
                $lastModified->setTimestamp(0);
            }
            // Check if the file has been modified since last run
            if (is_null($lastLogTime) || $lastModified >= $lastLogTime) {
                // Ok this log file has been modified since last run, now read it.
                $logFileLastProcessed = $this->processSingleLogFile($logFilename, $logConfig, $lastLogTime, $logLineCount);
                if (isset($logFileLastProcessed)) {
                    if (is_null($lastLogTimeForThisRun) || $logFileLastProcessed > $lastLogTimeForThisRun) {
                        $lastLogTimeForThisRun = $logFileLastProcessed;
                    }
                }
            }
        }
        // Save what date we got to in this run.
        $this->importStatus->setLastImportTime($logConfig->getLogtype(), $lastLogTimeForThisRun);
        $this->importStatus->setProcessedLineCount($logConfig->getLogtype(), $logLineCount);
    }

    /**
     * Process a single log file and return datetime of the last log line processed
     * @param $logFilename
     * @param SingleLogConfig $logConfig
     * @param \DateTime|null $lastLogTime
     * @param $processedLineCount
     * @return \DateTime
     */
    private function processSingleLogFile($logFilename, $logConfig, $lastLogTime, &$processedLineCount)
    {
        $lastLogTimeRead = new \DateTime();
        $lastLogTimeRead->setTimestamp(0);
        $handle = @fopen($logFilename, "r");
        if ($handle) {
            $lineFormat = $logConfig->getFormat();
            $bucket = $logConfig->getBucket();
            $line = new LineParser($lineFormat);
            while (($buffer = fgets($handle)) !== false) {
                $parsedData = $line->parse($buffer);
                if ($parsedData != null && is_array($parsedData)) {
                    $date = $this->findFirstDateTime($parsedData);
                    // Only save this log if it is never than last log time
                    if ($lastLogTime < $date) {
                        $key = $this->makeKeyFromParsedLine($buffer, $parsedData);
                        foreach ($this->writers as $writer) {
                            $writer->write($bucket, $key, $parsedData);
                        }
                        if ($lastLogTimeRead < $date) {
                            $lastLogTimeRead = $date;
                        }
                        $processedLineCount++;
                    }
                }
            }
        }
        return $lastLogTimeRead;
    }

    /**
     * @param string $rawLine
     * @param array $lineData
     * @return string
     */
    private function makeKeyFromParsedLine($rawLine, $lineData)
    {
        // Just find the first date time and use that for key
        // TODO Improve this
        $keyTime = new \DateTime();
        $found = false;
        foreach ($lineData as $item) {
            if ($item instanceof \DateTime) {
                $keyTime = $item;
                $found = true;
                break;
            }
        }
        $md5 = md5($rawLine);
        $shortenedMd5 = substr($md5, 0, 6);
        $stamp = $keyTime->getTimestamp();
        if (!$found) {
            echo "Did not find any log time in records, using now for line: '$lineData''".PHP_EOL;
        }
        return "$stamp-$shortenedMd5";
    }

    /**
     * @param array $parsedData
     * @return null
     * @return \DateTime|null
     */
    private function findFirstDateTime($parsedData)
    {
        foreach ($parsedData as $field) {
            if ($field instanceof \DateTime) {
                return $field;
            }
        }
        return null;
    }

}