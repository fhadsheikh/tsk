<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Feed extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Helpdesk_model');
        $this->load->helper('My_date_helper');
    }
    
    public function sync_get()
    {
        if($this->get('tickets'))
        {
            $ticketCount = $this->get('tickets');
        } else {
            $ticketCount = 10;
        }
        
        if($this->get('comments'))
        {
            $commentCount = $this->get('comments')-1;
        } else {
            $commentCount = 0;
        }
        
        // Get latest 150 tickets
        $tickets = $this->Helpdesk_model->getTickets2($ticketCount);
        $i = 0;
        foreach($tickets as $key => $ticket)
        {
            
            $comments = $this->Helpdesk_model->getComments($ticket->IssueID);
            $ii=0;
            foreach($comments as $key2 => $comment)
            {
                if($ii <= $commentCount)
                {
                    $feed[$i]['CommentID'] = $comment->CommentID;
                    $feed[$i]['TicketID'] = $comment->IssueID;
                    $feed[$i]['Date'] = mysqlDate($comment->CommentDate);
                    $feed[$i]['Username'] = $comment->UserName;
                    $feed[$i]['Email'] = $comment->Email;
                    $feed[$i]['IsSystem'] = $comment->IsSystem;
                    $feed[$i]['ForTechsOnly'] = $comment->ForTechsOnly;
                    $feed[$i]['TicketSubject'] = $comment->TicketSubject;
                    $feed[$i]['Body'] = $comment->Body;
                    $feed[$i]['Recipients'] = $comment->Recipients;
                    $i++;
                    $ii++;
                } else {
                    break;
                }
            }
            
//            $ticket->comments = $this->Helpdesk_model->getComments($ticket->IssueID);
        }
        
        $this->response($feed,200);
        
    }
    
    // Get latest updated tickets since last sync time
    
    // Get comments for all tickets from above
    
    // Determine nature of comment
    
    // Build array summarizing latest comments
    
    // Save into Database
    
    // Update Pusher
    
}