<?php

namespace Common\Log;


interface ILogHandler
{
    public function write($msg);

}