<?php
require_once __DIR__ . '/vendor/autoload.php';


define('APPLICATION_NAME', 'Google Sheets API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/sheets.googleapis.com-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/sheets.googleapis.com-php-quickstart.json
define('SCOPES', implode(' ', array(
    Google_Service_Sheets::SPREADSHEETS_READONLY)
));

//if (php_sapi_name() != 'cli') {
//  throw new Exception('This application must be run on the command line.');
//}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
  
  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    exit("La clave de Google Drive no est&aacute; configurada. Debe correr php quickstart.php desde el server primero.");
  }
  $client->setAccessToken($accessToken);
  
  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory =  __DIR__ . "/..";
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', $homeDirectory, $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);
$spreadsheetId = '1UMqp41iSRpobfhUq-VBr7_oQUSaUQ3N6XGBFvmEGKxU';

function leer($service, $spreadsheetId) {


// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
  
  $range = 'A1:C4';
  $response = $service->spreadsheets_values->get($spreadsheetId, $range);
  $values = $response->getValues();
  
  if (count($values) == 0) {
    print "No data found.\n";
  } else {
    foreach ($values as $row) {
      // Print columns A and E, which correspond to indices 0 and 4.
      printf("%s, %s, %s\n", $row[0], $row[1], $row[2]);
      print "<br>";
    }
  }
}

function escribir($service, $spreadsheetId) {
  $range = 'A1:C4';
  
  $values = array(
    array(
      1, 2, 3
    ),
    // Additional rows ...
  );
  $body = new Google_Service_Sheets_ValueRange(array(
    'values' => $values
  ));
  $valueInputOption = "RAW";
  $params = array(
    'valueInputOption' => $valueInputOption
  );
  $result = $service->spreadsheets_values->append($spreadsheetId, $range,
    $body, $params);
}

leer($service, $spreadsheetId);
escribir($service, $spreadsheetId);
leer($service, $spreadsheetId);