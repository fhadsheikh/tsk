<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| ClockWork MSSQL Database
|--------------------------------------------------------------------------
|
| Connection settings
|
*/

$config['tsk_database'] = array(
    'ServerName' => 'stableclockwork',
    'Port' => 1434,
    'Database' => 'ClockWorkTechno',
    'UID' => 'tp',
    'PWD' => 'techno03'
);

$config['expiringSoonMonths'] = 2;