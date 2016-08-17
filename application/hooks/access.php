<?php
class Access
{

	var $CI;

	public function index()
	{
		$routing =& load_class('Router');
		$method = $routing->fetch_method();
		$class = $routing->fetch_class();

        $CI = & get_instance();
        $CI->load->library('session');
        $CI->load->helper('url');

        $URL = base_url();

        //$netid = $CI->session->userdata('netid');
        

		if($class=="auth" && ($method == "login" || $method == "logout")){
					
			$this->session->sess_destroy();
		}

	}	
}
?>