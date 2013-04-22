<?php
/*
* overlay   plugin 3.1
* Joomla plugin
* by Purple Cow Websites
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/

class LivefyreLogger {
    
    private static $instance;
    private static $log_flag;

    private function __construct() {

        if (version_compare( JVERSION, '1.7', '>=' ) == 1) {
            jimport('joomla.log.log');
            // Initialise a basic logger with no options (once only).
            JLog::addLogger(array());
            JLog::add("Creating a logger");
            self::$log_flag = true;
        }
    }

    public static function getInstance() {

        if (!self::$instance) {
            self::$instance = new LivefyreLogger();
        }

        return self::$instance;
    }

    function add($message) {

        if (JDEBUG && self::$log_flag) {
            JLog::add($message, JLog::DEBUG, 'Livefyre');
        }
    }
}

?>