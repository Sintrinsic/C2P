<?php session_start();
$_SESSION['log_level'] = 0;//0:No logging, 1:Log Mysql errors, 2:1+input errors, 3:Debugging-2+output mysql errors in ajax return

if(isset($_COOKIE['name'])){
	require( 'views/full_page.php' );
}else{
	require( 'views/login.php' );	
}

?>