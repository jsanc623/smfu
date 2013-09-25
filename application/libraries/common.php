<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Common MY Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Common
 * @author      Juan L. Sanchez
 */
class Common {
    public $page_title;
    public $page_description;
    public $google_analytics;
    private $CI;
    
    public function __construct(){
        $this->CI =& get_instance();
         
        $options = $this->CI->db->get("options")->result();
        
        foreach($options as $o){
            switch($o->key){
                case "page_title" : $this->page_title = $o->value; break;
                case "page_description" : $this->page_description = $o->value; break;
                case "google_analytics" : $this->google_analytics = $o->value; break;
            }
        }
    }
    
    public function isUserLoggedIn(){
        $this->CI =& get_instance(); 
        if($this->CI->session->userdata("id")){
            return true;
        } else {
            return false;
        }
    }
    
    public function getCommonData(){
        $data["page_title"]       = $this->page_title;
        $data["page_description"] = $this->page_description;
        $data["google_analytics"] = $this->google_analytics;
        $data['base_url'] = substr(base_url(), 0, -1);
        
        return $data;
    }
    
    public function genHiddenTime(){
        $time = time();
        $time = base64_encode($time);
        $time = strrev($time);
        $time = str_split($time, strlen($time) / 2);
        $time = $time[1] . $time[0];
        return $time;
    }
    
    public function parseHiddenTime($time){
        if(strlen($time) < 2) return FALSE;
        $time = str_split($time, strlen($time) / 2);
        $time = $time[1] . $time[0];
        $time = strrev($time);
        $time = base64_decode($time);
        return $time;
    }
}

/* End of file Common.php */
/* Location: ./application/libraries/MY_Common.php */
