<?php

namespace Oracle\Oci\Common\Logging;

interface LogAdapterInterface
{
    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null);
    public function isLogEnabled($priority = LOG_INFO, $logName = null);
}

class NoOpLogAdapter implements LogAdapterInterface
{
    public function __construct()
    {
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        return false;
    }
}

abstract class AbstractLogAdapter implements LogAdapterInterface
{
    protected $debugLevel = LOG_INFO;
    protected $perLogName = [];

    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = []
    )
    {
        $this->debugLevel = $debugLevel;
        $this->perLogName = $perLogName;
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        $levelToUse = $this->debugLevel;
        if ($logName != null) {
            if (array_key_exists($logName, $this->perLogName)) {
                $levelToUse = $this->perLogName[$logName];
            } else {
                $components = explode('\\', $logName);
                $str = "";
                foreach ($components as $c) {
                    if (strlen($str) > 0) {
                        $str .= "\\";
                    }
                    $str .= $c;
                    if (array_key_exists($str, $this->perLogName)) {
                        $levelToUse = $this->perLogName[$str];
                    }
                }
            }
        }
        return ($priority <= $levelToUse);
    }
}


class EchoLogAdapter extends AbstractLogAdapter
{
    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = []
    )
    {
        parent::__construct($debugLevel, $perLogName);
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        if (!$this->isLogEnabled($priority, $logName)) {
            return;
        }
        switch ($priority) {
            case LOG_ALERT:
                $priorityStr = "[ALERT]";
                break;
            case LOG_CRIT:
                $priorityStr = "[CRIT]";
                break;
            case LOG_ERR:
                $priorityStr = "[ERR]";
                break;
            case LOG_WARNING:
                $priorityStr = "[WARN]";
                break;
            case LOG_DEBUG:
                $priorityStr = "[DEBUG]";
                break;
            default:
                $priorityStr = "[INFO]";
                break;
        }
        echo "$priorityStr ($logName) $message" . PHP_EOL;
    }
}
