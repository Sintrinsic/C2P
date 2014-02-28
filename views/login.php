<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>C2P - Login</title>
<link rel="stylesheet" type="text/css" href="css/layout.css">
<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//Temporary cookie-based login prompt for Proof of Concept. Will be replaced by proper login once connected to ldap. 
	login = function(){
		 if ($("#loginInput").val().match(new RegExp(/[a-zA-Z]{2,16} [a-zA-Z]{2,16}/gi))){
				document.cookie = "name=" + $("#loginInput").val();
				alert('good');
				document.location.reload();
				return;
			}else{
				$("#loginInput").css("background-color","red").fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
				$("#loginInput").animate({backgroundColor:"white"}, 100);
				return;
			}
	};
	$("#loginInput").bind("keypress",function(e){
		if(e.which == 13){
			login();
		}
	});
	$("#loginButton").bind("click",function(){
		login();
	});
});
</script>
</head>
<body>
		<div id="loginPrompt" class="popup">
			<div id="loginTitle" class="div-title">
				Please enter your full name as at appears in spark.
			</div>
			<div class="center-aligned">	
				<input id="loginInput" size=32 >
				<button id="loginButton">Login</button>
			</div>
		</div>
</body>
</html>