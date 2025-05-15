<?php
// Load Config
require_once 'config/config.php';

// Load Helpers
require_once 'helpers/url_helper.php'; // Nếu có
require_once 'helpers/session_helper.php';
require_once 'helpers/helpers.php';

// Autoload Core Libraries
spl_autoload_register(function($className) {
    if(file_exists('libraries/' . $className . '.php')) {
        require_once 'libraries/' . $className . '.php';
    } elseif(file_exists('controllers/' . $className . '.php')) {
        require_once 'controllers/' . $className . '.php';
    } elseif(file_exists('models/' . $className . '.php')) {
        require_once 'models/' . $className . '.php';
    }
});