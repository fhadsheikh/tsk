<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Syncs extends REST_Controller {
    
    /**
     * Constructor for Syncs Controller
     * Used by ticketSync
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('Helpdesk_model');
        $this->load->model('Database_model');
        $this->load->model('ClockWork_model');
        $this->load->helper('MY_date_helper');
        $this->load->helper('html2text_helper');
        $this->load->library('Prepare');
    }
    
    /**
     * Live Sync for ticketSync app
     * @access ticketSync app
     * @return void 
     */
    public function master_get()
    {
        // Get latest tickets
//        $tickets = $this->Helpdesk_model->getTicketsDateRange(0);
        $tickets = $this->Helpdesk_model->getTicketsBySchool(10);
        
        foreach($tickets as $key => $ticket)
        {
            // Get comments for latest tickets
            $comments = $this->Helpdesk_model->getComments($ticket->IssueID);
            
            $ticketDetails = $this->Helpdesk_model->getTicket($ticket->IssueID);
            
            foreach($comments as $key2 => $comment)
            {
                    
                $author = $this->Database_model->lookupUser($comment->UserID);

                // Check if author doesn't exists in tsk database
                if(!$author)
                {
                    // Check if author is not already in the pending list
                    if(!$this->Database_model->lookupPendingUser($comment->Email))
                    {
                        if($comment->Email != null)
                        {
                            $this->Database_model->insertPendingUser($comment->UserID,$comment->Email);
                        }
                    }
                }
                else 
                {
                    // Person Id
//                    $response['personid'] = $this->Database_model->lookupCompany($ticket->CompanyID)->clockwork_id;
                    $response['personid'] = 5244;
                    $response['appointmenttype'] = '115';
//                    $response['metwithpersonid'] = $this->Database_model->lookupCompany($ticket->CompanyID)->clockwork_id;
                    $response['metwithpersonid'] = 2765;
                    $response['groupcode'] = '102'.$ticket->IssueID;
                    $response['screennum'] = '105';
                    $response['author'] = $this->prepare->notificationName($comment);
                    $response['responseBody'] = str_replace("'", "", strip_tags(convert_html_to_text($comment->Body)));
                    $response['ticketID'] = $ticket->IssueID;
                    $response['subject'] = $ticket->Subject;
                    $response['priority'] = $ticket->Priority;
                    $response['category'] = $ticket->CategoryID;
                    $response['submissionDate'] = date('Y-m-d', unixToPhp($ticket->IssueDate));
                    $response['submittedBy'] = $this->prepare->notificationName($comment);
                    $response['ticketBody'] = str_replace("'", "", strip_tags(convert_html_to_text($ticketDetails->Body)));
                    $response['techsOnly'] = $comment->ForTechsOnly;
                    if($comment->Recipients == null)
                    {
                        $response['recipients'] = 'No Recipients';
                    } else 
                    {
                        $response['recipients'] = $comment->Recipients;                        
                    }
                    $response['responseDate'] = date('m/d/Y h:i a', unixToPhp($comment->CommentDate));
                    $response['commentID'] = '3'.$comment->CommentID;
//                    $response['commentID'] = rand();
//                    print_r($response);
                    $this->ClockWork_model->insertComment($response);
                }
            }
            
        }
        // Send to clockwork
        // Send to internal db
        // Send to pusher
        
        if(!isset($response))
        {
            $response = array('sounds good?'=>true);
        }
        $this->response($response,200);
    }
    
    public function pendingusers_get()
    {
        $pendingUsers = $this->Database_model->getPendingUsers();
        
        foreach($pendingUsers as $pendingUser)
        {
            $user = $this->Helpdesk_model->getUserByEmail($pendingUser->email);
            $this->Database_model->insertUser($user);
            $this->Database_model->deletePendingUser($pendingUser->helpdesk_id);
        }
        
        $this->response($pendingUsers, 200);
    }
    
}