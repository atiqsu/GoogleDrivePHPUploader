<?php
if(!(php_sapi_name() == 'cli' || PHP_SAPI == 'cli')) {
		     if(isset($_GET['filepath'])) {
		     				  $filePath = $_GET['filepath'];
						  if(!file_exists($filePath)) {
						  			      exit("Specified file does not exist");
									      }
									      if(isset($_GET['mime'])) {
									      			       $mime = $_GET['mime'];
												       } else {
												       	 $mime = mime_content_type($filePath);
													 }
		     } else {
		       exit("No file path given.");
		       }
} else {
  $optionsArray = getopt("f:m::");
  if(isset($optionsArray['f'])) {
  				$filePath= $optionsArray['f'];
				if(!file_exists($filePath)) {
							    exit("Specified file does not exist");
							    }
				if(isset($optionsArray['m']) && $optionsArray['m']) {
							     $mime = $optionsArray['m'];
				} else {
				  $mime = mime_content_type($filePath);
				  }
  } else {
    exit("No file name or path given\n");
  }
}
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

$client_secret = json_decode(file_get_contents("client_secret.json"));

$client = new Google_Client();
// Get your credentials from the console
$client->setClientId($client_secret->installed->client_id);
$client->setClientSecret($client_secret->installed->client_secret);
$client->setRedirectUri($client_secret->installed->redirect_uris[0]);
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
				    file_put_contents("accesstoken.txt", $client->getAccessToken(), LOCK_EX);
}

//Insert a file
$file = new Google_DriveFile();
$file->setTitle('My Document');
$file->setDescription('A test document');
$file->setMimeType($mime);

$data = file_get_contents($filePath);

$createdFile = $service->files->insert($file, array(
      'data' => $data,
      'mimeType' => $mime,
    ));


$permission = new Google_Permission();
$permission->setValue("inmobi.com");
$permission->setType("domain");
$permission->setRole("reader");
$service->permissions->insert($createdFile['id'], $permission);
print_r($createdFile['alternateLink']."\n");
?>