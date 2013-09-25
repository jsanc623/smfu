<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {
    public $key;
    public $id;
    
    public function index(){
        redirect("/");
    }
    
    public function profile(){
        if($this->common->isUserLoggedIn() == false){
            redirect("/");
        }
        
        # Load base data params
        $data = $this->common->getCommonData();
        $this->CI = get_instance();
        
        $id = $this->getUserId();
        
        $user_result = $this->db->select("username, key, email, dateCreated, lastAccessed, ipAddress, country, gender, age")->from("apiUsers")->where("id", $id);
        $user_result = $user_result->get()->result();
        $results['user'] = (array)$user_result[0];
        
        $redirect_result = $this->db->select("*")->from("redirects")->where("api_user_id", $id)->get();
        foreach($redirect_result->result() as $key => $value){
            $results['redirects'][$key] = (array)$value;
        }
        
        $data['output'] = $results;
        
        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("users/profile", $data);
        $this->parser->parse("crumbs/footer", $data);
    }
    
    private function getUserId(){
        return $this->session->userdata("id");
    }
    
    public function register(){
        # Load base data params
        $data = $this->common->getCommonData();
        $this->load->helper("form");
        
        if($this->input->get("error") != FALSE)
            $data['error'] = '<div class="alert alert-danger">' . ucwords(urldecode($this->input->get("error"))) . '</div>';
        else 
            $data['error'] = "";

        $urlFormAttributes = array("id" => "registerForm", "name" => "registerForm", "role" => "form", "class" => "form-horizontal");
        $genders = array("M" => "Male", "F" => "Female");
        for($i = 8; $i < 100; $i++){ $ages[$i] = $i; }

        $data['form'] = form_open("users/createAccount", $urlFormAttributes);
        $data['form'] .= '<div class="form-group">' . form_label("Username", "username", array("class" => "col-lg-2 control-label"));
        $data['form'] .= '<div class="col-lg-10">' . form_input(array("name" => "username", "id" => "username", "placeholder" => "ksmith", "class" => "form-control")) . "</div>";
        $data['form'] .= '</div><div class="form-group">' . form_label("Password", "password", array("class" => "col-lg-2 control-label"));
        $data['form'] .= '<div class="col-lg-10">' . form_input(array("name" => "password", "id" => "password", "type" => "password", "placeholder" => "&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;", "class" => "form-control")) . "</div>";
        $data['form'] .= '</div><div class="form-group">' . form_label("Email", "email", array("class" => "col-lg-2 control-label"));
        $data['form'] .= '<div class="col-lg-10">' . form_input(array("name" => "email", "id" => "email", "placeholder" => "ksmith@gmail.com", "class" => "form-control")) . "</div>";
        $data['form'] .= '</div><div class="form-group">' . form_label("Country", "country", array("class" => "col-lg-2 control-label"));
        $data['form'] .= '<div class="col-lg-10">' . form_input(array("name" => "country", "id" => "country", "placeholder" => "United States", "class" => "form-control")) . "</div>";
        $data['form'] .= '</div><div class="form-group">' . form_label("Gender", "gender", array("class" => "col-lg-2 control-label"));
        $data['form'] .= form_dropdown("gender", $genders, "M");
        $data['form'] .= '</div><div class="form-group">' . form_label("Age", "age", array("class" => "col-lg-2 control-label"));
        $data['form'] .= form_dropdown("age", $ages, 24);
        $data['form'] .= '</div>' . form_submit(array("name" => "registerSubmit", "id" => "registerSubmit", "value" => "Register", "class" => "btn btn-default"));
        $data['form'] .= form_close();

        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("users/register", $data);
        $this->parser->parse("crumbs/footer", $data);
    }
    
    public function genKey(){
        $this->CI = get_instance();
        
        if(!$this->id) redirect("/");

        # Get user data where id is set
        $userdata = $this->db->select("username, password, dateCreated")->from("apiUsers")->where("id", $this->id)->limit(1)->get()->result();
        
        if(gettype($userdata) == "array" && count($userdata) > 0) {
            # Make life simpler for us
            $userdata = (array)$userdata[0];
            
            # Hash the raw data from DB (password is already SHA256)
            $username = hash("sha512", $userdata['username']);
            $password = hash("sha512", $userdata['password']);
            $dateCreated = hash("sha512", $userdata['dateCreated']);
            
            # Split the SHA512 strings into two parts
            $username = str_split($username, strlen($username) / 2);
            $password = str_split($password, strlen($password) / 2);
            $dateCreated = str_split($dateCreated, strlen($dateCreated) / 2);
            
            # Create the weaved key
            $weavedKey = $username[0] . $password[0] . $dateCreated[0] . $username[1] . $password[1] . $dateCreated[1];
            
            # Set the key for this user
            $this->key = strrev(hash("sha256", $weavedKey));
            
            return true;
        } else 
            redirect("/");
        
        return false;
    }
    
    public function createAccount(){
        $username = $this->input->post("username", TRUE);
        $password = hash("sha256", $this->input->post("password", TRUE));
        $email = $this->input->post("email", TRUE);
        $dateCreated = time();
        $lastAccessed = time();
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $country = $this->input->post("country", TRUE);
        $gender = $this->input->post("gender", TRUE);
        $age = $this->input->post("age", TRUE);
        
        if($username == FALSE || $password == FALSE || $email == FALSE){
            redirect("/users/register?missing+data");
        } else {        
            $data = array(
                "username" => $username, 
                "password" => $password, 
                "email" => $email, 
                "dateCreated" => $dateCreated, 
                "lastAccessed" => $lastAccessed,
                "ipAddress" => $ipAddress,
                "country" => $country,
                "gender" => $gender, 
                "age" => $age
            );
            
            # Check if user exists
            $id = $this->db->select("id")->from("apiUsers")->where("username", $username)->get()->result();

            if(count($id) > 0)
                redirect("/users/register?error=user+exists");
            else {
                # Insert the data
               $this->db->insert("apiUsers", $data);
            
                # Fetch the id of new user
                $this->id = $this->db->select("id")->from("apiUsers")->where("username", $username)->get()->result();
                $this->id = $this->id[0]->id;
    
                if($this->genKey() == TRUE){
                    $this->db->where("id", $this->id)->update("apiUsers", array("key" => $this->key));
                    redirect("/users/profile");
                } else {
                    redirect("/users/register?error=keygen+failure.+contact+admin");
                }
            }
        }
    }

    public function doLogin(){
        $username = $this->input->post("username", TRUE);
        $password = $this->input->post("password", TRUE);
        $password = hash("sha256", $password);
        
        $userData = $this->db->select("*")->from("apiUsers")->where("username", $username)->limit(1)->get()->result();
        $userData = (array)$userData[0];
        
        if(trim($password) == trim($userData['password'])){
            $this->session->set_userdata(array("id" => $userData['id'], "username" => $userData['username'], "key" => $userData['key']));
            $this->db->where("username", $this->username)->update("apiUsers", array("lastAccessed" => time(), "ipAddress" => $_SERVER['REMOTE_ADDR']));
            
            redirect("/home");
        } else {
            redirect("/users/login?error=incorrect+username+or+password");
        }
    }
    
    public function login(){
        # Load base data params
        $data = $this->common->getCommonData();
        $this->load->helper("form");
        
        if($this->input->get("error") != FALSE)
            $data['error'] = '<div class="alert alert-danger">' . ucwords(urldecode($this->input->get("error"))) . '</div>';
        else 
            $data['error'] = "";

        $urlFormAttributes = array("id" => "registerForm", "name" => "registerForm", "role" => "form", "class" => "form-horizontal");
        $genders = array("M" => "Male", "F" => "Female");
        for($i = 8; $i < 100; $i++){ $ages[$i] = $i; }

        $data['form'] = form_open("users/doLogin", $urlFormAttributes);
        $data['form'] .= '<div class="form-group">' . form_label("Username", "username", array("class" => "col-lg-2 control-label"));
        $data['form'] .= '<div class="col-lg-10">' . form_input(array("name" => "username", "id" => "username", "placeholder" => "ksmith", "class" => "form-control")) . "</div>";
        $data['form'] .= '</div><div class="form-group">' . form_label("Password", "password", array("class" => "col-lg-2 control-label"));
        $data['form'] .= '<div class="col-lg-10">' . form_input(array("name" => "password", "id" => "password", "type" => "password", "placeholder" => "&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;", "class" => "form-control")) . "</div>";
        $data['form'] .= '</div>' . form_submit(array("name" => "registerSubmit", "id" => "registerSubmit", "value" => "Login", "class" => "btn btn-default"));
        $data['form'] .= form_close();

        $this->parser->parse("crumbs/header", $data);
        $this->parser->parse("users/login", $data);
        $this->parser->parse("crumbs/footer", $data);
    }
    
    public function logout(){
        $this->session->sess_destroy();
        redirect("/home");
    }
}