<?php

namespace TridevsIO\LaravelLogstashDriver;

class LogstashProcessor
{
    /**
     * @var array $extra
     */
    private array $extra;

    /**
     * @param array $extra
     */
    public function __construct(array $extra)
    {
        $this->extra = $extra;
    }

    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record): array
    {
        $record['extra'] = $this->extra;
        return $record;
    }
}
