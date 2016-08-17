<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Getedit extends CI_Controller {
	
	 function __construct() {
        parent::__construct();
        $this->load->model('imds_m', 'cd'); // załadowanie modelu imds_m jako 'cd'
    }
	
	function setimds(){ // Zmien imds -> edycja
		
		$imds_data = $this->input->get(); // Pobierz z formularzy		
		$error = $this->cd->setimds($imds_data); // Wyślij do modelu funkcji setimds z danymi
		$this->load->view('imds_edit_json', array('error'=>$error)); // JSON
	}
	
	function deleteimds(){ // Usuwanie wpisu
		
		$imds_data = $this->input->post(); // Pobierz postem
		$delete = $this->cd->deleteimds($imds_data); // przejdz do modelu i funkcji deleteimds
		$this->load->view('imds_delete_json', array('rezult'=>$delete)); // JSON
	}
	
}
?>