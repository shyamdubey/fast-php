<?php
namespace Core\Log;

interface LoggerInterface{


    static function get_logger($className);
    function log($message);
    function info($message);
    function warn($message);
    function error($message);
    

}