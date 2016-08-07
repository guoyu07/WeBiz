<?php

namespace Common\Logs;

abstract class ErrorsBase extends \Exception
{
    abstract protected function categorize();

    abstract protected function log();

    protected function getLogFile
}