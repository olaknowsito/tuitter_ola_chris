<?php  
	ob_start(); //this turns on output buffering, saves the php data and pass all to the browser  
	session_start();

	$timezone = date_default_timezone_set('Asia/Manila');
	//displays the date
	//echo "<span style='color:red;font-weight:bold;'>Date: </span>". date('F j, Y g:i:a  ');

	$con = mysqli_connect("localhost","root","","social");

	/*return an error*/
	if(mysqli_connect_errno()){
		echo "failed to connect: " . mysqli_connect_errno();
	}
?>