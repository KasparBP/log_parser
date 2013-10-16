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

namespace Bach\ApacheLogRiak\Command;


use Bach\ApacheLogRiak\Config\Config;
use Bach\ApacheLogRiak\FileParser;
use Bach\ApacheLogRiak\Store\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{

    protected function configure()
    {
        $this->setName('log:import')
             ->addArgument('configuration', InputArgument::REQUIRED, 'Location of the configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getArgument('configuration');
        $output->writeln('Running log parser');
        $output->writeln("Reading configuration from $configFile");
        $config = new Config();
        $config->loadFromYaml($configFile);

        $writers = Factory::createLogWriters($config);
        $parser = new FileParser($config, $writers);
        $parser->processLogs();
    }

}