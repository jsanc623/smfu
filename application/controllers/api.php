<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
    public $time;
    public $key;
    public $url;
    
    public function index(){
        redirect("/");
    }
    
    public function documentation(){
        # Load base data params
        $data = $this->common->getCommonData();
        $this->CI = get_instance();
        
        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("api/documentation", $data);
        $this->parser->parse("crumbs/footer", $data);
    }
    
    private function checkUrl($url){
        $url = strtolower($url);
        
        if(filter_var($url, FILTER_VALIDATE_URL) === FALSE){
            $has_tld = strpos($url, ".");
            $has_http = strpos($url, "http://");
            $has_https = strpos($url, "https://");
            $has_smfu_director = strpos("smfu.in/index.php/director", $url);
            $has_smfu_nonindex_director = strpos("smfu.in/director", $url);
            
            # url is empty
            if(empty($url)){
                $url = "NULL";
            }
            
            # malicious user is trying to cause a loop redirect to the director
            if($has_smfu_director !== FALSE || $has_smfu_nonindex_director !== FALSE){
                $url = "http://smfu.in";
            }
            
            # url does not have a TLD
            if($has_tld === FALSE){
                redirect("http://smfu.in/?error=invalid+url+'".$url."'");
            }
            
            # http:// or https:// were not found in url string
            if($has_http === FALSE && $has_https === FALSE){
                $url = "http://" . $url;
            }
        }
        
        return $url;
    }
    
    public function shorten(){
        $this->CI = get_instance();
        $this->load->library("surbl/blacklist");
        
        $this->time = $this->input->post("time", TRUE);
        $this->url = $this->input->post("url", TRUE);
        $userMarker = $this->input->post("userMarker", TRUE);
        $requestMarker = $this->input->post("requestMarker", TRUE);
        $this->key = $this->input->post("key", TRUE) ? $this->input->post("key", TRUE) : $this->session->userdata("key");
       
        $this->url = $this->checkUrl($this->url);
        
        # Is this an API call?
        if(strlen($userMarker) > 0 && strlen($requestMarker) > 0){
            $data = array("served" => "true");
            $this->db->where("userMarker", $userMarker)->where("requestMarker", $requestMarker)->update("requestQueue", $data);
            $api = TRUE;
        } else {
            $api = FALSE;
        }
        
        # No url, send back home or output error if API call
        if(strlen($this->url) == 0){
            if($api == FALSE)
                redirect("/?error=no+url");
            else
                echo json_encode(array("ERROR" => "No Url"));
        }
    
        # Does the URL already exist in the db?
        $urlExists = $this->db->select("*")->from("redirects")->where("url", $this->url)->limit(1)->get()->result();
        
        # If url exists, send back home with data, otherwise output JSON for API call
        if(count($urlExists) > 0){
            if($api == FALSE){
                redirect("/?key=" . $urlExists[0]->urlkey);
            } else {
                echo json_encode($urlExists);
                exit;
            }
        }
        
        if($this->blacklist->spam_check || preg_match("([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})", $this->url)) {
            # It's spam, redirect to Google
            $urlExists = $this->db->select("*")->from("redirects")->where("urlkey", "444979")->limit(1)->get()->result();
            if($api == FALSE){
                redirect("/?key=" . $urlExists[0]->urlkey);
            } else {
                echo json_encode($urlExists);
                exit;
            }
        } else {
            $exists = array("0" => 0); $key = ""; $url = "";
            
            while(count($exists) > 0){
                $key = $this->generateUrlKey();
                $url = "http://smfu.in/" . $key;
                $exists = $this->db->select("*")->from("redirects")->where("urlkey", $key)->limit(1)->get()->result();
            }
            
            $username = $this->db->select("requestUser")->from("requestQueue")->where("requestMarker", $requestMarker)->get()->result();
            $username = $username[0]->requestUser;
            $userId = $this->db->select("id")->from("apiUsers")->where("username", $username)->get()->result();
            $userId = $userId[0]->id;
            
            $data = array("urlkey" => $key, "url" => $this->url, "time" => time(), "api_user_id" => ($userId ? $userId : 0));
            $this->db->insert("redirects", $data);
            
            $urlExists = $this->db->select("*")->from("redirects")->where("urlkey", $key)->limit(1)->get()->result();
            if($api == FALSE){
                redirect("/?key=" . $urlExists[0]->urlkey);
            } else {
                echo json_encode($urlExists);
                exit;
            }
        }
        
        redirect("/");
    }

    private function generateUrlKey($length = 6){
        $consonants = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 1;
            }
        }
        return $password;
    }
    
    /**
     * Type: EXTERNAL
     * External call to generate a marker
     */
    public function genMarker(){
        $marker = array("userMarker" => uniqid(time() . "-", TRUE));
        echo json_encode($marker);
    }
    
    /**
     * Type: EXTERNAL
     * External calls require user to get this time prior to sending the request
     */
    public function getTime(){
        $time = array("time" => $this->common->genHiddenTime());
        echo json_encode($time);
    }
    
    /**
     * Type: EXTERNAL
     * External calls require validation of their key by saying they are ready to send a request
     */
    public function dockRequestReady(){
        $this->CI = get_instance();
        $this->CI->load->library("common");
        
        $key = $this->input->post("key", TRUE);
        $time = $this->input->post("time", TRUE);
        $marker = $this->input->post("marker", TRUE);

        if($key == FALSE || strlen($key) < 64){ json_encode(array("Bad Request" => "[Error 3201: Missing Key]")); exit; }
        if($time == FALSE || strlen($time) < 9){ json_encode(array("Bad Request" => "[Error 3207: Missing Time]")); exit; }
        if($marker == FALSE || strlen($marker) < 10){ json_encode(array("Bad Request" => "[Error 3205: Missing Marker]")); exit; }
        
        $time = $this->common->parseHiddenTime($time); 
        
        $r = $this->db->select("*")->from("apiUsers")->where("key", $key)->get()->result(); 
        $user = (array)$r[0];
        
        $data['receivedTime'] = time();
        $data['requestTime'] = $time;
        $data['requestKey'] = $key;
        $data['requestMarker'] = uniqid(strrev($time) . "-", TRUE);
        $data['requestUser'] = $user['username'];
        $data['userMarker'] = $marker;
        
        if(trim($key) == trim($user['key'])){
            $this->db->insert("requestQueue", $data);
            die(json_encode(array("userMarker" => $data['userMarker'], "requestMarker" => $data['requestMarker'])));
        } else {
            die(json_encode(array("Error" => "Wrong key.")));
        }
    }
}