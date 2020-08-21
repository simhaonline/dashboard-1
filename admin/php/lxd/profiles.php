<?php
 
//Instantiate the GET variables
if (isset($_GET['remote']))
  $remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
if (isset($_GET['project']))
  $project = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);
if (isset($_GET['action']))
  $action = filter_var(urldecode($_GET['action']), FILTER_SANITIZE_STRING);
if (isset($_GET['profile']))
  $profile = filter_var(urldecode($_GET['profile']), FILTER_SANITIZE_STRING);

if (isset($_GET['name']))
  $name = filter_var(urldecode($_GET['name']), FILTER_SANITIZE_STRING);
if (isset($_GET['description']))
  $description = filter_var(urldecode($_GET['description']), FILTER_SANITIZE_STRING);
if (isset($_GET['repo']))
  $repo = filter_var(urldecode($_GET['repo']), FILTER_SANITIZE_STRING);

//Instantiate the POST variable
if (isset($_POST['json']))  
  $json = $_POST['json'];

//Set the curl variables
$cert = "/root/.config/lxc/client.crt";
$key = "/root/.config/lxc/client.key";

//Query DB to find remote
$db = new SQLite3('/var/lxdware/data/sqlite/lxdware.sqlite');
$db_results = $db->query("SELECT * FROM lxd_hosts WHERE id = $remote LIMIT 1");

while($res = $db_results->fetchArray()){
  $url = "https://" . $res['host'] . ":" . $res['port'];

  //Run the matching action
  switch ($action) {
    case "createProfileForm":
      $url = $url . "/1.0/profiles?project=" . $project;
      $data = escapeshellarg('{"description": "'.$description.'", "name": "'.$name.'"}');
      $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X POST -d $data $url");
      break;
    case "createProfileJson":
      $url = $url . "/1.0/profiles?project=" . $project;
      $data = escapeshellarg($json);
      $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X POST -d $data $url");
      break;
    case "deleteProfile":
      $url = $url . "/1.0/profiles/" . $profile . "?project=" . $project;
      $data = escapeshellarg('{}');
      $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X DELETE -d $data $url");
    break;
    case "updateProfile":
      $url = $url . "/1.0/profiles/" . $profile . "?project=" . $project;
      $data = escapeshellarg($json);
      $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X PUT -d $data $url");
    break;
    case "renameProfile":
      $url = $url . "/1.0/profiles/" . $profile . "?project=" . $project;
      $data = escapeshellarg('{"name": "' . $name . '"}');
      $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X POST -d $data $url");
    break;
    case "loadProfile":
      $url = $url . "/1.0/profiles/" . $profile . "?project=" . $project;
      $results = shell_exec("sudo curl -k -L --cert $cert --key $key -X GET $url");
    break;
  }
}

echo $results;

?>