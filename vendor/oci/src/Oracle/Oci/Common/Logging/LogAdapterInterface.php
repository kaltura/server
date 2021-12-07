<?php

namespace Oracle\Oci\Common\Logging;

interface LogAdapterInterface
{
    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null);

    public function error($message, $logName = null, $extras = []);

    public function warn($message, $logName = null, $extras = []);

    public function info($message, $logName = null, $extras = []);

    public function debug($message, $logName = null, $extras = []);

    public function isLogEnabled($priority = LOG_INFO, $logName = null);

    public function isErrorEnabled($logName = null);

    public function isWarnEnabled($logName = null);

    public function isInfoEnabled($logName = null);

    public function isDebugEnabled($logName = null);
}

class NamedLogAdapterDecorator implements LogAdapterInterface
{
    private $logName;
    private $decoratedLogger;

    public function __construct(
        $logName,
        LogAdapterInterface $decoratedLogger
    ) {
        $this->logName = $logName;
        $this->decoratedLogger = $decoratedLogger;
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        return $this->decoratedLogger->log($message, $priority, $extras, $this->append($logName));
    }

    public function error($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->error($message, $this->append($logName), $extras);
    }

    public function warn($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->warn($message, $this->append($logName), $extras);
    }

    public function info($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->info($message, $this->append($logName), $extras);
    }

    public function debug($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->debug($message, $this->append($logName), $extras);
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        return $this->decoratedLogger->isLogEnabled($priority, $this->append($logName));
    }

    public function isErrorEnabled($logName = null)
    {
        return $this->decoratedLogger->isErrorEnabled($this->append($logName));
    }

    public function isWarnEnabled($logName = null)
    {
        return $this->decoratedLogger->isWarnEnabled($this->append($logName));
    }

    public function isInfoEnabled($logName = null)
    {
        return $this->decoratedLogger->isInfoEnabled($this->append($logName));
    }

    public function isDebugEnabled($logName = null)
    {
        return $this->decoratedLogger->isDebugEnabled($this->append($logName));
    }

    public function scope($logName)
    {
        return new NamedLogAdapterDecorator($this->append($logName), $this->decoratedLogger);
    }

    private function append($logName)
    {
        if (null == $logName || 0 == strlen($logName)) {
            return $this->logName;
        }

        return $this->logName.'\\'.$logName;
    }
}

class RedactingLogAdapterDecorator implements LogAdapterInterface
{
    private $decoratedLogger;
    private $maskRegexes;

    public function __construct(
        LogAdapterInterface $decoratedLogger,
        $maskRegexes = ['/(?<=keyId=)"(.*?)"/']
    ) {
        $this->maskRegexes = $maskRegexes;
        $this->decoratedLogger = $decoratedLogger;
    }

    public function redact($message)
    {
        foreach ($this->maskRegexes as $maskRegex) {
            $message = preg_replace($maskRegex, '<REDACTED>', $message);
        }

        return $message;
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        return $this->decoratedLogger->log($this->redact($message), $priority, $extras, $logName);
    }

    public function error($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->error($this->redact($message), $logName, $extras);
    }

    public function warn($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->warn($this->redact($message), $logName, $extras);
    }

    public function info($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->info($this->redact($message), $logName, $extras);
    }

    public function debug($message, $logName = null, $extras = [])
    {
        return $this->decoratedLogger->debug($this->redact($message), $logName, $extras);
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        return $this->decoratedLogger->isLogEnabled($priority, $logName);
    }

    public function isErrorEnabled($logName = null)
    {
        return $this->decoratedLogger->isErrorEnabled($logName);
    }

    public function isWarnEnabled($logName = null)
    {
        return $this->decoratedLogger->isWarnEnabled($logName);
    }

    public function isInfoEnabled($logName = null)
    {
        return $this->decoratedLogger->isInfoEnabled($logName);
    }

    public function isDebugEnabled($logName = null)
    {
        return $this->decoratedLogger->isDebugEnabled($logName);
    }
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

    public function isErrorEnabled($logName = null)
    {
        return false;
    }

    public function isWarnEnabled($logName = null)
    {
        return false;
    }

    public function isInfoEnabled($logName = null)
    {
        return false;
    }

    public function isDebugEnabled($logName = null)
    {
        return false;
    }

    public function error($message, $logName = null, $extras = [])
    {
    }

    public function warn($message, $logName = null, $extras = [])
    {
    }

    public function info($message, $logName = null, $extras = [])
    {
    }

    public function debug($message, $logName = null, $extras = [])
    {
    }
}

abstract class AbstractLogAdapter implements LogAdapterInterface
{
    const DEFAULT_LOG_NAME_REGEXES = [
        '/\\\\sensitive/' => 0,
        '/^sensitive/' => 0,
        '/\\\\verbose/' => 0,
        '/^verbose/' => 0,
    ];

    protected $debugLevel = LOG_INFO;
    protected $perLogName = [];
    protected $logNameRegexes = [];

    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = [],
        $logNameRegexes = AbstractLogAdapter::DEFAULT_LOG_NAME_REGEXES
    ) {
        $this->debugLevel = $debugLevel;
        $this->perLogName = $perLogName;
        $this->logNameRegexes = $logNameRegexes;
    }

    public function isLogEnabled($priority = LOG_INFO, $logName = null)
    {
        $levelToUse = $this->debugLevel;
        if (null != $logName) {
            if (array_key_exists($logName, $this->perLogName)) {
                $levelToUse = $this->perLogName[$logName];
            } else {
                $components = explode('\\', $logName);
                $str = '';
                foreach ($components as $c) {
                    if (strlen($str) > 0) {
                        $str .= '\\';
                    }
                    $str .= $c;
                    if (array_key_exists($str, $this->perLogName)) {
                        $levelToUse = $this->perLogName[$str];
                    }
                }

                // now check regexes and restrict logging
                foreach ($this->logNameRegexes as $regex => $restrictedLogLevel) {
                    if (preg_match($regex, $logName)) {
                        if ($levelToUse > $restrictedLogLevel) {
                            $levelToUse = $restrictedLogLevel;
                        }
                    }
                }
            }
        }

        return $priority <= $levelToUse;
    }

    public function info($message, $logName = null, $extras = [])
    {
        $this->log($message, LOG_INFO, $extras, $logName);
    }

    public function debug($message, $logName = null, $extras = [])
    {
        $this->log($message, LOG_DEBUG, $extras, $logName);
    }

    public function warn($message, $logName = null, $extras = [])
    {
        $this->log($message, LOG_WARNING, $extras, $logName);
    }

    public function error($message, $logName = null, $extras = [])
    {
        $this->log($message, LOG_ERR, $extras, $logName);
    }

    public function isErrorEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_ERR, $logName);
    }

    public function isWarnEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_WARNING, $logName);
    }

    public function isInfoEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_INFO, $logName);
    }

    public function isDebugEnabled($logName = null)
    {
        return $this->isLogEnabled(LOG_DEBUG, $logName);
    }
}

class EchoLogAdapter extends AbstractLogAdapter
{
    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = [],
        $logNameRegexes = AbstractLogAdapter::DEFAULT_LOG_NAME_REGEXES
    ) {
        parent::__construct($debugLevel, $perLogName, $logNameRegexes);
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        if (!$this->isLogEnabled($priority, $logName)) {
            return;
        }

        switch ($priority) {
            case LOG_ALERT:
                $priorityStr = '[ALERT]';

                break;

            case LOG_CRIT:
                $priorityStr = '[CRIT]';

                break;

            case LOG_ERR:
                $priorityStr = '[ERR]';

                break;

            case LOG_WARNING:
                $priorityStr = '[WARN]';

                break;

            case LOG_DEBUG:
                $priorityStr = '[DEBUG]';

                break;

            default:
                $priorityStr = '[INFO]';

                break;
        }
        echo "{$priorityStr} ({$logName}) {$message}".PHP_EOL;
    }
}

class StringBufferLogAdapter extends AbstractLogAdapter
{
    private $stringBuffer = '';

    public function __construct(
        $debugLevel = LOG_INFO,
        $perLogName = [],
        $logNameRegexes = AbstractLogAdapter::DEFAULT_LOG_NAME_REGEXES
    ) {
        parent::__construct($debugLevel, $perLogName, $logNameRegexes);
    }

    public function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        if (!$this->isLogEnabled($priority, $logName)) {
            return;
        }

        switch ($priority) {
            case LOG_ALERT:
                $priorityStr = '[ALERT]';

                break;

            case LOG_CRIT:
                $priorityStr = '[CRIT]';

                break;

            case LOG_ERR:
                $priorityStr = '[ERR]';

                break;

            case LOG_WARNING:
                $priorityStr = '[WARN]';

                break;

            case LOG_DEBUG:
                $priorityStr = '[DEBUG]';

                break;

            default:
                $priorityStr = '[INFO]';

                break;
        }
        $line = "{$priorityStr} ({$logName}) {$message}".PHP_EOL;
        $this->stringBuffer .= $line;
    }

    public function getString()
    {
        return $this->stringBuffer;
    }
}

class Logger
{
    /*LogAdapterInterface*/ private static $globalLogAdapter;

    public static function getGlobalLogAdapter() // : LogAdapterInterface
    {
        if (null == Logger::$globalLogAdapter) {
            Logger::setGlobalLogAdapter(new NoOpLogAdapter());
        }

        return Logger::$globalLogAdapter;
    }

    public static function setGlobalLogAdapter(LogAdapterInterface $logAdapter)
    {
        Logger::$globalLogAdapter = $logAdapter;
    }

    public static function logger($logName = null)
    {
        return new NamedLogAdapterDecorator($logName, Logger::getGlobalLogAdapter());
    }

    public static function log($message, $priority = LOG_INFO, $extras = [], $logName = null)
    {
        return Logger::logger()->log($message, $priority, $extras, $logName);
    }

    public static function error($message, $logName = null, $extras = [])
    {
        return Logger::logger()->error($message, $logName, $extras);
    }

    public static function warn($message, $logName = null, $extras = [])
    {
        return Logger::logger()->warn($message, $logName, $extras);
    }

    public static function info($message, $logName = null, $extras = [])
    {
        return Logger::logger()->info($message, $logName, $extras);
    }

    public static function debug($message, $logName = null, $extras = [])
    {
        return Logger::logger()->debug($message, $logName, $extras);
    }
}
