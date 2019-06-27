<?php  
	ob_start(); //this turns on output buffering, saves the php data and pass all to the browser  
	session_start();

	$timezone = date_default_timezone_set('Asia/Manila');
	//displays the date
	//echo "<span style='color:red;font-weight:bold;'>Date: </span>". date('F j, Y g:i:a  ');

	$con = mysqli_connect("ffn96u87j5ogvehy.cbetxkdyhwsb.us-east-1.rds.amazonaws.com","jvtvm907r10e1vtj","xra5kndfb6ap4g90","bejhu7hym9q9z0f4");

	/*return an error*/
	if(mysqli_connect_errno()){
		echo "failed to connect: " . mysqli_connect_errno();
	}
?>