<?php 
	require 'includes/header.php';
	// include("includes/classes/User.php");
	// include("includes/classes/Post.php");
	
// if the button is being pressed
	if(isset($_POST['post'])){

		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];
		$errorMessage = "";

		if($imageName != "") {
			$targetDir = "assets/images/posts/";
			$imageName = $targetDir . uniqid() . basename($imageName);
			//get the infromation from the file
			$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

			if($_FILES['fileToUpload']['size'] > 1000000) {
				$errorMessage = "Sorry Your file is to large";
				$uploadOk = 0;
			}

			if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg"){
				$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
				$uploadOk = 0;	
			}

			if($uploadOk) {
				if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)){
					//image uploaded okay
				} else {
					//image didnt upload
					$uploadOk = 0;
				}
			}

			if($uploadOk) {
				// creating new instances of class post
				$post = new Post($con, $userLoggedIn);
				// 2 parameters is the format for the submit post, none because your are on your own profile or index.php
				$post->submitPost($_POST['post_text'], 'none', $imageName);
				header("Location: index.php"); 
			} else {
				echo "<div style='text-align:center;' class='alert alert-danger'>
						$errorMessage
				
					</div>";
			}
		}
		
		
	}
?>

	<div class="user_details column">
		<a href="<?php echo $userLoggedIn; ?>">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn; ?>">
			<?php 
			echo $user['first_name'] . " " . $user['last_name'];

			 ?>
			</a>
			<br>
			<?php echo "Posts: " . $user['num_posts']. "<br>"; 
			echo "Likes: " . $user['num_likes'];

			?>
		</div>

	</div>

	

	<div class="main_column column">
		<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="fileToUpload" id="fileToUpload">
			<textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
			<input type="submit" name="post" id="post_button" value="Post">
			<hr>

		</form>

			<?php  
				// because of the format of construct on user class ?
				// $user_obj = new User($con, $userLoggedIn);
				// echo $user_obj->getFirstAndLastName();

				// $post = new Post($con, $userLoggedIn);
				// $post->loadPostsFriends();

			?>
			<div class="posts_area"></div>
			<img id="loading" src="assets/images/icons/loading.gif">

	</div>

	<div class="user_details column">
		<h4>Popular</h4><br>
		<div class="trends">
			<?php 
				$query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

				foreach($query as $row) {
					$word = $row['title'];
					$word_dot = strlen($word) >= 14 ? "..." : "";

					$trimmed_word = str_split($word, 14);
					$trimmed_word = $trimmed_word[0];

					echo "<div style='padding: 1px'><h1 class='trending'>";
					echo $trimmed_word . $word_dot;
					echo "</h1><br><div>";

				}
			?>
		</div>
	</div>

	<script>
	$(function(){
	
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
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
				url: "includes/handlers/ajax_load_posts.php",
				type: "POST",
				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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

		<!-- <script>
			var userLoggedIn = '<?php// echo $userLoggedIn; ?>';
			//this happen when the page loaded
			$(document).ready(function() {
				$('#loading').show();

				//Original ajax request for loading first posts 
				$.ajax({
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=1&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(data) {
						//returned with post so dont show the loading sign anymore
						$('#loading').hide();
											//this information, put inside post _Area
						$('.posts_area').html(data);
					}
				});
				//when scrolling this part will be detected wether they are on bottom of the page or not
				$(window).scroll(function() {
					// the height is equal to whatever the height of that div is which contains all the post
					var height = $('.posts_area').height(); //Div containing posts
					// it means scroll top variable contain the top of the page about where you are loaded. so the moment the top of the page is right  here in this window sroll top will be here
					var scroll_top = $(this).scrollTop();
					var page = $('.posts_area').find('.nextPage').val();
					var noMorePosts = $('.posts_area').find('.noMorePosts').val();

					if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
						$('#loading').show();

						var ajaxReq = $.ajax({
							url: "includes/handlers/ajax_load_posts.php",
							type: "POST",
							data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
							cache:false,

							success: function(response) {
								$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
								$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
								// hide the loading
								$('#loading').hide();
								$('.posts_area').append(response);
							}
						});

					} //End if 

					return false;

				}); //End (window).scroll(function())
			});
			
		</script> -->

	</div> 
	<!-- end of wrapper -->
</body>
</html>