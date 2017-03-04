<?

//include '../grado/api/lib/auth.php';
//include '../grado/api/lib/keywords.php';

$subtitle_film_name = str_replace(" ", "-", strtolower($_GET['film']));
$subtitle_film_year = $_GET['year'];

$subtitle_host_url = "http://www.english-subtitles.pro/movies/" . $subtitle_film_year . "-" . $subtitle_film_name . ".html";
$subtitle_host_content = file_get_contents($subtitle_host_url);
$subtitle_host_content = strip_tags($subtitle_host_content, "<a>");

preg_match_all("/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU", $subtitle_host_content, $sbutitle_host_links);
	
foreach ($sbutitle_host_links[2] as $site) {
	if (strpos($site, 'download') !== false) {
		$subtitle_download_host = 'http://www.english-subtitles.pro' . str_replace("'", "", $site);
		break;
		
	}
	
	
}

$subtitle_download_contents = file_put_contents("tempsubs.srt", fopen($subtitle_download_host, 'r'));
$subtitle_file = file('tempsubs.srt');
$subtitle_file = serialize($subtitle_file);
preg_match_all("/([a-zA-Z]{2,}+\b)/", $subtitle_file , $subtitle_file_formatted);

$subtitle_file_formatted = implode(" ", $subtitle_file_formatted[0]);
$subtitle_file_formatted = strtolower($subtitle_file_formatted);

print_r($subtitle_file_formatted);

/*
$tags_data = tags_produce($subtitle_file_formatted);
	
$json_status = "loaded data";
$json_output[] = array('status' => $json_status, 'error_code' => 200, 'tags' => $tags_data);
echo json_encode($json_output);
exit;
*/

?>