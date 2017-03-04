<?

include '../lib/auth.php';
include '../lib/dataobject.php';

$passed_limit = $_GET['limit'];
$passed_pagenation = $_GET['pangnation'];

if (empty($_GET['limit'])) $passed_limit = 40;
if (empty($_GET['pangnation'])) $passed_pagenation = 0;

$passed_pagenation = $data_passed_pagenation * $data_passed_limit;

if ($session_method == 'GET') {		
	$trending_expiry =	date('Y-m-d H:i:s' ,strtotime("-10 days"));	
	$trending_query = mysqli_query($database_connect, "SELECT *, COUNT(*) AS likes_count FROM likes LEFT JOIN films on likes.likes_film LIKE films.film_imdb WHERE likes_timestamp > '$trending_expiry' GROUP BY likes_film HAVING likes_count > 1 ORDER BY likes_count DESC, likes_timestamp DESC LIMIT $passed_pagenation, $passed_limit");
	$trending_count = mysqli_num_rows($trending_query);
	while($row = mysqli_fetch_array($trending_query)) {
		$trending_content[] = film_data($row);

	}
	
	if (count($trending_content) > 0) $section_content[] = array("title" => "Trending", "key" => "trending", "films" => $trending_content);
	
	$new_date = date('Y-m-d', strtotime("-625 days"));
	$new_query = mysqli_query($database_connect, "SELECT * FROM `films` WHERE `film_release` > '$new_date' AND `film_imdb` NOT LIKE '' ORDER BY `film_release` DESC, `film_metacritic` DESC LIMIT $passed_pagenation, $passed_limit");
	$new_count = mysqli_num_rows($new_query);
	while($row = mysqli_fetch_array($new_query)) {
		$new_content[] = film_data($row);

	}
	
	if (count($new_content) > 0) $section_content[] = array("title" => "New Releases", "key" => "new_releases", "films" => $new_content);
	
	$json_status = 'sections returned';
	$json_output[] = array('status' => $json_status, 'status_code' => 200, 'content' => $section_content);
	echo json_encode($json_output);
	exit;
	
	
}
else {
	$json_status = $passed_method . ' methods are not supported in the api';
	$json_output[] = array('status' => $json_status, 'status_code' => 405);
	echo json_encode($json_output);
	exit;
	
}

?>