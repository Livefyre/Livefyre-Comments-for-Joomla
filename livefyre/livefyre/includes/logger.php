<?php
/*
* Joomla Plugin
* Plugin Version: 1.0
* by Livefyre Inc
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/

class LivefyreLogger {
    
    private static $instance;

    private function __construct() {

        if (version_compare( JVERSION, '1.7', '>=' ) == 1) {
            jimport('joomla.log.log');
            // Initialise a basic logger with no options (once only).
            JLog::addLogger(array('text_file' => 'livefyre_log.php'));
            JLog::add("Creating a logger", JLog::DEBUG, 'Livefyre');
        }
    }

    public static function getInstance() {

        if (!self::$instance) {
            self::$instance = new LivefyreLogger();
        }

        return self::$instance;
    }

    function add($message) {

        if (JDEBUG) {
            JLog::add($message, JLog::DEBUG, 'Livefyre');
        }
    }
}

?>