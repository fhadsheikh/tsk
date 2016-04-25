<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Tickets extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Helpdesk_model');
        $this->load->library('Prepare');
        
        
       header("Access-Control-Allow-Origin: *");
    }
    
    public function tickets_get()
    {
        $tickets = $this->Helpdesk_model->getTicketsByCategory(array(3,14));
        
        $response = $this->prepare->forFats($tickets);
        
        $this->response($response,200);
    }
    
    public function ticket_get()
    {
        $issueID = $this->get('issueID');
        $ticket = $this->Helpdesk_model->getTicket($issueID);
        $this->response($ticket, 200);
    }
    
    public function comments_get()
    {
        $issueID = $this->get('issueID');
        $comments = $this->Helpdesk_model->getComments($issueID);
        $this->response($comments,200);
    }
    
}