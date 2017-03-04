<?

include '../lib/auth.php';
include '../lib/dataobject.php';

if ($session_method == 'GET') {	
	$tags_user = user_tags($auth_user);
	$tags_exists = array();
	for ($i = 0; $i < count($tags_user); $i++) {
		$tag_lower = strtolower($tags_user[$i]);
		$tag_next = strtolower($tags_user[$i + 1]);		
		if (!in_array($tag_lower, $tags_exists) && !empty($tag_lower)) {
			$tag_duplicates[] = $tag_lower;
			$tag_injection .= "film_tags LIKE '%$tag_lower%' ";
				
			if (strlen($tag_next) > 0) $tag_injection .= "OR ";
			
		}
				
	}
	
	$reccomended_query = mysqli_query($database_connect, "SELECT * FROM `films` WHERE `film_imdb` NOT LIKE '' AND ($tag_injection)");
		$film_exists = mysqli_num_rows($film_query);
		while($row = mysqli_fetch_array($film_query)) {
			$film_content[] = film_data_detailed($row);
	
		}
		
		echo "SELECT * FROM `films` WHERE `film_imdb` NOT LIKE '' AND ($tag_injection)";
		
}
else {
	$json_status = $session_method . ' methods are not supported in the api';
	$json_output[] = array('status' => $json_status, 'status_code' => 405);
	echo json_encode($json_output);
	exit;
	
}

?>