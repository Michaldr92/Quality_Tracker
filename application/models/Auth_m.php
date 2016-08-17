<?php
class Auth_m extends CI_Model {

 function __construct()
    {
        parent::__construct();
		$this->load->library('adldap'); // Wczytanie dodatkowej biblioteki do łaczenia z AD przez LDAP
    }


  function check_auth($netid, $pass){ // Sprawdź netid i hasło
	return $this->adldap->authenticate($netid,$pass);	
  }

  function get_user_info($netid, $pass){ // Pobierz dane o użytkowniku
		$tab['first_name']=$tab['last_name']=$tab['email']=$tab['netid']='';
		
			$info=$this->adldap->user_info($netid,array('mail',"sn","givenname"));
			
			if (isset($info[0]['givenname'][0])) $tab['first_name']=$info[0]['givenname'][0];
			if (isset($info[0]['sn'][0])) $tab['last_name']=$info[0]['sn'][0];
			if (isset($info[0]['mail'][0])) $tab['email']=$info[0]['mail'][0];
			$tab['netid']=$netid;
			$this->load->view('imds_v');
		
		
		return $tab;
	}
	
	public function get_user_db($netid){ // Pobierz użytkownika z bazy danych
	
		$r = $this->db->get_where('users',array('netid'=>$netid))->result_array();
		return $r[0];
	}
	
	function ad2db($adinfo){ // z active directory do bazy danych sql
		$adinfo['last_login']=date("Y-m-d H:i:s");
		$query = $this->db->where('netid', $adinfo['netid']);
		$ile = $this->db->update('users', $adinfo); 
		if($this->db->affected_rows() == 0)
		{
			$this->db->insert('users', $adinfo); 
		}
	}
	
	function getSession(){ // Sesja - Tworzenie
		$session = $this->session->userdata();
		
		if (!isset($session['netid'])) $session['netid'] = '';
		if (!isset($session['first_name'])) $session['first_name'] = '';
		if (!isset($session['last_name'])) $session['last_name'] = '';
		if (!isset($session['email'])) $session['email'] = '';
		if (!isset($session['level'])) $session['level'] = 0;	
		return $session;	
	}
}