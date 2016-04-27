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
        $tickets = $this->Helpdesk_model->getTicketsDateRange(0);
        
        foreach($tickets as $key => $ticket)
        {
            // Get comments for latest tickets
            $comments = $this->Helpdesk_model->getComments($ticket->IssueID);
            
            foreach($comments as $key2 => $comment)
            {
                // check if comment doesn't already exist in database
                if(!$this->Database_model->lookupComment($comment->CommentID))
                {
                    
                    // Check if author doesn't exists in tsk database
                    if(!$this->Database_model->lookupUser($comment->UserID))
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
                        
                        // Insert into clockwork
                        $response[$key]['Author'] = $this->prepare->notificationName($comment);
                        $response[$key]['ResponseBody'] = $comment->Body;

                        $response[$key]['Subject'] = $ticket->Subject;
                        $response[$key]['Priority'] = $ticket->Priority;
                        $response[$key]['Category'] = $ticket->CategoryID;
                        $response[$key]['SubmissionDate'] = date('Y-m-d', unixToPhp($ticket->IssueDate));
                        $response[$key]['SubmittedBy'] = $this->prepare->notificationName($comment);

                        //$this->Database_model->insertComment($comment);
                    }
                    
                    
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
    
    public function test_get()
    {
        $personid = '5244';
		$appointmenttype = '115';
		$metwithpersonid = '680';
		$groupcode = '9995';
		$screennum = '105';
		$author = 'Ticket Sync';
		$responseBody = 'This ticket was assigned to Shia Labeouf';
		$ticketID = '9048';
		$subject = 'Who let the dogs out AGAIN?';
		$priority = 'Critical';
		$category = 'Inquiry';
		$submissionDate = '4/26/2016 2:00 pm';
		$submittedBy = 'Shia Labeouf';
		$ticketBody = 'Seriously, who did it? Who let the dogs out?';
		$techsOnly = '1';
		$recepients = 'shia@clockworks.ca,azim@clockworks.ca';
		$responseDate = '4/26/2016 9:40 am';
		$commentID = 'sad face10';
        
        
        $this->ClockWork_model->insertComment($personid, $appointmenttype, $metwithpersonid, $groupcode, $screennum, $author, $responseBody, $ticketID,$subject,$priority,$category,$submissionDate,$submittedBy,$ticketBody,$techsOnly,$recepients,$responseDate,$commentID);
            
        
    }
    
}