<?

header("Refresh:6");
		
$database_grado_connect = mysqli_connect('localhost', 'root', 'root'); 
$database_grado_table = mysqli_select_db($database_grado_connect, "filmio");

$database_item_query = mysqli_query($database_grado_connect, "SELECT * FROM `films` WHERE `film_imdb` LIKE '' ORDER BY `film_updated` ASC LIMIT 0, 1");
$database_item_data = mysqli_fetch_assoc($database_item_query);
$database_item_id = $database_item_data['film_id'];
$database_item_title = str_replace('"', '', $database_item_data['film_title']);
$database_item_imdb = $database_item_data['film_imdb'];
$database_item_language = $database_item_data['film_plot'];

$imdb_url = "http://www.omdbapi.com?t=" . urlencode($database_item_title) . "&plot=short&r=json";
$imdb_data = json_decode(file_get_contents($imdb_url));
$imdb_title = mysqli_real_escape_string($database_grado_connect, $imdb_data->Title);
$imdb_type = $imdb_data->Type;
$imdb_runtime = (int)filter_var($imdb_data->Runtime, FILTER_SANITIZE_NUMBER_INT);
$imdb_plot = mysqli_real_escape_string($database_grado_connect, preg_replace('/\s+/', ' ',$imdb_data->Plot));
$imdb_language = str_replace(" ", "", strtolower($imdb_data->Language));
$imdb_metacritic = (int)$imdb_data->Metascore;
$imdb_score = (float)$imdb_data->imdbRating;
$imdb_poster = $imdb_data->Poster;
$imdb_released = date('Y-m-d', strtotime($imdb_data->Released));
$imdb_year = date('Y', strtotime($imdb_data->Released));
$imdb_key = $imdb_data->imdbID;
$imdb_genres = str_replace(" ", "", strtolower($imdb_data->Genre));

if (strtolower($imdb_plot) == "n/a") $imdb_plot = "";
if (strtolower($imdb_poster) == "n/a") $imdb_poster = "";

echo "<p>API Query: " . $imdb_url;
echo "<p>IMDB Key: " . $imdb_key;
echo "<p>Media Type: " . $imdb_type;
echo "<p>Langauge: " . $imdb_language;
echo "<p>Plot: " . $imdb_plot;
echo "<p>Genre: " . $imdb_genres;
echo "<p>Release: " . $imdb_released;
echo "<p>Existing ID: " . $database_item_id;

$existing_query = mysqli_query($database_grado_connect, "SELECT * FROM `films` WHERE `film_imdb` LIKE '$imdb_key' LIMIT 0, 1");
$existing_count = mysqli_num_rows($existing_query);

if (isset($imdb_data)) {
	if ($imdb_type == "movie" && $existing_count == 0 && in_array("english", explode(",", $imdb_language)) && !in_array("short", explode(",", $imdb_genres)) && !in_array("adult", explode(",", $imdb_genres)) && strtolower($imdb_genres) != "n/a" && $imdb_runtime > 60 && isset($imdb_poster)) {
		$imdb_header_url = "http://www.imdb.com/title/" . $imdb_key . "/mediaviewer/";
		$imdb_header_data = download_web_content($imdb_header_url);
		$imdb_header_meta = output_meta($imdb_header_data);
		
		foreach($imdb_header_meta as $tags) {
			if ($tags[0] == "og:image") $imdb_header = $tags[1];
			
		}
		
		$imdb_subtitles = get_subtitles ($imdb_data->Title, $imdb_year);
		$imdb_tags = $imdb_genres;
		
		echo "<p>Subtitles Word Count: " . count(explode(" ", $imdb_subtitles));
		echo "<div style='position:fixed; top:15px; right:15px;'>";
		echo "<img src='" . $imdb_header . "' width='300px' style='border-radius:4px;'><p>";
		echo "<img src='" . $imdb_poster . "' width='300px' style='border-radius:4px;'>";
		echo "</div>";
		
		$database_item_update = mysqli_query($database_grado_connect, "UPDATE `films` SET `film_release` = '$imdb_released', `film_title` = '$imdb_title', `film_runtime` = '$imdb_runtime', `film_metacritic` = '$imdb_metacritic', `film_score` = '$imdb_score', `film_poster` = '$imdb_poster', `film_header` = '$imdb_header', `film_plot` = '$imdb_plot', `film_genres` = '$imdb_genres', `film_subtitles` = '$imdb_subtitles', `film_imdb` = '$imdb_key', `film_tags` = '' WHERE `film_id` = $database_item_id;");
		if ($database_item_update) {
			echo "<p>Title: " . $database_item_title . " <p>Status: <strong style='color:green;'>updated with content</strong><p>";
			
		}
		else {
			echo "<p>Title: " . $database_item_title . " <p>Status: <strong style='color:orange;'>failed updating</strong></h2><p>Error: " . mysqli_error($database_grado_connect);
			
		}

	}
	else {
		$database_item_remove = mysqli_query($database_grado_connect, "DELETE FROM `films` WHERE `film_id` = '$database_item_id';");
		if ($database_item_remove) {
			echo "<p>Title: " . $database_item_title . " <p>Status: <strong style='color:red;'>removed</strong>";
			
		}
		else {
			echo "<p>Title: " . $database_item_title . " <p>Status: <strong style='color:orange;'>failed removing</strong></h2><p>Error: " . mysqli_error($database_grado_connect);
			
		}
		
	}
	
	echo get_import_stats();
	
}
else {
	echo "<p>Status: <strong style='color:red;'>" . $imdb_data->Error . "</strong>";
	
}

function download_web_content($url) {
	$url_image = array();
	$ch = curl_init();
	$url_curl = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_BINARYTRANSFER => true,
        CURLOPT_HEADER => true, // don't return headers
        CURLOPT_FOLLOWLOCATION => true, // follow redirects
        CURLOPT_ENCODING => "", // handle all encodings
        CURLOPT_USERAGENT => "spider", // who am i
        CURLOPT_AUTOREFERER => true, // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 60, // timeout on connect
        CURLOPT_TIMEOUT => 120, // timeout on response
        CURLOPT_MAXREDIRS => 5, // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_VERBOSE => false,
		
    );
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt_array($ch, $url_curl);
    $url_contents = curl_exec($ch);
    curl_close($ch);
	
	return $url_contents;
	
}

function output_meta($content) {
	preg_match_all('/<meta[^>]+(?:name|property)=\"([^\"]*)\"[^>]+content=\"([^\"]*)\"[^>]*>/', $content, $match_tags);
	
	//convert meta tags into readable array	
	if (isset($match_tags[2]) && count($match_tags[2])) {
        foreach ($match_tags[2] as $key => $value) {
            $meta_key = trim($match_tags[1][$key]);
            $meta_tag = trim($value);
            if ($meta_tag) $metatags[] = array($meta_key, $meta_tag);
           
        }
		
    }
	
	return $metatags;
	
}

function get_subtitles ($film, $year) {
	$subtitle_film_name = strtolower($film);
	$subtitle_film_name = reset(explode(":", $subtitle_film_name));
	$subtitle_film_name = str_replace("/", "", $subtitle_film_name);
	$subtitle_film_name = str_replace(" ", "-", $subtitle_film_name);
	$subtitle_film_year = $year;
	
	$subtitle_host_url = "http://www.english-subtitles.pro/movies/" . $subtitle_film_year . "-" . $subtitle_film_name . ".html";
	$subtitle_host_content = file_get_contents($subtitle_host_url);
	$subtitle_host_content = strip_tags($subtitle_host_content, "<a>");
	
	echo "<p><p>Subtitle URL: " . $subtitle_host_url;
	
	preg_match_all("/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU", $subtitle_host_content, $sbutitle_host_links);
		
	foreach ($sbutitle_host_links[2] as $site) {
		if (strpos($site, 'download') !== false) {
			$subtitle_download_host = 'http://www.english-subtitles.pro' . str_replace("'", "", $site);
			break;
			
		}
		
		
	}
	
	$subtitle_film_formatted = "subtitles/" . $subtitle_film_name . ".srt";	
	$subtitle_download_contents = file_put_contents($subtitle_film_formatted, fopen($subtitle_download_host, 'r'));
	$subtitle_file = file($subtitle_film_formatted);
	$subtitle_file = serialize($subtitle_file);
	preg_match_all("/([a-zA-Z]{2,}+\b)/", $subtitle_file , $subtitle_file_formatted);
	
	$subtitle_file_formatted = implode(" ", $subtitle_file_formatted[0]);
	$subtitle_file_formatted = strtolower($subtitle_file_formatted);
	
	return $subtitle_file_formatted;
	
}

function get_import_stats() {
	global $database_grado_connect;
	
	$unimported_query = mysqli_query($database_grado_connect, "SELECT * FROM `films` WHERE `film_imdb` NOT LIKE '%tt%' ORDER BY `film_updated` DESC");
	$unimported_count = mysqli_num_rows($unimported_query);
	
	$imported_query = mysqli_query($database_grado_connect, "SELECT * FROM `films` WHERE `film_imdb` LIKE '%tt%' ORDER BY `film_updated` DESC");
	$imported_count = mysqli_num_rows($imported_query);
	$imported_percent = ($imported_count / unimported_count) * 100;
	
	return "<p><p><p>Imported <strong>" . $imported_count . "</strong> of <strong>" . $unimported_count . " " . round($imported_percent, 3) . "%</strong>";
			
}

/*
ini_set('memory_limit', '-1');

$database_grado_connect = mysqli_connect('localhost', 'root', 'root'); 
$database_grado_table = mysqli_select_db($database_grado_connect, "filmio");

$file_content = file_get_contents("aka-titles.list");
$file_content = preg_split('/\r\n|\r|\n/', $file_content);
foreach ($file_content as $value) {
	preg_match("/^[^\(]+|([0-9]{4})/", $value, $value);
	
	$file_title = preg_replace('/\s+/', ' ',$value[0]);
	if (strlen($file_title) > 2) {
		$file_input = mysqli_query($database_grado_connect, "INSERT INTO `films` (`film_id`, `film_updated`, `film_title`, `film_release`, `film_poster` , `film_header` , `film_plot`, `film_subtitles`, `film_imdb`, `film_tags`) VALUES (NULL, CURRENT_TIMESTAMP, '$file_title', '2017-01-22', '', '', '', '', '', '')");
		
	}

}
*/

?>