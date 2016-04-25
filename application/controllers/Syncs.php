<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Sync extends REST_Controller {
    
    /**
     * Constructor for Syncs Controller
     * Used by ticketSync
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Live Sync for ticketSync app
     * @access ticketSync app
     * @return void 
     */
    public function live_get()
    {
        // Get latest tickets
        // Check tickets that have updates
        // Get comments for latest tickets
        // Check comments that are new
        // Send to clockwork
        // Send to internal db
        // Send to pusher
    }
    
}