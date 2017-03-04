<?

$url = 'https://api.twitter.com/oauth/request_token';

//setting OAuth parameters
$Oauth = Array();
$Oauth['oauth_callback'] = 'http://www.soytumascota.com/twitter/user.php';
$Oauth['oauth_consumer_key'] = "V81NJel5QM91KerjWZ2Zff46p";
$Oauth['oauth_nonce'] = md5( $Oauth['oauth_callback'] . "V81NJel5QM91KerjWZ2Zff46p" . time() );
$Oauth['oauth_signature_method'] = 'HMAC_SHA1';
$Oauth['oauth_timestamp'] = (string)time();
$Oauth['oauth_version'] = '1.0';

$Oauth['oauth_signature'] = calculateSignature( 'POST', $url, $Oauth );
$authorization = getAuthorizationHeader( $Oauth ); 
ksort( $Oauth );

//setting and sending request using cURL
$curl_session = curl_init( $url );
curl_setopt( $curl_session, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $curl_session, CURLOPT_POST, true );
curl_setopt( $curl_session, CURLINFO_HEADER_OUT, true );
curl_setopt( $curl_session, CURLOPT_HTTPHEADER, Array( 'Authorization: ' . $authorization ) );

$result = curl_exec( $curl_session );

?>