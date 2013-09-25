<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	public function index(){
        # Load base data params
        $data = $this->common->getCommonData();
        $this->load->helper("form");
        $this->load->helper("url");
        
        $uriString = uri_string();
        if(strlen($uriString) <= 8){
            $checkExists = $this->db->select("*")->from("redirects")->where("urlkey", $uriString)->limit(1)->get()->result();
            
            if(count($checkExists) > 0){
                redirect("/director?url=" . $checkExists[0]->url);
            }
        }
        
        $urlFormAttributes = array("id" => "urlForm", "name" => "urlForm");
        $urlFormHidden = array("time" => $this->common->genHiddenTime());
        
        $data['urlwithkey'] = "";
        $data['hide-if-no-key'] = "hide";
        $data['hide-if-no-val-error'] = "hide";
        $data['hide-if-no-error'] = "hide";
        
        if($this->input->get("key", TRUE)){
            $data['urlwithkey'] = "http://smfu.in/" . $this->input->get("key", TRUE);
            $data['hide-if-no-key'] = "";
        }
        
        $data['form'] = form_open("api/shorten", $urlFormAttributes, $urlFormHidden);
        $data['form'] .= form_input(array("name" => "url", "id" => "url",));
        $data['form'] .= form_submit(array("name" => "urlSubmit", "id" => "urlSubmit", "value" => "Shorten"));
        $data['form'] .= form_close();

        $data['validation_errors'] = validation_errors();
        $data['error'] = ucwords($this->input->get("error", TRUE));

        if(strlen($data['validation_errors']) > 0)
            $data['hide-if-no-val-error'] = "";
        
        if(strlen($data['error']) > 0)
            $data['hide-if-no-error'] = "";

        $this->parser->parse("crumbs/header", $data);
		$this->parser->parse("home", $data);
        $this->parser->parse("crumbs/footer", $data);
	}
}