<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Database_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getClient($companyId)
    {
        $query = $this->db->get_where('clients', array('helpdesk_id'=>$companyId));
        
        return $query->row();
    }
    
    public function findTicket($ticket)
    {
        $query = $this->db->get_where('tickets', array('ticket_id'=>$ticket));
        
        if($query->row())
        {
            return $query->row();
        } else {
            return false;
        }
    }
    
    public function addTicket($ticketID,$expired)
    {
        $this->db->insert('tickets', array('ticket_id'=>$ticketID,'expired'=>$expired));
        
    }
    
    public function lookupClient($pid)
    {
        
        
        $query = $this->db->get_where('clients', array('clockwork_id'=>$pid));
        
        $result = $query->result();
        
        $template = new stdClass();
        
        $template->name = $pid;
        
        if($result == null)
        {
            $data = array($template);
        } else {
            $data = $query->result();
        }
        
        return $data;

    }
    
    public function isHidden($pid)
    {
        $query = $this->db->get_where('clients', array('clockwork_id'=>$pid));
        
        $result = $query->row();
        
        if($result->hide)
        {
            return true;
        } else {
            return false;
        }
        
    }
    
}