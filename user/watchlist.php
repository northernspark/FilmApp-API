<?

include '../lib/auth.php';
include '../lib/dataobject.php';

$passed_film = $_GET['film'];
$passed_limit = $_GET['limit'];
$passed_pagenation = $_GET['pangnation'];

if (empty($_GET['limit'])) $passed_limit = 40;
if (empty($_GET['pangnation'])) $passed_pagenation = 0;

$passed_pagenation = $data_passed_pagenation * $data_passed_limit;

if ($session_method == 'GET') {	
	$watchlist_query = mysqli_query($database_connect, "SELECT * FROM `watchlist` LEFT JOIN films on watchlist.watchlist_film LIKE films.film_imdb WHERE `watchlist_user` LIKE '$auth_user' ORDER BY watchlist_id DESC LIMIT $passed_pagenation, $passed_limit");
	$watchlist_count = mysqli_num_rows($watchlist_query);
	while($row = mysqli_fetch_array($watchlist_query)) {
		$watchlist_output[] = film_data($row);
		
	}
	
	if ($watchlist_output == 0) $watchlist_output = array();
	
	$json_status = count($watchlist_output) . ' items returned';
	$json_output[] = array('status' => $json_status, 'status_code' => 200, 'items' => $watchlist_output);
	echo json_encode($json_output);
	exit;
	
}
elseif ($session_method == 'POST') {
	if (empty($passed_film)) {
		$json_status = 'film parameter missing';
		$json_output[] = array('status' => $json_status, 'status_code' => 422);
		echo json_encode($json_output);
		exit;
		
	}
	else {
		$watchlist_exists_query = mysqli_query($database_connect, "SELECT * FROM `watchlist` WHERE `watchlist_film` LIKE '$passed_film' AND `watchlist_user` LIKE '$auth_user' LIMIT 0, 1");
		$watchlist_exists_count = mysqli_num_rows($watchlist_exists_query);
		if ($watchlist_exists_count == 0) {
			$watchlist_post = mysqli_query($database_connect, "INSERT INTO `watchlist` (`watchlist_id`, `watchlist_timestamp`, `watchlist_film`, `watchlist_user`) VALUES (NULL, CURRENT_TIMESTAMP, '$passed_film', '$auth_user');");
			if ($watchlist_post) {
				$json_status = 'added to watchlist';
				$json_output[] = array('status' => $json_status, 'status_code' => 200);
				echo json_encode($json_output);
				exit;
				
			}
			else {
				$json_status = 'an uknown error occured ' . mysql_error();
				$json_output[] = array('status' => $json_status, 'status_code' => 400);
				echo json_encode($json_output);
				exit;
				
			}
			
		}
		else {
			$json_status = 'already added to watchlist';
			$json_output[] = array('status' => $json_status, 'status_code' => 200);
			echo json_encode($json_output);
			exit;
			
		}
		
	}
	
}
elseif ($session_method == 'DELETE') {
	if (empty($passed_film)) {
		$json_status = 'film parameter missing';
		$json_output[] = array('status' => $json_status, 'status_code' => 422);
		echo json_encode($json_output);
		exit;
		
	}
	else {
		$watchlist_exists_query = mysqli_query($database_connect, "SELECT * FROM `watchlist` WHERE `watchlist_film` LIKE '$passed_film' AND `watchlist_user` LIKE '$auth_user' LIMIT 0, 1");
		$watchlist_exists_count = mysqli_num_rows($watchlist_exists_query);
		$watchlist_exists_data = mysqli_fetch_assoc($watchlist_exists_query);
		$watchlist_exists_identifyer = $watchlist_exists_data['watchlist_id'];
		if ($watchlist_exists_count == 1) {
			$watchlist_delete = mysqli_query($database_connect, "DELETE FROM `watchlist` WHERE `watchlist_id` = '$watchlist_exists_identifyer';");
			if ($watchlist_delete) {
				$json_status = 'removed from watchlist';
				$json_output[] = array('status' => $json_status, 'status_code' => 200);
				echo json_encode($json_output);
				exit;
				
			}
			else {
				$json_status = 'an uknown error occured ' . mysql_error();
				$json_output[] = array('status' => $json_status, 'status_code' => 400);
				echo json_encode($json_output);
				exit;
				
			}
			
		}
		else {
			$json_status = 'does not exist in watchlist';
			$json_output[] = array('status' => $json_status, 'status_code' => 200);
			echo json_encode($json_output);
			exit;
			
		}
		
	}
	
}
else {
	$json_status = $session_method . ' methods are not supported in the api';
	$json_output[] = array('status' => $json_status, 'status_code' => 405);
	echo json_encode($json_output);
	exit;
	
}

?>