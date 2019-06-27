<?php  
	if (isset($_POST['login_button'])) {
		$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //Remove all illegal characters from email

		$_SESSION['log_email'] = $email; //store email into session variable, purpose is to use if this session exis display in value html
		$password = md5($_POST['log_password']);

		$check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password = '$password'");
		$check_login_query = mysqli_num_rows($check_database_query);

		if($check_login_query==1){
			//fetch array returns numeric keys and associative strings(columns name) but both are index
			$row = mysqli_fetch_array($check_database_query);
			$username = $row['username'];

			//reopening a close account. if log in user_close is no if not login user close is yes
			$user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE email = '$email' AND user_closed = 'yes'");
			if(mysqli_num_rows($user_closed_query)==1){
				$reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'");
			}

			//if we navigate we can determine if log in  or not 
			$_SESSION['username']=$username;
			header("location: index.php");
			exit;
		} else {
			array_push($error_array, "Email or Password was incorrect<br>");
		}
	}


?>