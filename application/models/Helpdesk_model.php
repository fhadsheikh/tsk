<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Helpdesk_model extends CI_Model {
    
    public $tickets;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('MY_date_helper');
        $this->getTickets();
    }
    
    /* GOLDEN */
    public function getTickets()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/tickets?&count=150",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "postman-token: 68490363-3ea7-6df8-db6f-70bea21cc040"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        $this->tickets = json_decode($response);
    }
    
    public function getTickets2($ticketCount = 10)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/tickets?&count=$ticketCount",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "postman-token: 68490363-3ea7-6df8-db6f-70bea21cc040"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        return json_decode($response);
    }
    
    public function getTicketsByCategory($categories, $count = 100)
    {
        
        $openTickets = [];
        
        foreach($categories as $category)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/tickets?mode=unclosed&categoryid=$category&count=$count",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
                "cache-control: no-cache",
                "postman-token: 3201605d-a223-2a8a-0dae-e84143fa828e"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            
            $openTickets = array_merge($openTickets, json_decode($response));
        }
        
        return $openTickets;

    }
    
    public function getTicketsBySchool($id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/tickets?fromCompanyId=10&count=300",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "postman-token: d4b6d141-df71-5cbd-b91e-76f4c3f1a78f"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    
        return json_decode($response);
    }
    
    public function getTicket($issueID)
    {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/ticket?id=".$issueID,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"categoryId\"\r\n\r\n3\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"body\"\r\n\r\nTest body\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"subject\"\r\n\r\ntest subject\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"priorityId\"\r\n\r\n1\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"\"\r\n\r\n\r\n-----011000010111000001101001--",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=---011000010111000001101001",
            "id: 8780",
            "postman-token: 8751c119-fbf1-7b28-deb6-77a8b1fd7b02"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        return json_decode($response);
    }
    
    public function getTicketsDateRange($days = 1)
    {
        
        $daysAgo = mktime(0, 0, 0, date("m"), date("d")-$days,   date("Y"));
                
        foreach($this->tickets as $ticket)
        {
            $lastUpdated = unixToPhp($ticket->LastUpdated);
            
            if($lastUpdated >= $daysAgo)
            {
                $response[] = $ticket;
            }
        }
        
        return $response;
    }
    
    public function getComments($issueID)
    {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/comments",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"id\"\r\n\r\n$issueID\r\n-----011000010111000001101001--",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=---011000010111000001101001",
            "postman-token: 7a9fd13c-d9b4-0414-cef5-f40c5e7c9a01"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response);
    }
    
    public function getOpenTickets()
    {   
        $open = 0;
        foreach($this->tickets as $ticket)
        {
            if($ticket->Status != 'Resolved' && ($ticket->CategoryID == 3 || $ticket->CategoryID == 14))
            {
                $open++;
            }
        }
        return $open;
    }
    
    public function getUnassignedTickets()
    {
        $unassigned = 0;
        foreach($this->tickets as $key => $ticket)
        {
            if($ticket->AssignedToUserID == NULL && $ticket->Status != 'Resolved' && ($ticket->CategoryID == 3 || $ticket->CategoryID == 14))
            {
                $unassigned++;
            }
        }
        return $unassigned;
    }
    
    public function getCriticalTickets()
    {
        $critical = 0;
        foreach($this->tickets as $key => $ticket)
        {
            if($ticket->Priority == 2 && $ticket->Status != 'Resolved' && ($ticket->CategoryID == 3 || $ticket->CategoryID == 14))
            {
                $critical++;
            }
        }
        
        return $critical;  
    }
    
    public function getStaleTickets()
    {
        //Set Timezone
        date_default_timezone_set('America/New_York');

        //Set var for Two Days Ago
        $today = date("l");

        if($today == "Monday"){
            $d = 4;
        } else if ($today == "Tuesday"){
            $d = 3;
        } else {
            $d = 2;
        }

        $twoDaysAgo = mktime(0, 0, 0, date("m"), date("d")-$d,   date("Y"));
        
        $stale = 0;
        foreach($this->tickets as $key => $ticket)
        {
            $str = $ticket->LastUpdated;
            preg_match( "#/Date\((\d{10})\d{3}(.*?)\)/#", $str, $match );
            $date = strtotime(date( "Y-m-d", $match[1] ));

            if($date <= $twoDaysAgo && $ticket->Status != 'Resolved' && ($ticket->CategoryID == 3 || $ticket->CategoryID == 14)){
                $stale++;
            }
        }
        return $stale;
    }
    
    public function getOpenedTickets()
    {
        $opened = 0;
        $today = strtotime(date("Y-m-d"));
        foreach($this->tickets as $ticket)
        {
            $str = $ticket->IssueDate;
            preg_match( "#/Date\((\d{10})\d{3}(.*?)\)/#", $str, $match );
            $openDate = strtotime(date("Y-m-d", $match[1]));
            
            if($openDate == $today){
            $opened++;
            }
        }
        return $opened;
    }
    
    public function getClosedTickets()
    {
        $closed = 0;
        $today = strtotime(date("Y-m-d"));
        foreach($this->tickets as $ticket)
        {
            $str = $ticket->LastUpdated;
            preg_match( "#/Date\((\d{10})\d{3}(.*?)\)/#", $str, $match );
            $closedDate = strtotime(date("Y-m-d", $match[1]));
            
            if($ticket->Status == 'Resolved' && $closedDate == $today){
            $closed++;
            }
        }
        return $closed;
        
    }
    
    public function getTechStats()
    {
        $today = strtotime(date('Y-m-d') . "\n");
        $lastWeek = strtotime(date('Y-m-d', strtotime('-7 days')) . "\n");
        
        foreach($this->tickets as $ticket)
        {
            if($ticket->Status != 'Resolved' && ($ticket->CategoryID == 3 || $ticket->CategoryID == 14))
            {
                $openTickets[] = $ticket;
            }
        }
        
        $totalOpen = count($openTickets)/2;
        
        $techs = array(
            array(
                "name"=>"Azim Ahmed",
                "id"=>414
            ),
            array(
                "name"=>"Lester Siew",
                "id"=>468
            ),
            array(
                "name"=>"Jenny Wang",
                "id"=>1054
            ),
            array(
                "name"=>"Elisa Lo Monaco",
                "id"=>1141
            ),
            array(
                "name"=>"David Chun",
                "id"=>1154
            ));
        
        $techCount = array();

        foreach($techs as $key => $tech){

            $i = 0; // var for total open
            $c = 0; // var for total critical
            $t = 0; // Tech
            $u = 0; // user
            $s = 0; // Staff
            $n = 0; //New
            $closed = 0;
            
            foreach($this->tickets as $ticket)
            {
                $str = $ticket->LastUpdated;
                preg_match( "#/Date\((\d{10})\d{3}(.*?)\)/#", $str, $match );
                $date = strtotime(date( "Y-m-d", $match[1] ));
                
                if($ticket->AssignedToUserID == $tech['id'] && $date <= $today && $date >= $lastWeek && $ticket->Status == 'Resolved' && ($ticket->CategoryID == 14 || $ticket->CategoryID == 3))
                {
                    $techCount[$key]['closed'][] = $ticket->IssueID;
                    $closed++;
                }
            }

            foreach($openTickets as $openTicket) {

                if($openTicket->AssignedToUserID == $tech['id']) {

                    $i++;

                    if($openTicket->Priority == 2) {
                        $c++;
                    }

                    if($openTicket->UpdatedByUser == 1) {
                        $u++;
                    }

                    if($openTicket->UpdatedByPerformer == 1) {
                        $s++;
                    }

                    if($openTicket->UpdatedByUser == '' && $openTicket->UpdatedByPerformer == '') {

                        if($openTicket->Status == 'New') {
                            $n++;
                        } else {
                            $t++;
                        }
                    }

                }


                if($s > 0) {$ps = ($s * 100) / $totalOpen;} else {$ps = 0;};

                if($u > 0) {$pu = ($u * 100) / $totalOpen;} else {$pu = 0;};

                if($n > 0) {$pn = ($n * 100) / $totalOpen;} else {$pn = 0;};

                if($t > 0) {$pt = ($t * 100) / $totalOpen;} else {$pt = 0;};


            }
            
            $techCount[$key]['name'] = $tech['name'];
            $techCount[$key]['Total'] = $i;
            $techCount[$key]['Critical'] = $c;
            $techCount[$key]['Staff'] = $s;
            $techCount[$key]['Client'] = $u;
            $techCount[$key]['Tech'] = $t;
            $techCount[$key]['New'] = $n;
            $techCount[$key]['PercentStaff'] = $ps;
            $techCount[$key]['PercentClient'] = $pu;
            $techCount[$key]['PercentNew'] = $pn;
            $techCount[$key]['PercentTech'] = $pt;
            $techCount[$key]['Closed'] = $closed;


        }

        return $techCount;
    }
    
    public function getWorkOrders()
    {
        $approved = 0;
        $pending = 0;
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/tickets?&categoryid=25&mode=unclosed&count=25",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "postman-token: 68490363-3ea7-6df8-db6f-70bea21cc040"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        $approved = count(json_decode($response));
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/tickets?&categoryid=26&mode=unclosed&count=25",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "postman-token: 68490363-3ea7-6df8-db6f-70bea21cc040"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        $pending = count(json_decode($response));
        
        return array("approved"=>$approved, "pending"=>$pending);
    }
    
    public function getUserByEmail($email)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://clockworks.ca/support/helpdesk/api/userbyemail?email=$email",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic ZmhhZDpIb3RtYWlsMTIzNA==",
            "cache-control: no-cache",
            "postman-token: 570e22bc-3715-e8a2-b940-0d3f0523c134"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        return json_decode($response);

    }
    
}