<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Tv extends REST_Controller {
    
    /**
     * Constructor for TV Controller
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pusher');
        $this->load->model('Clockwork_model');
        $this->load->model('Database_model');
        $this->load->model('Helpdesk_model');
        
        
       header("Access-Control-Allow-Origin: *");
        
    }
    
    /**
     * API ENDPOINT - GET /tv/techs
     * @access public
     * @return push array containing tech details
     * @return 200
     */
    public function techs_get()
    {
        $data = $this->Helpdesk_model->getTechStats();
        
        $this->pusher->trigger(
            'tix',
            'techs',
            $data
        );
        
        $this->response($data,200);
    }
    
    /**
     * API ENDPOINT - GET /tv/tickets
     * @access public
     * @return push array containing ticket details
     * @return 200
     */
    public function tickets_get()
    {
        
        // OPEN
        $data['open'] = $this->Helpdesk_model->getOpenTickets();
        
        //UNASSIGED
        $data['unassigned'] = $this->Helpdesk_model->getUnassignedTickets();
        
        // CRITICAL        
        $data['critical'] = $this->Helpdesk_model->getCriticalTickets();
        
        // STALE
        $data['stale'] = $this->Helpdesk_model->getStaleTickets();
        
        // OPENED
        $data['opened'] = $this->Helpdesk_model->getOpenedTickets();
        
        // CLOSED
        $data['closed'] = $this->Helpdesk_model->getClosedTickets();
        
        // OPEN WORK ORDERS
        $data['workorders'] = $this->Helpdesk_model->getWorkOrders();
        
        $this->pusher->trigger(
            'tix',
            'tickets',
            $data
        );
        
        $this->response($data,200);
    }

    /**
     * API ENDPOINT - GET /tv/support
     * @access public
     * @return push array containing list of Expired and Expiring schools
     * @return 200
     */
    public function support_get()
    {
        // Get array of expired and expiring school personids
        $data = $this->Clockwork_model->getExpiryDates();
        
        // For each Expired school, get school name and format expiry date
        foreach($data['expired'] as $key => $expired)
        {
            // Get array of school data from TSK database
            $school = $this->Database_model->lookupClient($expired['personid']);
            
            // Only build array for schools that are not marked as hidden
            if(!$school->hide){
                
                $expiredClients[$key]['name'] = $school->name;
                $expiredClients[$key]['date'] = date('F j, Y', $expired['date']);
            }
        }
        
        // For each Expiring school, get school name and format expiry date
        foreach($data['expiring'] as $key => $expiring)
        {
            // Get array of school data from TSK database
            $school = $this->Database_model->lookupClient($expiring['personid']);
            
            // Only build array for schools that are not marked as hidden
            if(!$school->hide){
                
                $expiringClients[$key]['name'] = $school->name;
                $expiringClients[$key]['date'] = date('F j, Y', $expiring['date']);
            }
        }
        
        // Push to tix app
        $this->pusher->trigger(
            'tix',
            'support',
            array(
            'expiredClients' => $expiredClients,
            'expiringClients' => $expiringClients
            )
        );
        
        $this->response($data, 200);
        
    }
    
    /**
     * API ENDPOINT - GET /tv/comments
     * @access public
     * @return push array containing latest comment details
     * @return 200
     */
    public function comments_get()
    {
        $latestTicket = $this->Helpdesk_model->getTickets2(1);
        
        $latestComment = $this->Helpdesk_model->getComments($latestTicket[0]->IssueID)[0];
        
        $latestComment->Subject = $latestTicket[0]->Subject;
        
        if(!$this->Database_model->lookupComment($latestComment->CommentID))
        {
            $this->Database_model->insertComment($latestComment);
                    
            if($latestComment->FirstName != NULL && $latestComment->LastName != NULL )
            {
                $latestComment->Name = $latestComment->FirstName." ".$latestComment->LastName;
            }
            else
            {
                $latestComment->Name = $latestComment->Email;
            }
        
            if($latestComment->Body === 'New ticket submitted (email)')
            {
                
                $latestComment->Preview = 'submitted a new ticket #'.$latestComment->IssueID;
                
            } 
            elseif($latestComment->Body == 'The ticket has been taken')
            {
                
                $latestComment->Preview = 'took over ticket #'.$latestComment->IssueID;
                
            } 
            elseif(strpos($latestComment->Body, 'The ticket has been re-opened'))
            {
                
                $latestComment->Preview = 're-opened ticket #'.$latestComment->IssueID;
                
            } 
            elseif($latestComment->Body == 'The ticket has been closed')
            {
                
                $latestComment->Preview = 'closed ticket #'.$latestComment->IssueID;
                
            } 
            elseif(strpos($latestComment->Body, 'ticket has been assigned to technician:'))
            {
                
                $subjectSplit = explode(":", $latestComment->Body);
                $latestComment->Preview = 'assigned ticket '.$latestComment->IssueID.' to '.$subjectSplit[1];
                
            }
            else
            {
                $latestComment->Preview = 'replied to ticket #'.$latestComment->IssueID;
            }
            
            
            $this->pusher->trigger(
                'tix',
                'comments',
                $latestComment
            );
            
        }
        
        $this->response($latestComment,200);
        
    }
    
    
}
    