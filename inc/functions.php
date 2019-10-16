<?php
function get_tags($tag_id = null,$entry_id = null) {
  include('connection.php');
  $sql = $where = $order = "";
  try {
    $sql = "SELECT tags.* FROM tags";
    if (!empty($entry_id)) {
      $where = " JOIN entries_to_tags ON entries_to_tags.tag_id = tags.tag_id
              JOIN entries ON entries_to_tags.entry_id = entries.entry_id
              WHERE entries.entry_id = ?";
    }
    elseif (!empty($tag_id)) {
      $where = " WHERE tags.tag_id = ?";
    }
    $order = " ORDER BY tags.title ASC";
    $results = $db->prepare($sql . $where . $order);
    if (!empty($entry_id)) {
      $results->bindParam(1,$entry_id,PDO::PARAM_INT);
    }
    elseif (!empty($tag_id)) {
      $results->bindParam(1,$tag_id,PDO::PARAM_INT);
    }
    $results->execute();

    if (!empty($tag_id)) {
      $tags = $results->fetch();
    }
    else {
      $tags = $results->fetchAll(PDO::FETCH_ASSOC);
    }

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    exit;
  }
  return $tags;
}

function get_entries($entry_id = null,$tag_id = null) {
  include('connection.php');
  $sql = $where = $order = "";
  try {
    $sql = "SELECT entries.* FROM entries";

    if (!empty($tag_id)) {
      $where = " JOIN entries_to_tags ON entries.entry_id = entries_to_tags.entry_id
                JOIN tags ON entries_to_tags.tag_id = tags.tag_id
                WHERE tags.tag_id = ?
        ";
    }
    elseif (!empty($entry_id)) {
      $where = " WHERE entries.entry_id = ?";
    }
    $order = " ORDER BY entries.date DESC";

    $results = $db->prepare($sql . $where . $order);
    if (!empty($tag_id)) {
      $results->bindParam(1,$tag_id,PDO::PARAM_INT);
    }
    elseif (!empty($entry_id)) {
      $results->bindParam(1,$entry_id,PDO::PARAM_INT);
    }
    $results->execute();

    if (!empty($entry_id)) {
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

function add_or_edit_entry($title,$date,$time_spent,$time_unit,$learned,$resources,$entry_id = null) {
  include('connection.php');
  $sql = "";

  try {
    if(!empty($entry_id))  {
      $sql = "UPDATE entries SET title = ?, date = ?, time_spent = ?, time_unit = ?,learned = ?, resources = ? WHERE entry_id = ?";
    }
    else {
      $sql = "INSERT INTO entries(title,date,time_spent,time_unit,learned,resources) VALUES (?,?,?,?,?,?)";
    }

    $results = $db->prepare($sql);
    $results->bindParam(1,$title,PDO::PARAM_STR);
    $results->bindParam(2,$date,PDO::PARAM_STR);
    $results->bindParam(3,$time_spent,PDO::PARAM_STR);
    $results->bindParam(4,$time_unit,PDO::PARAM_STR);
    $results->bindParam(5,$learned,PDO::PARAM_STR);
    $results->bindParam(6,$resources,PDO::PARAM_STR);
    if(!empty($entry_id))  {
      $results->bindParam(7,$entry_id,PDO::PARAM_INT);
    }
    $results->execute();

  } catch (Exception $e) {
    echo "Bad query: " . $e->getMessage();
    exit;
  }
  if($results->rowCount() > 0) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

function delete_entry($entry_id) {

  include('connection.php');
  $entries_sql = $tags_sql = "";
  try {
    $entries_sql = "DELETE FROM entries WHERE entry_id = ?";
    $entries_results = $db->prepare($entries_sql);

    $tags_sql = "DELETE FROM entries_to_tags WHERE entry_id = ?";
    $tags_results = $db->prepare($tags_sql);

    $db->beginTransaction();

    $entries_results->bindParam(1,$entry_id,PDO::PARAM_INT);
    $entries_results->execute();

    $tags_results->bindParam(1,$entry_id,PDO::PARAM_INT);
    $tags_results->execute();

    $db->commit();



  } catch (Exception $e) {
    $db->rollBack();
    echo "Bad query: " . $e->getMessage();
    exit;
  }

  return TRUE;

}

//thanks to info on stackoverflow on how to commit 2 query in 1 transaction
//link: https://stackoverflow.com/questions/6598215/prepare-multiple-statments-before-executing-them-in-a-transaction
function link_tags($entry_id,$tags) {

  include('connection.php');
  $delete_sql = $insert_sql = "";

  try {
    //if the user deselected tags, they should be removed
    //i choose to remove everything and insert all selected tags later
    $delete_sql = "DELETE FROM entries_to_tags WHERE entry_id = ?";
    $delete_results = $db->prepare($delete_sql);

    if(is_array($tags) && count($tags) > 0) {

      $insert_sql = "INSERT INTO entries_to_tags (entry_id,tag_id) VALUES (?,?)";

      for ($i=1; $i < count($tags); $i++) {
        $insert_sql .= ", (?,?)";
      }

      $insert_results = $db->prepare($insert_sql);

    }


    $db->beginTransaction();

    $delete_results->bindParam(1,$entry_id,PDO::PARAM_INT);
    $delete_results->execute();

    if(!empty($insert_sql)) {

      for ($i=0; $i < count($tags); $i++) {
        //1st tag: 1 & 2
        //2nd tag: 3 & 4
        //3rd tag: 5 & 6

        $second_param = ($i + 1) * 2;
        $first_param = $second_param  - 1;

        echo "1st: $first_param and 2st: $second_param <br>";

        $insert_results->bindParam($first_param,$entry_id,PDO::PARAM_INT);
        $insert_results->bindParam($second_param,$tags[$i],PDO::PARAM_INT);
      }

      $insert_results->execute();

    }

    $db->commit();

  } catch (Exception $e) {
    $db->rollBack();
    echo "Bad query: " . $e->getMessage();
    exit;
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
