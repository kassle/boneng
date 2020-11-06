<?php declare(strict_types=1);

namespace Boneng\Processor;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class SystemLogger implements LoggerInterface {
    private const DEFAULT_TAG = 'Boneng';

    private const LEVEL = [
        LogLevel::EMERGENCY => LOG_EMERG,
        LogLevel::ALERT => LOG_ALERT,
        LogLevel::CRITICAL => LOG_CRIT,
        LogLevel::ERROR => LOG_ERR,
        LogLevel::WARNING => LOG_WARNING,
        LogLevel::NOTICE => LOG_NOTICE,
        LogLevel::INFO => LOG_INFO,
        LogLevel::DEBUG => LOG_DEBUG
    ];

    private $tag;
    private $console;

    public function __construct(string $tag = SystemLogger::DEFAULT_TAG, bool $console = false) {
        $this->tag = $tag;
        $this->console = $console;
    }

    public function emergency($message, array $context = array()) {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = array()) {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = array()) {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = array()) {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = array()) {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = array()) {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = array()) {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = array()) {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = array()) {
        if (\openlog($this->tag, LOG_PID | LOG_ODELAY, LOG_USER)) {
            \syslog(SystemLogger::LEVEL[$level], $message);
            \closelog();
        } else {
            \error_log($this->tag . ": fail to open syslog");
        }

        if ($this->console) {
            print($this->tag . ': ' . $message);
        }
    }
}