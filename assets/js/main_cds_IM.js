var dialog, form; // Zmienne globalne dla dialogu -> wykorzystane w imds_dialog.js
    
$(document).ready(function () {
		
	//Inizjalizacja dialogu -> plik imds_dialog.js
	dialog = dialogInit();
	
	// Utworzenie datatables	
	var d_table = $('#imds').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": base_url+'imds_c/cd_list',
		"drawCallback": function( settings ) {
			// Rysowanie kolumny edit (definicja w bazie)		
			$('#imds a.iedit').each(function(){
				var a = $(this);					 
				var tekst = a.text();
				a.data('idai',tekst);
				a.text('');
			});
			// Określenie poziomu dostępu (10, 20, 30, 40, 50)		
			if(level < 11)
			{
				var column = d_table.column(13);
				column.visible(false);
			}
			if(level > 10)
			{
				$("#add_row").show();	
			}
					
			$('#imds td:nth-child(11)').each(function(){
				var td = $(this);
				var status = td.text();
						
				status = status.replace(' ', '');
				td.parent().addClass(status);							
			});
		}
    });	

		$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });	// Ustawienie formatu daty
		
		//Prawy klik w ołówek 'edit' - 3 opcje do wyboru		
		$.contextMenu({
			selector: '.iedit', 
			trigger: 'left',
			callback: function(key, options) {
				var a = $(this);
				idai=a.data('idai');
						
				if (key == "edit"){
					edit_imds(idai); // Edycja
				}
				if (key == "n_rev"){
					edit_imds(idai, true); // Nowa rewizja
				}
				if (key == "delete"){
					delete_imds(idai); // Usuwanie wpisu
				}
				if(key == "copy"){
					edit_imds(idai, false, true); // Kopiowanie
					//copy_imds(idai, false, true);
				}
			},
			items: { // Context Menu -> LPM
				"edit": {name: "Edit"},
				"copy": {name: "Copy"},
				"n_rev": {name: "New Revision"},
				"sep1": "---------",
				"delete": {name: "Delete"}
			}
		});			
	});		
//------------------------------------------------------------------- ------------------------------
		
	function edit_imds(idai){	//2 parametr true: nowa rewizja			  
		
		var copy = false;
		var new_rev = false;
		
			if (arguments[1]==true) new_rev = true;
			if (arguments[2]==true) copy = true;
			
				$("#dialog-form input").val('').prop('readonly', false).css('backgroundColor','white'); // Tylko do odczytu
				$("#dialog-form textarea").val('');
				$('#current_rev_span').text('');
				
				dialog.dialog( "open" ); // Otwarcie okna dialogowego
				if(idai > 0){ // Jesli idai jest większe od 0 to...
						
					$("#imds_id").prop('readonly', true).css('backgroundColor','#ececec');	// Tylko do odczytu
					$("#rev").prop('readonly', true).css('backgroundColor','#ececec');	// Tylko do odczytu
						
					var imds_data = []; // Utworzenie tablicy danych
					
					// AJAX					
					imds_data.push({'idai': idai});
						$.ajax({
							method: "GET",
							dataType: "json",
							data: {'imds_data': imds_data},
							url: base_url+'getdata/getimds/'+idai, // funkcja getimds -> model
							success: function(data){
								data = data[0];
								if (new_rev){ // Tryb nowej rewizji
									$('#idai').val(data['idai']);
									$("#rev").prop('readonly', false).css('backgroundColor','white');									
									$('#customer').val(data['customerid']);
									$('#customer_platform').val(data['customer_platform']);
									$('#bwi_part').val(data['bwi_part']);
									$('#customer_part').val(data['customer_part']);
									$('#imds_id').val(data['imds_id']);
									$('#current_rev_span').text('(Current rev:'+data['rev']+')');
									$('#prev_rev').val(data['rev']);
									$('#tryb').val('new_rev');										
										
								} else if(copy){ // Tryb kopiowania
									$('#idai').val(data['idai']);
									$('#customer').val(data['customerid']);
									$('#customer_platform').val(data['customer_platform']);
									$('#bwi_part').val(data['bwi_part']);
									$('#customer_part').val(data['customer_part']);
									$('#imds_id').val(data['imds_id']);
									$('#imds_id').prop('readonly', false).css('backgroundColor','white');
									$('#rev').val(data['rev']);	
									$('#rev').prop('readonly', false).css('backgroundColor','white');									
									$('#request_date').val(data['request_date']);	
									$('#report_date').val(data['report_date']);	
									$('#accepted_date').val(data['accepted_date']);	
									$('#ppap_date').val(data['ppap_date']);	
									$('#status').val(data['status_id']);	
									$('#requester').val(data['requester_id']);	
									$('#comments').val(data['comment']);	
									$('#tryb').val('copy');
								} 
								else { // Tryb nowego wpisu
									$('#imds_id').prop('readonly', false).css('backgroundColor','white');
									$('#idai').val(data['idai']);
									$('#customer').val(data['customerid']);
									$('#customer_platform').val(data['customer_platform']);
									$('#bwi_part').val(data['bwi_part']);
									$('#customer_part').val(data['customer_part']);
									$('#imds_id').val(data['imds_id']);
									$('#rev').val(data['rev']);
									$('#rev').prop('readonly', false).css('backgroundColor','white');		
									$('#request_date').val(data['request_date']);	
									$('#report_date').val(data['report_date']);	
									$('#accepted_date').val(data['accepted_date']);	
									$('#ppap_date').val(data['ppap_date']);	
									$('#status').val(data['status_id']);	
									$('#requester').val(data['requester_id']);	
									$('#comments').val(data['comment']);	
									$('#tryb').val('edit');
								}	
									
							},
							error: function(data) {
								alert( "Wystąpił błąd"); // Jeśli błąd to wyświetl
								console.log(data); 
							}
						});							
				}								
	}	
	// Funkcja usuwająca rekord
	function delete_imds(idai){ // Usuwanie wpisu z bazy
						
		if(confirm("Czy napewno chcesz usunąć ten rekord?")){
			$.ajax({
				dataType: "json",
				data: {'idai': idai},
				method:'POST',
				url: base_url+'getedit/deleteimds',			// kontroler - model -> getedit -> funkcja = deleteimds	
				success: function(data){
					$('#imds').DataTable().ajax.reload();			// Przeładuj tabele						
				}
			});	
		}		
	}	