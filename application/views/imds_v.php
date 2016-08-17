<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>IMDS Tracker</title>


    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.contextMenu.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/imds.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.dataTables.min.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui.css" />


    <script>
        var base_url = "<?=base_url();?>";
        var netid = "<?php echo $session['netid'];?>";
        var first_name = "<?php echo $session['first_name'];?>";
        var last_name = "<?php echo $session['last_name'];?>";
        var email = "<?php echo $session['email'];?>";
        var level = <?php echo $session['level'];?>;
    </script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/jquery-1.11.3.min.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/jquery-dateFormat.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/main_cds_IM.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/main_dialog_IM.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/main.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/jquery.contextMenu.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/jquery.ui.position.min.js"></script>
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>


</head>

<body>
    <div class="pasek"></div>
    <div id="logo">
        <?php echo '<img src="'.base_url().'assets/img/logo.png"/>'; // Logo ?>
    </div>


    <div class="links_div">

        <?php
	// SESJA - Uzyskanie danych użytkownika
		if (isset($session['netid']) && $session['netid']!='') {
			echo '<span id= "wyloguj" class = "ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><a href="'.base_url().'auth/logout">Wyloguj: '.$session['first_name'].'&nbsp;'.$session['last_name'].'</a></span>';
		} else {
			echo '<span id = "loguj" class = "ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><a href="'.base_url().'auth/login">Zaloguj</a></span>';	
		}		

	?>
    </div>
    <div class="naglowek">IMDS Tracker</div>

    <div id="tabeleczka">
        <table id="imds" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Application</th>
                    <th>BWI PN</th>
                    <th>Customer PN</th>
                    <th>IMDS ID </th>
                    <th>Rev</th>
                    <th>Request Date</th>
                    <th>Report Date</th>
                    <th>Accepted Date</th>
                    <th>PPAP Date</th>
                    <th>Status</th>
                    <th>Requester</th>
                    <th>Comments</th>
                    <th>edit</th>
                </tr>
            </thead>
        </table>

        <div id="dialog-form" title="Edit">


            <form id=i mds_form>
                <fieldset id="imds_editform">
                    <label for="customer">Customer:*</label>
                    <select name="customer_id" id="customer" class="text ui-widget-content ui-corner-all">
	  <option value="Wybierz.." selected >Wybierz..</option>
			<?php
				foreach($customers as $value){
					echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
				}
			?>			
	  </select>
                    <br/>
                    <label for="customer_platform">Application:</label>
                    <input type="text" name="customer_platform" id="customer_platform" value="" class="text ui-widget-content ui-corner-all">
                    <label for="bwi_part">BWI PN:</label>
                    <input type="text" name="bwi_part" id="bwi_part" value="" class="text ui-widget-content ui-corner-all">
                    <label for="customer_part">Customer PN:</label>
                    <input type="text" name="customer_part" id="customer_part" value="" class="text ui-widget-content ui-corner-all">
                    <label for="imds_id">IMDS ID:</label>
                    <input type="text" name="id" id="imds_id" value="" readonly="readonly" class="text ui-widget-content ui-corner-all">
                    <label for="rev">Rev:<span id="current_rev_span"></span></label>
                    <input type="text" name="rev" id="rev" value="" readonly="readonly" class="text ui-widget-content ui-corner-all">
                    <label for="request_date">Request Date:</label>
                    <input type="text" name="request_date" id="request_date" value="yy-mm-dd" class="text ui-widget-content ui-corner-all datepicker">
                    <label for="report_date">Report Date:</label>
                    <input type="text" name="report_date" id="report_date" value="" class="text ui-widget-content ui-corner-all datepicker">
                    <label for="accepted_date">Accepted Date:</label>
                    <input type="text" name="accepted_date" id="accepted_date" value="" class="text ui-widget-content ui-corner-all datepicker">
                    <label for="ppap_date">PPAP Date:</label>
                    <input type="text" name="ppap_date" id="ppap_date" value="" class="text ui-widget-content ui-corner-all datepicker">
                    <label for="status">Status:*</label>
                    <select name="status_id" id="status" class="text ui-widget-content ui-corner-all">
	  <option value="Wybierz.." selected >Wybierz..</option>
			<?php
				foreach($abbr as $value){
					echo '<option value="'.$value['id'].'">'.$value['abbr'].'</option>';
				}
			?>			
	  </select>
                    <br/>
                    <label for="requester">Requester:*</label>
                    <select name="requester_id" id="requester" class="text ui-widget-content ui-corner-all">
	  <option value="Wybierz.." selected >Wybierz..</option>
			<?php
				foreach($users as $value){
					echo '<option value="'.$value['netid'].'">'.$value['email'].'</option>';
				}
			?>			
	  </select>
                    <br/>
                    <label for="comments">Comments:</label>
                    <textarea name="comment" id="comments" class="text ui-widget-content ui-corner-all"></textarea>
                    <input type="hidden" name="prev_rev" id="prev_rev" value="" readonly="readonly" class="text ui-widget-content ui-corner-all">
                    <input type="hidden" name="tryb" id="tryb" value="" readonly="readonly" class="text ui-widget-content ui-corner-all">
                    <input type="hidden" name="idai" id="idai" value="" readonly="readonly" class="text ui-widget-content ui-corner-all">

                    <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                </fieldset>
            </form>
        </div>
        <button id="add_row">Add row</button>
    </div>


</body>

</html>