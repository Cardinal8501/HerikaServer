<?php


/* Voice Sample Extractor */


$path = dirname((__FILE__)) . DIRECTORY_SEPARATOR;
require_once($path . "conf".DIRECTORY_SEPARATOR."conf.php"); // API KEY must be there

require_once($path . "lib".DIRECTORY_SEPARATOR."fuz_convert.php"); // API KEY must be there

if (strpos($_GET["oname"],".fuz"))  {
    $ext="fuz";
} else if (strpos($_GET["oname"],".xwm")) {
    $ext="xwm";
} else if (strpos($_GET["oname"],".wav")) {
  $ext="wav";
}

    
$finalName=__DIR__.DIRECTORY_SEPARATOR."soundcache/_vsx_".md5($_FILES["file"]["tmp_name"]).".$ext";


if (!$_FILES["file"]["tmp_name"])
    die("VSX error, no data given");

if (filesize($_FILES["file"]["tmp_name"])==0) {
    error_log("Empty file {$_FILES["file"]["tmp_name"]}");
    die();
}

@copy($_FILES["file"]["tmp_name"] ,$finalName);


error_log("Received sample: {$_GET["oname"]}");

if (strpos($_GET["oname"],".fuz")) {
    $finalFile=fuzToWav($finalName);
    
} else if (strpos($_GET["oname"],".xwm")) {

    $finalFile=xwmToWav($finalName);

  } else if (strpos($_GET["oname"],".wav")) {

    $finalFile=wavToWav($finalName);
}

if (!isset($GLOBALS["TTS"]["XTTSFASTAPI"]["endpoint"]) || !($GLOBALS["TTS"]["XTTSFASTAPI"]["endpoint"]) ) {
  die("Error");
}

//$codename=strtr(strtolower($_GET["codename"]),[" "=>"_"]);
$codename = str_replace(" ", "_", mb_strtolower($_GET["codename"], 'UTF-8'));

$url = $GLOBALS["TTS"]["XTTSFASTAPI"]["endpoint"].'/upload_sample';

$already=file_exists("{$GLOBALS["TTS"]["XTTSFASTAPI"]["endpoint"]}/sample/$codename.wav");
if ($already) {
  error_log("Empty file {$_FILES["file"]["tmp_name"]} already exists at {$GLOBALS["TTS"]["XTTSFASTAPI"]["endpoint"]}/sample/$codename.wav");
  die();
}



$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => array(
    'wavFile' => new CURLFile($finalFile, 'audio/wav', "$codename.wav")
  ),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: multipart/form-data'
  )
));

// Execute cURL request and get response
$response = curl_exec($curl);

  
?>
