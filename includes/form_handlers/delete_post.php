<?php 
require '../../config/config.php';
 
if(isset($_GET['post_id']))
    $post_id = $_GET['post_id'];
 
if(isset($_POST['result'])) {
    if($_POST['result'] == 'true')
        $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
}
 

    // require '../../config/config.php';
    // // include("../classes/User.php");
    // // include("../classes/Post.php");

    // if(isset($_GET['post_id'])) {
    //     $post_id = $_GET['post_id'];
    // }

    // if(isset($_POST['result'])) {
    //     if($_POST['result']=='true') {
    //         $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
    //     }
    
    // }
?>