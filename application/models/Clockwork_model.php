<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ClockWork Model
 * Connect to TechnoPro's ClockWork MSSQL Database
 */

class Clockwork_model extends CI_Model {

    /**
     * Will store the connection string to connect to MSSQL
     *
     * @var string
     */
    private $conn;
    
    /**
     * Constructor for ClockWork Model
     * Build Connection String and store it in $this->conn
     *
     * @access public
     * @param string $config - Configuration filename minus the file extension
     * e.g: 'tsk' will load config/tsk.php
     * @return void
     */
    public function __construct($config = 'tsk')
    {
        parent::__construct();
        
        // Load the tsk.php configuration file
        $this->load->config($config);
        
        /***************************
         * Build Connection String *
         ***************************/
        
        /**
         * Contains Database Settings from Config file
         * @var array
         */
        $databaseSettings = $this->config->item('tsk_database');
        
        /**
         * Contains servername and port seperated by a comma
         * @var string
         */
        $serverName = $databaseSettings['ServerName'].', '.$databaseSettings['Port'];
        
        /**
         * Contains Connection Information
         * @var array
         */
        $connectionInfo = array(
            "Database" => $databaseSettings['Database'],
            "UID" => $databaseSettings['UID'],
            "PWD" => $databaseSettings['PWD']
        );
        
        /**
         * Store sql connect funtion to private $conn var
         * Other functions can pass $conn along with sql queries to get results
         */
        $this->conn = sqlsrv_connect($serverName, $connectionInfo);
    }
    
    /*
     * @access public
     * @return array - Two sets of clients; 'expired' and 'expiring' with their
     * personids and expiry date timestamps
     */
    public function getExpiryDates()
    {
        /***************************************************************************
         * Get list of clients' personids and expiry dates from ClockWork Database *
         ***************************************************************************/
        
        $sql = "select * from licensinginternal_productlicense where licensetypeid = 5 order by expirydate";
        $stmt = sqlsrv_query($this->conn, $sql);
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){$data[] = $row;}
        
        /*************************************************************************
         * Filter only schools who's support is already expired or expiring soon *
         *************************************************************************/
        
        // var $today timestamp. Any expiry date before this date is expired
        $today = strtotime(date('Y-m-d'));
        // var $future timestamp. Any expiry between $today and $future is expiring soon
        $expiringSoonMonths = $this->config->item('expiringSoonMonths');
        $future = strtotime('+'.$expiringSoonMonths.' weeks');
        // var $i & $ii, incrementals to set as array keys. For JSON.
        $i = 0;
        $ii = 0;
        
        // Prepare outgoing arrays. For each client, set personid and expiry date
        foreach($data as $key => $client)
        {
            if($client['expirydate']->getTimestamp() < $today)
            {
                $schools['expired'][$i]['personid'] = $client['personid'];
                $schools['expired'][$i]['date'] = $client['expirydate']->getTimestamp();
                $i++;
            } else if ($client['expirydate']->getTimestamp() > $today && $client['expirydate']->getTimestamp() < $future){
                $schools['expiring'][$ii]['personid'] = $client['personid'];
                $schools['expiring'][$ii]['date'] = $client['expirydate']->getTimestamp();
                $ii++;
            }
        }
        return $schools;
    }
    
}