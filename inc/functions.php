<?php

function get_entries($id = null) {
  include('connection.php');
  $sql = $where = $order = "";
  try {
    $sql = "SELECT * FROM entries";
    if (!empty($id)) {
      $where = " WHERE id = ?";
    }
    $order = " ORDER BY date DESC";
    $results = $db->prepare($sql . $where . $order);
    if (!empty($id)) {
      $results->bindParam(1,$id,PDO::PARAM_INT);
    }
    $results->execute();

    if (!empty($id)) {
      $entries = $results->fetch();
    }
    else {
      $entries = $results->fetchAll(PDO::FETCH_ASSOC);
    }

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    exit;
  }
  return $entries;

}

function add_or_edit_entry($title,$date,$time_spent,$learned,$resources,$id = null) {
  include('connection.php');
  $sql = "";

  try {
    if(!empty($id))  {
      $sql = "UPDATE entries SET title = ?, date = ?, time_spent = ?, learned = ?, resources = ? WHERE id = ?";
    }
    else {
      $sql = "INSERT INTO entries(title,date,time_spent,learned,resources) VALUES (?,?,?,?,?)";
    }

    $results = $db->prepare($sql);
    $results->bindParam(1,$title,PDO::PARAM_STR);
    $results->bindParam(2,$date,PDO::PARAM_STR);
    $results->bindParam(3,$time_spent,PDO::PARAM_STR);
    $results->bindParam(4,$learned,PDO::PARAM_STR);
    $results->bindParam(5,$resources,PDO::PARAM_STR);
    if(!empty($id))  {
      $results->bindParam(6,$id,PDO::PARAM_INT);
    }
    $results->execute();

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    return FALSE;
  }
  return TRUE;
}

function delete_entry($id) {

  include('connection.php');
  $sql = "";
  try {
    $sql = "DELETE FROM entries WHERE id = ?";

    $results = $db->prepare($sql);
    $results->bindParam(1,$id,PDO::PARAM_INT);
    $results->execute();

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    return FALSE;
  }
  return TRUE;

}




/*
//retrieve all tables
$results = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
$output = $results->fetchAll(PDO::FETCH_ASSOC);
var_dump($output);
*/


?>
