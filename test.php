<?

$trending_import_url = "https://api.themoviedb.org/3/discover/movie?api_key=b8c339e24878d6766a67e14ba5ab3cbf&sort_by=popularity.desc";
$trending_import_data = json_decode(file_get_contents($trending_import_url));
$trending_title = md5($trending_import_data->results[0]->title) ;

print_r($trending_title);


$subtitle_agent = "SubDB/1.0 (Grado/1.0; http://github.com/jrhames/pyrrot-cli)"

$subtitle_header = array('http' => array('method' => "GET", 'header' => "Accept-language: en\r\n" . "User-Agent: foo=bar\r\n"));
$subtitle_context = stream_context_create($subtitle_header);
$subtitle_data = file_get_contents('http://api.thesubdb.com/?action=search&hash=edc1981d6459c6111fe36205b4aff6c2[&versions]', false, $subtitle_context);

print_r($subtitle_data);

?>