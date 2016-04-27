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
     * Returns User Data (array) when helpdesk userid is passed
     * @access public
     * @param $uid int
     * @return array
     */
    public function lookupUser($uid)
    {
        $query = $this->db->get_where('users',
        array('helpdesk_id'=>$uid));
        
        return $query->row();
    }
    
    /**
     * Adds user to users table
     * @access public
     * @param array $user
     * @return void
     */
    public function insertUser($user)
    {
        
        
        $data = array(
            "helpdesk_id"=> $user->UserID,
            "company_id"=> $user->CompanyId,
            "username"=>$user->Username,
            "email"=>$user->Email);
        
        $this->db->insert('users', $data);
        
    }    
    
    /**
     * Returns all users (array) in pending table
     * @access public
     * @return array
     */
    public function getPendingUsers()
    {
        $this->db->select('*');
        $this->db->from('users_pending');
        $query = $this->db->get();
        
        return $query->result();
    }    
        
    /**
     * Returns Pending User Data (array) when helpdesk userid is passed
     * @access public
     * @param $uid int
     * @return array
     */
    public function lookupPendingUser($email)
    {
        $query = $this->db->get_where('users_pending',
        array('email'=>$email));
        
        return $query->row();
    }
    
    /**
     * Adds passed helpdesk_id and email of user to users_pending table
     * @access public
     * @param string $user
     * @return void
     */
    public function insertPendingUser($uid,$email)
    {
        
        if($email == null)
        {
            echo "found=".$email;
        }
        $data = array(
            "helpdesk_id"=>$uid,
            "email"=>$email);
        
        $this->db->insert('users_pending', $data);
        
    }
    
        
    /**
     * Deletes pending user from users_pending table
     * @access public
     * @param int $id
     * @return void
     */
    public function deletePendingUser($id)
    {
       $this->db->delete('users_pending', array('helpdesk_id'=>$id));
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