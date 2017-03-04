<?

include '../lib/auth.php';
include '../lib/dataobject.php';

$passed_films = $_GET['films'];
$passed_limit = $_GET['limit'];
$passed_pagenation = $_GET['pangnation'];

if (empty($_GET['limit'])) $passed_limit = 40;
if (empty($_GET['pangnation'])) $passed_pagenation = 0;

$passed_pagenation = $data_passed_pagenation * $data_passed_limit;

if ($session_method == 'GET') {	
	$json_likes = user_likes($auth_user);
	$json_status = count($json_likes) . ' items returned';
	$json_output[] = array('status' => $json_status, 'status_code' => 200, 'items' => $json_likes);
	echo json_encode($json_output);
	exit;
	
}
elseif ($session_method == 'POST') {
	if (empty($passed_films)) {
		$json_status = 'films parameter missing';
		$json_output[] = array('status' => $json_status, 'status_code' => 422);
		echo json_encode($json_output);
		exit;
		
	}
	else {
		foreach (explode(",", $passed_films) as $film) {
			$like_exists_query = mysqli_query($database_connect, "SELECT * FROM `likes` WHERE `likes_film` LIKE '$film' AND `likes_user` LIKE '$auth_user' LIMIT 0, 1");
			$like_exists_count = mysqli_num_rows($like_exists_query);
			if ($like_exists_count == 0) {
				$like_post = mysqli_query($database_connect, "INSERT INTO `likes` (`likes_id`, `likes_timestamp`, `likes_film`, `likes_user`) VALUES (NULL, CURRENT_TIMESTAMP, '$film', '$auth_user');");
				if ($like_post) {
					$likes_added[] = $film;
					
				}
			
			}
			
		}
		
		$json_status = count($likes_added) . ' items added to likes';
		$json_output[] = array('status' => $json_status, 'status_code' => 200);
		echo json_encode($json_output);
		exit;
		
	}
	
}
elseif ($session_method == 'DELETE') {
	if (empty($passed_films)) {
		$json_status = 'film parameter missing';
		$json_output[] = array('status' => $json_status, 'status_code' => 422);
		echo json_encode($json_output);
		exit;
		
	}
	else {
		foreach (explode(",", $passed_films) as $film) {
			$like_exists_query = mysqli_query($database_connect, "SELECT * FROM `likes` WHERE `likes_film` LIKE '$film' AND `likes_user` LIKE '$auth_user' LIMIT 0, 1");
			$like_exists_count = mysqli_num_rows($like_exists_query);
			$like_exists_data = mysqli_fetch_assoc($like_exists_query);
			$like_exists_identifyer = $like_exists_data['likes_id'];
					
			if ($like_exists_count == 1) {
				$like_delete = mysqli_query($database_connect, "DELETE FROM `likes` WHERE `likes_id` = '$like_exists_identifyer';");
				if ($like_delete) {
					$likes_removed[] = $film;
					
				}
				
			}
			
		}
		
		$json_status = count($likes_removed) . ' items removed from likes';
		$json_output[] = array('status' => $json_status, 'status_code' => 200);
		echo json_encode($json_output);
		exit;
		
	}
	
}
else {
	$json_status = $session_method . ' methods are not supported in the api';
	$json_output[] = array('status' => $json_status, 'status_code' => 405);
	echo json_encode($json_output);
	exit;
	
}

?>