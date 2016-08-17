<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Getdata extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model('imds_m', 'cd');  // Załadowanie modelu imds_m jako 'cd'
    }
	
	function getimds($idai){ // Pobierz wpisy
		$list = $this->cd->getimds($idai); // Przekazanie danych do modelu
		$this->load->view('imds_list_json', array('response'=>$list)); // Odpowiedź JSON
	}
	
	
}
?>