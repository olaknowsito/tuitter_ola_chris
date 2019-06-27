<?php 
	include("includes/header.php");
	//came from the index
	// include("includes/classes/User.php");
	// include("includes/classes/Post.php");
	$message_obj = new Message($con, $userLoggedIn);


	

	if(isset($_GET['profile_username'])){
		$username = $_GET['profile_username'];
		$user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username = '$username'");
		$user_array = mysqli_fetch_array($user_details_query);

		//count the comma
		$num_friends = (substr_count($user_array['friend_array'], ","))-1;
	}

	if(isset($_POST['remove_friend'])){
		$user = new User($con, $userLoggedIn);
		$user->removeFriend($username);
	}

	if(isset($_POST['add_friend'])){
		$user = new User($con, $userLoggedIn);
		$user->sendRequest($username);
	}

	if(isset($_POST['respond_request'])){
		header("Location: requests.php");
	}

	if(isset($_POST['post_message'])){
		if(isset($_POST['message_body'])){
			$body = mysqli_real_escape_string($con, $_POST['message_body']);
			$date = date("Y-m-d H:i:s");
			$message_obj->sendMessage($username, $body, $date);
		}
		$link = '#myTab a[href="#messages_div"]';
		echo "<script>
				$(function() {
					$('" . $link . "').tab('show');
				})
			</script>";
	}
?>

	<style type="text/css">
		.wrapper {
			margin-left: 0px;
			padding-left: 0px;
		}
	</style>

	<div class="profile_left">
		<img src="<?php echo $user_array['profile_pic']?>" alt="">

		<div class="profile_info">
			<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
			<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
			<p><?php echo "Friends: " . $num_friends; ?></p>
		</div>

			<form action="<?php echo $username; ?>" method="POST">
				<?php 
					$profile_user_obj = new User($con, $username);
					if($profile_user_obj->isClosed()){
						header("Location: user_closed.php");
					}

					$logged_in_user_object = new User($con, $userLoggedIn);
					//there will be times username is not equal to userloggin
					if($userLoggedIn != $username) {
						if($logged_in_user_object->isFriend($username)){
							echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br><br>
                				<a style="margin-left:5px;" class="btn btn-success sendMessageLink" href="messages.php?u=' . $username . '">Send message</a><br>';
						} else if ($logged_in_user_object->didReceiveRequest($username)) {
							echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
						} else if ($logged_in_user_object->didSendRequest($username)) {
							echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
						} else {
							echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
						}
					}

				?>
			</form>
			<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something">

			<?php 
				if($userLoggedIn != $username) {
					echo "<div class='profile_info_bottom'>";
						echo $logged_in_user_object->getMutualFriends($username) . " Mutual Friends";
					echo "</div>";
				}
			?>
	</div>
	

	<div class="profile_main_column column">

		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="newsfeed-tab" data-toggle="tab" href="#newsfeed_div" role="tab" aria-controls="newsfeed_div" aria-selected="true">Newsfeed</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" id="profile-tab" data-toggle="tab" href="#about_div" role="tab" aria-controls="about_div" aria-selected="false">About</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" id="messages-tab" data-toggle="tab" href="#messages_div" role="tab" aria-controls="messages_div" aria-selected="false">Messages</a>
			</li>
		</ul>

		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="newsfeed_div" role="tabpanel" aria-labelledby="newsfeed-tab">
				<div class="posts_area"></div>
				<img id="loading" src="assets/images/icons/loading.gif">
			</div>

			<div class="tab-pane fade" id="about_div" role="tabpanel" aria-labelledby="profile-tab">...</div>
			<div class="tab-pane fade" id="messages_div" role="tabpanel" aria-labelledby="messages-tab">
			<?php 
					echo "<h4> You and <a href='" . $username . "'>" . $profile_user_obj->getFirstAndLastname() . "</a></h4><hr><br>";
					echo "<div class='loaded_messages' id='scroll_messages'>";
						echo $message_obj->getMessages($username);
					echo "</div>";
					echo "<h4>New Message</h4>";
				?>

				<div class="message_post">
					<form action="" method="POST">
						<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
						<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
					</form>
				</div>
				
				<script>
					var div = document.getElementById("scroll_messages");
					if(div != null) {
						div.scrollTop = div.scrollHeight;
					}
				</script>
			</div>
		</div>

	</div>



	<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Post Something</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<a>This will appear on the user's profile page and also their newsfeed for your friends to see!</a>
				<form class="profile_post" action="profile.php" method="POST">
					<div class="form-group">
						<textarea class="form-control" name="post_body"></textarea>
						<input type="hidden" name="user_from" value="<?php echo $userLoggedIn?>">
						<input type="hidden" name="user_to" value="<?php echo $username?>">
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
			</div>
			</div>
		</div>
	</div>

	<script>
	$(function(){
	
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
		var profileUsername = '<?php echo $username; ?>';
		var inProgress = false;
	
		loadPosts(); //Load first posts
		// when the user scrolls, we execute some code
		$(window).scroll(function() {
			var bottomElement = $(".status_post").last();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();
	
			// isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
			if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
				loadPosts();
			}
		});
	
		function loadPosts() {
			// if we are already loading  posts, then break out of the code, since we don't want to load the same posts over and over again. Only load if it's false.
			if(inProgress) { //If it is already in the process of loading some posts, just return
				return;
			}
			
			inProgress = true;
			$('#loading').show();
	
			var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
	
			$.ajax({
				url: "includes/handlers/ajax_load_profile_posts.php",
				type: "POST",
				// data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
				data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
				cache:false,
	
				success: function(response) {
					$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
					$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
					$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage
	
					$('#loading').hide();
					$(".posts_area").append(response);
	
					inProgress = false;
				}
			});
		}
	
		//Check if the element is in view
		function isElementInView (el) {
				if(el == null) {
					return;
				}
	
			var rect = el.getBoundingClientRect();
	
			return (
				rect.top >= 0 &&
				rect.left >= 0 &&
				rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
				rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
			);
		}
	});
 
   </script>


	</div>
</body>
</html>