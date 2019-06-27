<?php 
    include("includes/header.php");
    if(isset($_POST['cancel'])) {
        header("Location: settings.php");
    }

    if(isset($_POST['close_account'])) {
        $close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
        session_destroy();
        header("Location: register.php");
    }
?>

<div class="main_column column">

    <h4>Close Account</h4>
    Are you sure you want to close your account? <br><br>
    Closing your account will hide your activity and all your activity from other users. <br><br>
    You can re-open your account at anytime by simply logging in. <br><br>

    <form action="close_account.php" method="POST">
        <input type="submit" class="btn btn-danger" name="close_account" id="close_account" value="Yes! Close it!">
        <input type="submit" class="btn btn-secondary" name="cancel" id="update_details" value="No way!">
    </form>

</div>