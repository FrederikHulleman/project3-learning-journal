<?php

function get_entries() {
  include('connection.php');
  try {
    return $db->query("SELECT * FROM entries");

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    exit;
  }

}
/*
$results = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
$output = $results->fetchAll(PDO::FETCH_ASSOC);
var_dump($output);
*/


?>
