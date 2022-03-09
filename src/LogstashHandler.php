<?php

namespace TridevsIO\LaravelLogstashDriver;

use Cocur\Slugify\Slugify;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class LogstashHandler extends AbstractProcessingHandler
{
    /**
     * @var array $config
     */
    private array $config;

    /**
     * @param array $with
     * @param $level
     * @param $bubble
     */
    public function __construct(array $with, $level = Logger::DEBUG, $bubble = true)
    {
        $this->config = $with;
        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {
        $text = $record["message"];
        /**
         * @var \Exception $exception
         */
        $exception = $record["context"]["exception"] ?? null;
        if ($exception) {
            $record["context"] = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
                'code' => $exception->getCode(),
            ];
            $record["level"] = $exception->getCode() > 0 ? $exception->getCode() : $record["level"];
            $text = $exception->getMessage();
        }
        $re = '/("|\')data:image(.+);base64,(.+)/m';
        $subst = '"[IMAGE FILE]"';
        $text = preg_replace($re, $subst, $text);

        $json = [
            "appName" => (new Slugify)->slugify($record["channel"]),
            "errorCode" => $record["level"],
            "severity" => $record["level_name"],
            "context" => $record["context"],
            "message" => $text
        ];
        $message = json_encode($json);

        $host = $this->config['host'];
        $port = "" . $this->config['port'];

        if (!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }

        if (!socket_connect($sock, $host, $port)) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Could not connect: [$errorcode] $errormsg \n");
        }


        //Send the message to the server
        if (!socket_send($sock, $message, strlen($message), 0)) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Could not send data: [$errorcode] $errormsg \n");
        }
        socket_close($sock);
    }

}
