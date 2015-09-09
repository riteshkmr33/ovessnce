<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
// Conditional Base url
function returnBaseurl() {
    
    if($_SERVER['SERVER_NAME']=="ovessence.in") {
        //lnserver
        $base_url = '//ovessence.in/'; 

    } else if($_SERVER['SERVER_NAME']=="dev.clavax.us") {
        //dev.clavax.us development server
        $base_url = '//dev.clavax.us/ovessence/public/';

    } else {
        // local setting
        $base_url = '//ovessence.loc/';
    }
    
    return $base_url;
}


// Conditional DataBase detail
function returnDataBaseDetail() {
    
    if($_SERVER['SERVER_NAME']=="ovessence.in") {
        //lnserver
        $db = 'mysql:dbname=OvEssenCe;host=192.168.2.129';        

    } else if($_SERVER['SERVER_NAME']=="dev.clavax.us") {
        //dev.clavax.us development server
        $db = 'mysql:dbname=ovessence_dev;host=localhost';

    } else {
        // local setting
        $db = 'mysql:dbname=OvEssenCe;host=localhost';
    }
    
    return $db;
}

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => returnDataBaseDetail(),
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    
    'view_manager' => array(
        'base_path' => returnBaseurl(),
    ),
    
    'payment_methods' => array(
		'1' => 'Visa',
		'2' => 'Mastercard',
		'3' => 'Amex',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    
    'module_layouts' => array(
       'Admin' => 'layout/layout.phtml',
   ),
   
);
