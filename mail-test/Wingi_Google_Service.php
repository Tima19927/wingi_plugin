<?php
require_once 'vendor/autoload.php';
require_once 'vendor/google/apiclient/src/Google/Utils/URITemplate.php';

//define('APPLICATION_NAME','Mail to table');
//define('SCOPES', implode(' ', array(Google_Service_Sheets::SPREADSHEETS)));
//// define('DEV_KEY',"AIzaSyD0mJUXHqAkC6MbiaEnHKxL_tX2d4UVNio");
//define('DEV_KEY',"AIzaSyCnQxXm8M9KLWhmFIVFILo8ue4gdFqh1HQ");
//
//define('CLIENT_ID', "857535033482-8ts5lt91q03tg6agjd9h176i1mme36ui.apps.googleusercontent.com");
//define("CLIENT_SECRET",'dLnasxg9jA1V640NwqczCreb');
//
//// Файл клиент секрет - это файл json Сервисные аккаунты проекта
//define("CONFIG", __DIR__.'\client_secret2.json');


define('APPLICATION_NAME','Mail to table');
define('SCOPES', implode(' ', array(Google_Service_Sheets::SPREADSHEETS)));
define('DEV_KEY',"AIzaSyB6bk6poX3ndtDvTRiNaS6ZfOayIUD86eE"); // Ключ апи

define('CLIENT_ID',        "333597182903-d4rd1ji0poke71rj66lk0u5kha2b9t7m.apps.googleusercontent.com"); //
define("CLIENT_SECRET",   "Khc5FQOckD4ID4LXcRUdQ2tK");

// Файл клиент секрет - это файл json Сервисные аккаунты проекта
define("CONFIG", __DIR__.'/client_secret_4.json');




class Wingi_Google_Service{

    private  $sheetFields;

    public function __construct( )  {

    }

    private function getClient ( ){
        $client = new Google_Client();
        $client->setDeveloperKey(DEV_KEY);
        $client->setApplicationName(APPLICATION_NAME);
        $client->setScopes(SCOPES);
        $client->setClientSecret(CLIENT_SECRET);
        $client->setAuthConfigFile(CONFIG);
        $client->setClientId(CLIENT_ID);

        if ( $client )
            return $client;

        return null;
    }

    public function getService( ) {
        $client = $this->getClient();
        $service = null;
        if ( $client )
            $service = new Google_Service_Sheets($client);

        if ( $service )
            return $service;


        return null;
    }

    private function getGoogleRange ( $values ) {
        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues(
            ["values" => $values ]
          //Формат передаваемых данных ["values" => ["a1", "b2"]]
        );
        return $valueRange;
    }

   public function appendDataCell($spreadsheetId, $range="A1:K1", $value) {

       try{
            $service = $this->getService();
            $valueRange =  $this->getGoogleRange( $value );
            $conf = [
                "valueInputOption" => "RAW"
            ];
          $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $conf);
        }catch (Exception $ex){
            print_r($ex->getMessage());
        }
    }

    public function getFieldSheet($spreadsheetId , $range){

       try {
           $service = $this->getService();


           $response = $service->spreadsheets_values->get($spreadsheetId, $range);
           $values = $response->getValues();

           if ( count($values) > 0 ) {
              $this->sheetFields =  $values;
           }
           else{
               $this->sheetFields =  null;
           }
       }
       catch (Exception $ex){
          error_log(   print_r($ex, true)  );
       }

     return $this->sheetFields;

    }

}
