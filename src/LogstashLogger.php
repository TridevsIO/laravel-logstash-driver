<?php

namespace TridevsIO\LaravelLogstashDriver;

use Monolog\Logger;

class LogstashLogger {
    /**
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger($config['appName'] ?? 'MytestApp' );
        $handler = new LogstashHandler($config, Logger::DEBUG,true);
        $processor = new LogstashProcessor($config['extra'] ?? []);
        $logger->pushHandler($handler);
        $logger->pushProcessor($processor);
        return $logger;
    }
}
