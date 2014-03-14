<?php
/*
 * Show php errors
 * and set memory limit to infinity to allow 
 * large file uploads.
 */
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');

require('mime.php');
if(!(php_sapi_name() == 'cli' || PHP_SAPI == 'cli')) {
	if(isset($_GET['filepath'])) {
		$filePath = $_GET['filepath'];
		if(isset($_GET['title'])){
			$docTitle = $_GET['title'];
		} else {
			$docTitle = "Untitled Document";
		}
		if(!file_exists($filePath)) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
		}
		if(isset($_GET['mime'])) {
			$mime = $_GET['mime'];
		} else {
			$mime = get_mime($filePath);
		}
	} else {
        header('HTTP/1.1 400 Bad Request', true, 400);
		exit;
	}
} else {
	$optionsArray = getopt("f:m:t:");

	if(isset($optionsArray['f'])) {
		if(isset($optionsArray['t'])){
			$docTitle = $optionsArray['t'];
		} else {
			$docTitle = "Untitled Document";
		}
		$filePath= $optionsArray['f'];
		if(!file_exists($filePath)) {
			exit("Specified file does not exist");
		}
		if(isset($optionsArray['m']) && $optionsArray['m']) {
			$mime = $optionsArray['m'];
		} else {
			$mime = get_mime($filePath);
		}
	} else {
		exit("No file name or path given\n");
	}
}

/*
 * Using dba for file based db.
 */
$fdb = dba_open("./cache.db", "wl");
if($fdb) {
    if($fileData = dba_fetch($filePath,$fdb)) {
        print_r($fileData);
        exit;
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
	$decodedAccessToken = json_decode($client->getAccessToken());
	$client->refreshToken($decodedAccessToken->refresh_token);
	file_put_contents("accesstoken.txt", $client->getAccessToken(), LOCK_EX);
}

//Insert a file

$file = new Google_DriveFile();
$file->setTitle($docTitle);
$file->setDescription('Edge Document Preview');
$file->setMimeType($mime);

$labels = new Google_DriveFileLabels();
$labels->setRestricted(true);

$file->setLabels($labels);
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
dba_insert($filePath, json_encode($createdFile), $fdb);
print_r(json_encode($createdFile));
?>
