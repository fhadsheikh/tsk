<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->helper('test');
    }
    public function index()
    {
        echo "Hello";
    }
    public function test()
    {
        phpinfo();
        $this->load->model('Clockwork_model');
        $this->Clockwork_model->getExpiryDates();
    }
}