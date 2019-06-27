<?php  
	/*Declaring variables to prevent errors*/
	$fname = "";
	$lname = "";
	$em = "";
	$em2 = "";
	$password = "";
	$password2 = "";
	$date=""; // sign up date

	//holds and restart the session if pass through
	$error_array = []; //holds error message
	

	//if button has been press
	if(isset($_POST['register_button'])){

		//Register From Values
		$fname = strip_tags($_POST['reg_fname']);//avoid the html or code injection
		$fname = str_replace(' ','', $fname); //remove the spaces
		$fname = ucfirst(strtolower($fname)); //uppercase first letter
		$_SESSION['reg_fname']= $fname;

		$lname = strip_tags($_POST['reg_lname']);//avoid the html or code injection
		$lname = str_replace(' ','', $lname); //remove the spaces
		$lname = ucfirst(strtolower($lname)); //uppercase first letter
		$_SESSION['reg_lname']= $lname;

		$em = strip_tags($_POST['reg_email']);//avoid the html or code injection
		$em = str_replace(' ','', $em); //remove the spaces
		$em = ucfirst(strtolower($em)); //uppercase first letter
		$_SESSION['reg_email']= $em;


		$em2 = strip_tags($_POST['reg_email2']);//avoid the html or code injection
		$em2 = str_replace(' ','', $em2); //remove the spaces
		$em2 = ucfirst(strtolower($em2)); //uppercase first letter2
		$_SESSION['reg_email2']= $em2;

		$password = strip_tags($_POST['reg_password']);//avoid the html or code injection
		$password2 = strip_tags($_POST['reg_password2']);//avoid the html or code injection
		$date = date("Y-a-d"); //gets the current date

		if($em == $em2) {
			//check if email is valid format
			if(filter_var($em, FILTER_VALIDATE_EMAIL)) {
				$em = filter_var($em, FILTER_VALIDATE_EMAIL);

				//check if email already exists
				$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

				//count the number of rows returned
				$num_rows = mysqli_num_rows($e_check);

				if($num_rows > 0){
					array_push($error_array, "Email already in use<br>");
				}
			} else {
				array_push($error_array, "Invalid Format<br>");
			}
		} else {
			array_push($error_array, "emails don't match<br>");
		}
		//check if fname length higher than 25 or less than 2
		if(strlen($fname) > 25 || strlen($fname) < 2){
			array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
		}

		if(strlen($lname) > 25 || strlen($lname) < 2){
			array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
		}

		if($password != $password2){
			array_push($error_array, "Your passwords do not match<br>");
		} else {
			//checks to see if the value on password
			if(preg_match('/[^A-Za-z0-9]/', $password)) {
				array_push($error_array, "Your password can only contain english characters or numbers<br>");
			}
		}

		if(strlen($password)>30 || strlen($password)<5){
			array_push($error_array, "your password must be between 5 and 30 characters<br>");
		}

		if(empty($error_array)){
			$password = md5($password); // encrypt password before sending to db

			//Generate username by concatenating firstname and last name
			$username = strtolower($fname . "_" .$lname);
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");

			$i = 0;
			//if useranme exists add number to username
			while(mysqli_num_rows($check_username_query) != 0){
				$i++; //add 1 to i
				$username = $username . "_" . $i;
				$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");
			}

			//profile picture assignment
			$rand = rand(1,2); //random number between 1 and 2

			if($rand==1){
				$profile_pic = "assets/images/profile_pics/defaults/head_deep_blue.png";
			}else if($rand==2){
				$profile_pic = "assets/images/profile_pics/defaults/head_emerald.png";
			}
			$query = mysqli_query($con, "INSERT INTO users VALUES ('','$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
			array_push($error_array, "<span style='color: #14C800;'> You're all set! Goahead and login! <span> <br>");

			//Clear session variables 
			$_SESSION['reg_fname'] = "";
			$_SESSION['reg_lname'] = "";
			$_SESSION['reg_email'] = "";
			$_SESSION['reg_email2'] = "";

		}
		
	}
?>