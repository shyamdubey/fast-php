<?php


namespace Core\Log;

use App\Settings;
use Core\Enums\HttpStatus;
use Core\Http\Response;
use Exception;

class LoggerFactory implements LoggerInterface
{

    private static $className;
    private static LoggerFactory $instance;

    public static function get_logger($className)
    {
        self::$className = $className;
        return new LoggerFactory($className);
       
    }
    public function log($message)
    {
        $final_message = date("Y-m-d h:i:s", time()) . " | INFO | ".self::$className.".php | " . $message;
        $this->logMessage($final_message);
    }
    public function info($message)
    {
        $final_message = date("Y-m-d h:i:s", time()) . " | INFO | ".self::$className.".php | " . $message;
        $this->logMessage($final_message);
    }
    public function warn($message)
    {
        $final_message = date("Y-m-d h:i:s", time()) . " | WARN | ".self::$className.".php | " . $message;
        $this->logMessage($final_message);
    }
    public function error($message)
    {
        $final_message = date("Y-m-d h:i:s", time()) . " | ERROR | ".self::$className.".php | " . $message;
        $this->logMessage($final_message);
    }

    private function logMessage($fullMessage)
    {
        //check whether user want to log the message in custom directory
        if (Settings::LOG_IN_CUSTOM_DIR) {
            $dir = Settings::LOG_DIR;
            try {
                if (is_dir($dir)) {
                    $logPattern = Settings::LOG_FILE_PATTERN;
                    $logPatternCases = LogPattern::cases();
                    if (!in_array($logPattern, $logPatternCases)) {
                        throw new Exception("Log pattern could not be found. It should be one of the following " . implode(", ", $logPatternCases));
                    }
                    if ($logPattern == LogPattern::DDMMYYYY_LOGS) {
                        $today = date("Y-m-d", time());
                        $file_name = $dir . "/" . $today . "_logs.log";
                        if (!file_exists($file_name)) {
                            $f = fopen($file_name, "w");
                        } else {
                            $f = fopen($file_name, "a");
                        }
                        fwrite($f, $fullMessage."\n");
                    }
                } else {
                    throw new Exception(__DIR__ . "/" . $dir . " directory doesn't exists. Please create the directory to log the message");
                }
            } catch (Exception $ex) {
                $message = $ex->getMessage();
                $stacktrace = $ex->getTraceAsString();
                $response["message"] = $message;
                $response["stacktrace"] = $stacktrace;
                $response["statusCode"] = HttpStatus::INTERNAL_SERVER_ERROR->value;
                $response["time"] = time();
                Response::json(HttpStatus::INTERNAL_SERVER_ERROR, $response);
            }
        } else {
            error_log($fullMessage);
        }
    }
}
