<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller {
    public function index(){
        redirect("/");
    }
    
    public function about(){
        # Load base data params
        $data = $this->common->getCommonData();
        $this->load->helper("form");

        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("site/about", $data);
        $this->parser->parse("crumbs/footer", $data);
    }
    
    public function stats(){
        # Load base data params
        $data = $this->common->getCommonData();
        $this->load->helper("form");

        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("site/stats", $data);
        $this->parser->parse("crumbs/footer", $data);
        
    }
}
    