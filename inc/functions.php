<?php

function get_entries($id = null) {
  include('connection.php');
  $sql = $where = "";
  try {
    $sql = "SELECT * FROM entries";
    if (!empty($id)) {
      $where = " WHERE id = ?";
    }
    $results = $db->prepare($sql . $where);
    if (!empty($id)) {
      $results->bindParam(1,$id,PDO::PARAM_INT);
    }
    $results->execute();
    $entries = $results->fetchAll(PDO::FETCH_ASSOC);
    /*
    `id`	INTEGER,
    `title`	TEXT,
    `date`	TEXT,
    `time_spent`	INTEGER,
    `learned`	BLOB,
    `resources`	BLOB,
    */

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    exit;
  }
  return $entries;

}
/*
//retrieve all tables
$results = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
$output = $results->fetchAll(PDO::FETCH_ASSOC);
var_dump($output);
*/


?>
