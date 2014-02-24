<?php
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

$client = new Google_Client();
// Get your credentials from the console
$client->setClientId('470041835120-3s3ldb989c8i1ude06nhs949al6u9kj7.apps.googleusercontent.com');
$client->setClientSecret('mUvcsJoXI1fdm8jcdGXqhRDd');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setScopes(array('https://www.googleapis.com/auth/drive'));

$service = new Google_DriveService($client);


// Exchange authorization code for access token
if(file_exists("accesstoken.txt")) {
$accessToken = file_get_contents("accesstoken.txt");
} else {
$authUrl = $client->createAuthUrl();

//Request authorization
print "Please visit:\n$authUrl\n\n";
print "Please enter the auth code:\n";
$authCode = trim(fgets(STDIN));

	    $accessToken = $client->authenticate($authCode);
	    file_put_contents("accesstoken.txt", $accessToken, LOCK_EX);
}
$client->setAccessToken($accessToken);
if($client->isAccessTokenExpired()) {
				    echo "Access token expired";
				    $decodedAccessToken = json_decode($client->getAccessToken());
				    $client->refreshToken($decodedAccessToken->refresh_token);
}

//Insert a file
$file = new Google_DriveFile();
$file->setTitle('PPT Test');
$file->setDescription('A test document');
$file->setMimeType("application/vnd.openxmlformats-officedocument.presentationml.presentation");

$data = file_get_contents("ppt.pptx");

$createdFile = $service->files->insert($file, array(
      'data' => $data,
      'mimeType' => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
    ));
print_r($createdFile['alternateLink']."\n");
?>