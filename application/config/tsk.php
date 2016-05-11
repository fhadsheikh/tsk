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

$config['cw_database'] = array(
    'ServerName' => 'stableclockwork',
    'Port' => 1434,
    'Database' => 'ClockWork5-15-1-21',
    'UID' => 'tsk',
    'PWD' => 'techno@07'
);

$config['expiringSoonMonths'] = 2;