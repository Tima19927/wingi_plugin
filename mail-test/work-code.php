<?php
require_once 'vendor/autoload.php';
// echo __DIR__.'/client_secret.json';
define('APPLICATION_NAME','Mail to table');
define('SCOPES', implode(' ', array(Google_Service_Sheets::SPREADSHEETS)));
//define('SCOPES', "https://www.googleapis.com/auth/spreadsheets");
define('DEV_KEY',"AIzaSyD0mJUXHqAkC6MbiaEnHKxL_tX2d4UVNio");
define('CLIENT_ID', "857535033482-8ts5lt91q03tg6agjd9h176i1mme36ui.apps.googleusercontent.com");
define("CONFIG", __DIR__.'/client_secret.json');
define("CLIENT_SECRET",'PoF5FY1-F4MgIbk9M1e2ZZUe');


$client = new Google_Client();
$client->setDeveloperKey(DEV_KEY);
$client->setApplicationName(APPLICATION_NAME);
$client->setScopes(SCOPES);
$client->setClientSecret(CLIENT_SECRET);
$client->setAuthConfigFile(CONFIG);
$client->setClientId(CLIENT_ID);

$service = new Google_Service_Sheets($client);
$spreadsheetId = "1iszFZ-4H-1ebiqGnUTXZpSF-meqO2YGSMPimo8zS8Ik";


  $range = 'Лист1!A1:C1';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $values = $response->getValues();
  var_dump($values);
  if (count($values) == 0) {
      print "No data found.\n";
  } else {
      print "Name, Major:\n";
      foreach ($values as $row) {
          printf("%s, %s\n", $row[0], $row[4]);
      }
  }