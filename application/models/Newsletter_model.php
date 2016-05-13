<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter_model extends CI_Model {
    
    public $listData;
    
    public function __construct()
    {
        parent::__construct();
        $this->listData = $this->getListData();
    }
    
    public function getListData()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://us13.api.mailchimp.com/3.0/lists/1fa70609cd",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic dXNlcm5hbWU6NDY0OTdmYjRhM2EyZjc5MGM0MDM3Y2Q4ZGI5MTE4ZTUtdXMxMw==",
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: 9c60c685-7c4c-9d46-fb6d-0e81238b4de5"
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        return json_decode($response);
        
    }
    
    public function getSubscriberCount()
    {
        return $this->listData->stats->member_count;
    }
    
}