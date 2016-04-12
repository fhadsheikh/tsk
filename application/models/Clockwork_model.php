<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Clockwork_model extends CI_Model {

    private $conn;
    
    public function __construct(){
        
        $serverName = "stableclockwork, 1434";
        
        $connectionInfo = array(
            "Database" => "ClockWorkTechno",
            "UID" => "tp",
            "PWD" => "techno03"
        );
        
        $this->conn = sqlsrv_connect($serverName, $connectionInfo);
        
        
    }
    
    /*
     * getExpiryDates()
     * ----------------
     * returns Array
     * Array of two sets of clients. Expired Support and Expiring Soon. 
     * Array provides the personid and formatted date of support expiry
     *
     */
    public function getExpiryDates()
    {
        /***************************************************************************
         * Get list of clients' personids and expiry dates from ClockWork Database *
         ***************************************************************************/
        
        $sql = "select * from licensinginternal_productlicense where licensetypeid = 5";
        $stmt = sqlsrv_query($this->conn, $sql);
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){$data[] = $row;}
        
        /*************************************************************************
         * Filter only schools who's support is already expired or expiring soon *
         *************************************************************************/
        
        // var $today timestamp. Any expiry date before this date is expired
        $today = strtotime(date('Y-m-d'));
        // var $future timestamp. Any expiry between $today and $future is expiring soon
        $future = strtotime('+1 month');
        // var $i & $ii, incrementals to set as array keys. For JSON.
        $i = 0;
        $ii = 0;
        
        // Prepare outgoing arrays. For each client, set personid and expiry date
        foreach($data as $key => $client)
        {
            if($client['expirydate']->getTimestamp() < $today)
            {
                $schools['expired'][$i]['personid'] = $client['personid'];
                $schools['expired'][$i]['date'] = date('F j, Y', $client['expirydate']->getTimestamp());
                $i++;
            } else if ($client['expirydate']->getTimestamp() > $today && $client['expirydate']->getTimestamp() < $future){
                $schools['expiring'][$ii]['personid'] = $client['personid'];
                $schools['expiring'][$ii]['date'] = date('F j, Y', $client['expirydate']->getTimestamp());
                $ii++;
            }
        }
        return $schools;
    }
    
    
}