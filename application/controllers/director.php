<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Director extends CI_Controller {
    public function index(){
        # Load base data params
        $data = $this->common->getCommonData();
        
        $data['DirectorJS'] = '$(document).ready(function(){ Director.getUrl().timeCountdown(); });';
        
        $url = $this->input->get("url", TRUE);
        $result = $this->db->select("id, count")->from("redirects")->where("url", $url)->limit(1)->get()->result();
        $result = $result[0];
        $result->count = $result->count + 1;
        
        $this->db->where("id", $result->id)->update("redirects", array("count" => $result->count));

        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("director/home", $data);
        $this->parser->parse("crumbs/footer", $data);
    }
}
    