<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<title>IMDS</title>
<style>
	body{
		background-color:#D0D0D0;
	}
	
	#main_div{
		width:100%;
		height:100%;
		text-align:center;
		margin-top:130px;
		font-family: arial, sans-serif;
	}
	
	#logon_window{
		position:absolute;
		top:50%;
		margin-top:-167px;
		left:50%;
		margin-left:-311px;
		width:622px;
		height:334px;
		background:url("<?php echo base_url();?>assets/img/log.png");
		background-repeat:no-repeat;

		box-shadow : 8px 8px 10px #aaa;
		-moz-box-shadow : 8px 8px 10px #aaa;
		-webkit-box-shadow : 8px 8px 10px #aaa;
	}
	
	.log_form{
		position:absolute;
		width:250px;
		height:150px;
		top:150px;
		left:330px;
	}

	.log_form div{
		margin:10px 0 10px 0;
		text-align:right;
		font-size:12px;
		height:20px;
		width:100%;
		line-height:200%;
	}

	.log_form input{
		float:right;
		margin:0 0 0 10px;
		width:150px;
	}
	
	.log_form input.btn{
		width:70px;
	}

	#input_submit{
		width:70px;
	}
	
	.error_div{
		color:red;		
	}
	
</style>
	
</head>


<body>
<div id="main_div">
<div id="logon_window">
	<div class="log_form">
		<form method="post" action="<?php echo base_url();?>auth/check">
			<div><input type="text" name="netid" class="input_text">NetID:</div>
			<div><input type="password" name="pass" class="input_text">Hasło:</div>
			<div>
			<input type="submit" name="logon" id="input_submit" value="Zaloguj" class="btn">
			<input type="submit" name="cancel" value="Anuluj" class="btn">
			</div>
			<div class="error_div"><?php echo $error;?></div>
		</form>
	</div>
</div>
</div>

</body>
</html> 