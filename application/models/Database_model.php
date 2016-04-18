<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Database_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Returns Tech Data (array) when id is passed
     * @access public
     * @param $pid int
     * @return array
     */
    public function lookupTech($id)
    {
        $query = $this->db->get_where('techs', array('helpdesk_id'=>$id));
        
        return $query->row();
    }    
    
    /**
     * Returns School Data (array) when personid is passed
     * @access public
     * @param $pid int
     * @return array
     */
    public function lookupClient($pid)
    {
        $query = $this->db->get_where('clients', array('clockwork_id'=>$pid));
        
        return $query->row();
    }
    
    /**
     * Returns Comment Data (array) when commentid is passed
     * @access public
     * @param $commentid int
     * @return array
     */
    public function lookupComment($commentid)
    {
        $query = $this->db->get_where('comments', array('commentid'=>$commentid));
        return $query->row();
    }
    
    /**
     * Adds comment to database
     * @access public
     * @param array $comment
     * @return void
     */
    public function insertComment($comment)
    {
        $data = array(
            "CommentID" => $comment->CommentID,
            "IssueID" => $comment->IssueID
        );
        
        $this->db->insert('comments', $data);
    }
    
}