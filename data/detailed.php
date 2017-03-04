<?

include '../lib/auth.php';
include '../lib/dataobject.php';

$passed_film = $_GET['film'];

if ($session_method == 'GET') {	
	if (empty($passed_film)) {
		$json_status = 'film parameter missing';
		$json_output[] = array('status' => $json_status, 'status_code' => 422);
		echo json_encode($json_output);
		exit;
		
	}
	else {
		$film_query = mysqli_query($database_connect, "SELECT * FROM `films` WHERE `film_imdb` LIKE '$passed_film' LIMIT 0, 1");
		$film_exists = mysqli_num_rows($film_query);
		while($row = mysqli_fetch_array($film_query)) {
			$film_content[] = film_data_detailed($row);
	
		}
		
		if ($film_exists == 1) {
			$json_status = 'film retured with content';
			$json_output[] = array('status' => $json_status, 'status_code' => 200, 'content' => $film_content);
			echo json_encode($json_output);
			exit;
			
		}
		else {
			$json_status = 'film does not exist';
			$json_output[] = array('status' => $json_status, 'error_code' => 404);
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