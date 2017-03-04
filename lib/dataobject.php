<?

function film_data($data) {
	$film_id = (int)$data['film_id'];
	$film_title =(string)htmlspecialchars($data['film_title']);
	$film_release = $data['film_release'];
	$film_genres = explode(",", $data['film_genres']);
	$film_metascore = (int)$data['film_metacritic'];
	$film_imdb = (string)$data['film_imdb'];
	$film_poster = (string)htmlspecialchars($data['film_poster']);

	return array("id" => $film_id, "key" => $film_imdb, "title" => $film_title, "release" => $film_release, "genres" => $film_genres, "metacritic" => $film_metascore, "poster" => $film_poster);
		
}

function film_data_detailed($data) {
	$film_id = (int)$data['film_id'];
	$film_title =(string)htmlspecialchars($data['film_title']);
	$film_plot = (string)htmlspecialchars($data['film_plot']);
	$film_tags = explode(",", $data['film_tags']);
	$film_release = $data['film_release'];
	$film_genres = explode(",", $data['film_genres']);
	$film_metascore = (int)$data['film_metacritic'];
	$film_imdb = (string)$data['film_imdb'];
	$film_poster = (string)htmlspecialchars($data['film_poster']);
	$film_header = (string)htmlspecialchars($data['film_header']);
	$film_related = films_related($film_tags);
	$film_liked = film_liked($film_imdb);	
	
	return array("id" => $film_id, "key" => $film_imdb, "title" => $film_title, "plot" => $film_plot, "release" => $film_release, "genres" => $film_genres, "metacritic" => $film_metascore, "poster" => $film_poster, "header" => $film_header, "tags" => $film_tags, "related" => $film_related, "liked" => $film_liked);
	
}

function film_affiliate($data) {
	
}

function films_related($tags) {
	//$related_injection
	foreach ($array as $key => $value) {
		
	}
	return array();
	
}

function film_liked($film) {
	global $auth_user;
	global $database_connect;
	
	$liked_query = mysqli_query($database_connect, "SELECT `likes_id` FROM `likes` WHERE `likes_user` LIKE '$auth_user' AND `likes_film` LIKE '$film' LIMIT 0, 1");
	$liked_bool = mysqli_num_rows($liked_query);
	
	return $liked_bool;
	
}

function user_tags($user) {
	global $database_connect;
	
	$tags_query = mysqli_query($database_connect, "SELECT `likes_id`, `likes_timestamp`, `film_imdb`, `film_tags` FROM `likes` LEFT JOIN films on likes.likes_film LIKE films.film_imdb WHERE `likes_user` LIKE '$user'");
	while($row = mysqli_fetch_array($tags_query)) {
		foreach (explode(",", $row['film_tags']) as $tag) {
			if (!in_array($tag, $tags_output)) $tags_output[] = $tag;
			
		}
		
	}
	
	if (count($tags_output) == 0) $tags_output = array();
	
	return $tags_output;
	
}

function user_likes($user) {
	global $database_connect;
	global $passed_pagenation;
	global $passed_limit;
	
	$favorites_query = mysqli_query($database_connect, "SELECT * FROM `likes` LEFT JOIN films on likes.likes_film LIKE films.film_imdb WHERE `likes_user` LIKE '$user' ORDER BY likes_id DESC LIMIT $passed_pagenation, $passed_limit");
	$favorites_count = mysqli_num_rows($favorites_query);
	while($row = mysqli_fetch_array($favorites_query)) {
		$favorites_output[] = film_data($row);
		
	}
	
	if ($favorites_output == 0) $favorites_output = array();
	
	return $favorites_output;
	
}
	

/*
	film_idPrimary	int(20)			No	None		AUTO_INCREMENT	 Change Change	 Drop Drop	
More
	2	film_updated	timestamp		on update CURRENT_TIMESTAMP	No	CURRENT_TIMESTAMP		ON UPDATE CURRENT_TIMESTAMP	 Change Change	 Drop Drop	
More
	3	film_title	varchar(100)	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
	4	film_runtime	int(4)			No	None			 Change Change	 Drop Drop	
More
	5	film_metacritic	int(5)			No	None			 Change Change	 Drop Drop	
More
	6	film_score	float			No	None			 Change Change	 Drop Drop	
More
	7	film_release	date			No	None			 Change Change	 Drop Drop	
More
	8	film_poster	varchar(500)	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
	9	film_header	varchar(500)	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
	10	film_plot	varchar(500)	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
	11	film_genres	varchar(60)	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
	12	film_subtitles	longtext	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
	13	film_imdb	varchar(15)	utf8_general_ci		No	None			 Change Change	 Drop Drop	
More
14	film_tags
*/

?>