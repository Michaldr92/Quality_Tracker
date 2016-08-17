<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Główny kontroler

class Imds_c extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('imds_m', 'cd'); // załadowanie modelu imds_m jako 'cd'
		$this->load->model('auth_m'); // załadowanie auth_m
    }

    function index() {
        $customers = $this->cd->getcustomers(); // Pobierz customersów
		$users = $this->cd->getusers(); // Pobierz użytkowników
		$abbr = $this->cd->getabbr(); // Pobranie abbr
		$session = $this->auth_m->getSession(); // Utwórz sesje		
		$out = array('customers' =>$customers, 'users'=>$users, 'abbr'=>$abbr,'session'=>$session); // zapisz jako tablice powyższe
		$this->load->view('imds_v', $out); 	// Prześlij do widoku imds_v
		
    }

    function cd_list() { // Pobierz liste tracków
        $results = $this->cd->get_cd_list();
        echo json_encode($results); // JSON
    }

}

?>