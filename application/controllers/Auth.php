<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {
    
    public function index()
    {
        redirect(''); // Przekierowanie na strone główną
    }
    
    public function login() // Logowanie
    {
        $data['error']="";
        $this->load->view('logform',$data); // Widok z logowaniem (jeśli błąd to pokaż)
    }
    
    public function logout() // Wyloguj
    {
        $this->session->sess_destroy(); // Niszczenie sesji
        redirect('');        // Przekieruj na stronę główną
    }
    
    public function check(){ // Funkcja sprawdzająca czy został wcisnięty cancel
        if ($this->input->post('cancel')) {
            redirect(''); // Przekieruj na główną
        }
        $netid=strtolower($this->input->post('netid',TRUE));
        $pass=$this->input->post('pass');
        $this->load->model('auth_m'); // Wczytanie modelu auth_m
        
        $authorized=FALSE;
        
        if (! $this->session->userdata('netid')){
            
            $authorized = $this->auth_m->check_auth($netid, $pass);
            if ($authorized){
                $user_info=$this->auth_m->get_user_info($netid,$pass);
                $this->auth_m->ad2db($user_info);
                $user_info = $this->auth_m->get_user_db($netid);
                
                if ($user_info['netid']){
                    $this->session->set_userdata($user_info);
                }
            }
        } else {
            $authorized=TRUE;
        }
        if ($authorized){
            redirect('');
		} else {
            $data['error']="Niepoprawny NetID lub hasło"; // błąd złe hasło lub netid
            $this->load->view('logform',$data);    // Wyświetl widok logowania
        }
    }
}