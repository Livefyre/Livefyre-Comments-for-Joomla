<?php
/*
* overlay   plugin 3.1
* Joomla plugin
* by Purple Cow Websites
* @copyright Copyright (C) 2010 * Livefyre All rights reserved.
*/

class Logger {
    
    function __construct() {
        if (version_compare( JVERSION, '1.7', '>=' ) == 1) {
            jimport('joomla.log.log');
            // Initialise a basic logger with no options (once only).
            JLog::addLogger(array());
            JLog::add("Creating a logger", JLog::DEBUG, 'Livefyre');
            $use_log = true;
        }
    }

    function add($message) {
        if (JDEBUG !== true || !$this->$use_log) {
            return;
        }
        JLog::add($message, JLog::DEBUG, 'Livefyre');
    }
}

?>