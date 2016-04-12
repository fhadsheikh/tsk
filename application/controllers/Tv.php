<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Tv extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pusher');
        $this->load->model('Clockwork_model');
        $this->load->model('Database_model');
        $this->load->model('Helpdesk_model');
        
        
       header("Access-Control-Allow-Origin: *");
        
    }
    
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
    
    public function support_get()
    {
        
        $data = $this->Clockwork_model->getExpiryDates();
        
        
        foreach($data['expired'] as $key => $expired)
        {
            $isHidden = $this->Database_model->isHidden($expired['personid']);
            if(!$isHidden){
                $name = $this->Database_model->lookupClient($expired['personid']);
                $expiredClients[$key]['name'] = substr($name[0]->name, 0, 40);
                $expiredClients[$key]['date'] = $expired['date'];
            }
        }
        
        foreach($data['expiring'] as $key => $expiring)
        {

            $isHidden = $this->Database_model->isHidden($expiring['personid']);
            if(!$isHidden){
                $name = $this->Database_model->lookupClient($expiring['personid']);
                $expiringClients[$key]['name'] = $name[0]->name;
                $expiringClients[$key]['date'] = $expiring['date'];
            }
        }
        
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
    
}
    