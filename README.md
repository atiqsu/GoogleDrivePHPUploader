Google Drive Uploader
=====================

Upload files to Google Drive from PHP.

Instructions
------------

Go to Google API Console and create a new app.

Go to APIs & auth -> Credentials in the dashboard for that app and create a new client id. Set the application type as Installed application.

Update client_id and client_secret in client_secret.json which can be copied from client_secret.json.sample. Note you can also download the json file from api console and rename it to client_secret.json.

You will be request to enter the access token once.

Usage
-----

Browse to the cloned folder in command line and type `php index.php`. A demo file document.txt will be uploaded. You can change this to anything you want in the index.php file.