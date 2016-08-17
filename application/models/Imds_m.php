<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Imds_m extends CI_Model {

    private $v_imds = 'v_imds';

    function __construct() {
        
    }
	
	// WYKORZYSTANIE ZEWNĘTRZNEJ BIBLIOTEKI DO DATATABLES + SERVERSIDE
    function get_cd_list() {
        /* Array of table columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = array('customer', 'customer_platform', 
		'bwi_part', 'customer_part', 'imds_id', 'rev', 
		'request_date', 'report_date', 'accepted_date', 
		'ppap_date', 'status', 'requester', 'comment_abr', 'iedit');

        /* Indexed column (used for fast and accurate table cardinality) */
        //$sIndexColumn = "*";

        /* Total data set length */
        $sQuery = "SELECT COUNT(*) AS row_count FROM v_imds";
		$rResultTotal = $this->db->query($sQuery);
        $aResultTotal = $rResultTotal->row();
        $iTotal = $aResultTotal->row_count;

        /*
         * Paging
         */
        $sLimit = "";
        $iDisplayStart = $this->input->get_post('start', true);
        $iDisplayLength = $this->input->get_post('length', true);
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $sLimit = "LIMIT " . intval($iDisplayStart) . ", " .
                    intval($iDisplayLength);
        }

        $uri_string = $_SERVER['QUERY_STRING'];
        $uri_string = preg_replace("/\%5B/", '[', $uri_string);
        $uri_string = preg_replace("/\%5D/", ']', $uri_string);

        $get_param_array = explode("&", $uri_string);
        $arr = array();
        foreach ($get_param_array as $value) {
            $v = $value;
            $explode = explode("=", $v);
            $arr[$explode[0]] = $explode[1];
        }

        $index_of_columns = strpos($uri_string, "columns", 1);
        $index_of_start = strpos($uri_string, "start");
        $uri_columns = substr($uri_string, 7, ($index_of_start - $index_of_columns - 1));
        $columns_array = explode("&", $uri_columns);
        $arr_columns = array();
        foreach ($columns_array as $value) {
            $v = $value;
            $explode = explode("=", $v);
            if (count($explode) == 2) {
                $arr_columns[$explode[0]] = $explode[1];
            } else {
                $arr_columns[$explode[0]] = '';
            }
        }

        /*
         * Ordering
         */
        $sOrder = "ORDER BY ";
        $sOrderIndex = $arr['order[0][column]'];
        $sOrderDir = $arr['order[0][dir]'];
        $bSortable_ = $arr_columns['columns[' . $sOrderIndex . '][orderable]'];
        if ($bSortable_ == "true") {
            $sOrder .= $aColumns[$sOrderIndex] .
                    ($sOrderDir === 'asc' ? ' asc' : ' desc');
        }

        /*
         * Filtering
         */
        $sWhere = "";
        $sSearchVal = $arr['search[value]'];
        if (isset($sSearchVal) && $sSearchVal != '') {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                $sWhere .= $aColumns[$i] . " LIKE '%" . $this->db->escape_like_str($sSearchVal) . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        /* Individual column filtering */
        $sSearchReg = $arr['search[regex]'];
        for ($i = 0; $i < count($aColumns); $i++) {
            $bSearchable_ = $arr['columns[' . $i . '][searchable]'];
            if (isset($bSearchable_) && $bSearchable_ == "true" && $sSearchReg != 'false') {
                $search_val = $arr['columns[' . $i . '][search][value]'];
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i] . " LIKE '%" . $this->db->escape_like_str($search_val) . "%' ";
            }
        }

        /*
         * SQL queries
         * Get data to display
         */
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM $this->v_imds
        $sWhere
        $sOrder
        $sLimit
        ";
        $rResult = $this->db->query($sQuery);

        /* Data set length after filtering */
        $sQuery = "SELECT FOUND_ROWS() AS length_count";
        $rResultFilterTotal = $this->db->query($sQuery);
        $aResultFilterTotal = $rResultFilterTotal->row();
        $iFilteredTotal = $aResultFilterTotal->length_count;

        /*
         * Output
         */
        $sEcho = $this->input->get_post('draw', true);
        $output = array(
            "draw" => intval($sEcho),
            "recordsTotal" => $iTotal,
            "recordsFiltered" => $iFilteredTotal,
            "data" => array()
        );

        foreach ($rResult->result_array() as $aRow) {
            $row = array();
            foreach ($aColumns as $col) {
                $row[] = $aRow[$col];
            }
            $output['data'][] = $row;
        }

        return $output;
    }

	// Pobranie wszystkich danych z bazy danych
	function getimds($idai){
		
		$q = 'SELECT idai, customerid, customer_platform, bwi_part, customer_part, imds_id, rev, request_date, report_date, accepted_date, ppap_date, status_id, requester_id, comment FROM v_imds WHERE idai = '.$idai;
		//file_put_contents('q.txt',$q);
		$result = $this ->db -> query($q)->result_array();
		
		return $result;

	}
	
	// pobranie customersów z bazy
	function getcustomers(){
		$q = 'SELECT id, name FROM customers ORDER BY id';
		$result = $this ->db -> query($q)->result_array();
		
		return $result;
	}
	
	// pobranie użytkowników z bazy
	function getusers(){
		$q = 'SELECT netid, email FROM users ORDER BY last_name';
		$result = $this ->db -> query($q)->result_array();
		
		return $result;
	}
	
	// pobranie abbr z bazy
	function getabbr(){
		$q = 'SELECT id, abbr FROM statuses ORDER BY id';
		$result = $this ->db -> query($q)->result_array();
		
		return $result;
	}
	
	// Zmiana imds, edycja
	function setimds($imds_data){
		
		$session = $this->session->userdata(); // Ustawienie sesji
		
		if($session['level'] > 10 )  // Jeżeli level jest większy od 10 (większe uprawnienia)
		{		
			$error=$this->validate($imds_data); // Funkcja walidacyjna
		
			$tryb = $imds_data['tryb']; // Tryb -> edycja, nowy
			$imds_data['edited_by_id']= $session['netid']; // Kto edytuje? Widzimy po sesji NETID
			$imds_data['edited_date']=date("Y-m-d H:i:s"); // Data edycji
			unset($imds_data['prev_rev']);
			unset($imds_data['tryb']);
			
				if ($error==''){ // Jezeli nie ma blędu to...
					
					if ($tryb == 'edit'){ //edit
						$this->db->where(array('idai'=>$imds_data['idai']));
						$this->db->update('imds', $imds_data);  // Update
						
					} elseif($tryb == 'new_rev'){//new rev
							
						unset($imds_data['idai']);
						$this->db->insert('imds', $imds_data); // Nowa rewizja
							
					} elseif ($tryb == ''){ // new
						unset($imds_data['idai']);
						$this->db->insert('imds', $imds_data); // Dodanie nowego wpisu
					
					} elseif ($tryb == 'copy'){ //copy
						unset($imds_data['idai']);
						$this->db->insert('imds', $imds_data); // Jeżeli kopiujemy to dodaj
					}
				}
		} else{
			return 'Access Denied'; // Odmowa dostępu
		}

			return $error;	
	}
	
	function deleteimds($imds_data){ // Usuwanie wpisu z bazy
		$session = $this->session->userdata();
		
		if($session['level'] > 10){	
			$this->db->where(array('idai'=>$imds_data['idai'])); // Pobierz dane
			$this->db->delete('imds');  // Usuń
		}	
	}
	
	function validate($imds_data) // Walidacja
	{
		$error = "";
		
		if($imds_data['customer_id'] == "Wybierz.." || $imds_data['customer_id'] == ""){ 
			$error .="Please choose customer\n"; 
		}

		if (isset($imds_data['prev_rev']) && $imds_data['prev_rev']!='' && $imds_data['rev'] <= $imds_data['prev_rev']){
			$error .="Revision must be greater than previous one\n"; 
		}

		if($imds_data['status_id'] == "Wybierz.." || $imds_data['status_id'] == ""){
			$error .="Please choose Status\n"; 
		}
		if($imds_data['requester_id'] == "Wybierz.." || $imds_data['requester_id'] == ""){
		$error .="Please choose a Requester\n"; 
		}
		
		if($imds_data['tryb'] == ('new_rev')){ // Tryb nowej rewizji (rev)
			
			$result = $this->db->get_where('imds', array('id'=>$imds_data['id'], 'rev'=>$imds_data['rev'] ));
			$rows = $result->num_rows();
				
				if($rows > 0)
				{
					$error .="Duplicate IMDS ID or Revision, please write a new data\n";
				}
		}
		
		if($imds_data['tryb'] == ''){ // Tryb dodawania nowego wpisu
			
			$result = $this->db->get_where('imds', array('id'=>$imds_data['id']));
			$rows = $result->num_rows();
				
				if($rows > 0)
				{
					$error .="Duplicate IMDS ID, please write a new data\n";
				}
		}
		
		if($imds_data['tryb'] == ('edit')){ // Tryb edycji
			$result = $this->db->get_where('imds', array('id'=>$imds_data['id']));
			$rows = $result->num_rows();
			
				if($rows > 0)
				{
					$error .="Duplicate IMDS ID, please write a new data\n";
				}
		}
		return $error;
	}

}
