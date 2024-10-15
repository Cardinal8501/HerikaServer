<?php

$enginePath =__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR;
require_once($enginePath . "conf" . DIRECTORY_SEPARATOR . "conf.php");

$conn = pg_connect("host={$GLOBALS["PGSQL"]["DBHOST"]} dbname={$GLOBALS["PGSQL"]["DBNAME"]}} user={$GLOBALS["PGSQL"]["DBUSER"]} password={$GLOBALS["PGSQL"]["DBPASS"]}");

if (!$conn) {
    echo "Failed to connect to database.\n";
    die();
}

// Drop and recreate database
$Q[]="DROP SCHEMA IF EXISTS $schema CASCADE";
$Q[]="DROP EXTENSION IF EXISTS vector CASCADE";
$Q[]="CREATE SCHEMA $schema";
$Q[]="CREATE EXTENSION vector";

foreach ($Q as $QS) {
  $r = pg_query($conn, $QS);
  if (!$r) {
    echo pg_last_error($conn);
    die();
  } else {
    echo "$QS ok<br/>";
  }
  
}

// Path to SQL file to import
$sqlFile = $enginePath.'/data/database_default.sql';

// Command to import SQL file using psql
$psqlCommand = "PGPASSWORD={$GLOBALS["PGSQL"]["DBPASS"]}} psql -h {$GLOBALS["PGSQL"]["DBHOST"]} -p {$GLOBALS["PGSQL"]["DBPASS"]} -U {$GLOBALS["PGSQL"]["DBUSER"]}} -d {$GLOBALS["PGSQL"]["DBNAME"]} -f $sqlFile";

// Execute psql command
$output = [];
$returnVar = 0;
exec($psqlCommand, $output, $returnVar);

if ($returnVar !== 0) {
    echo "Failed to import SQL file.\n";
    echo implode("\n", $output) . "\n";
    exit;
}

echo "SQL file imported successfully.\n";
echo implode("\n", $output) . "\n";

echo "Import completed.\n";



?>
