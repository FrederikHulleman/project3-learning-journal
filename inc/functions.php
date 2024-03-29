<?php
//flexible function to retrieve tags, 3 possibilities:
//1. all tags: call function without $tag_id & $entry_id
//2. all tags per entry: call function with $entry_id, without $tag_id
//3. retrieve title for 1 tag_id: call function with tag_id, without entry_id
function get_tags($tag_id = null,$entry_id = null) {
  include('connection.php');
  $sql = $where = $order = "";
  try {
    $sql = "SELECT tags.* FROM tags";
    if (!empty($entry_id)) {
      $where = " JOIN entries_to_tags ON entries_to_tags.tag_id = tags.tag_id
              WHERE entries_to_tags.entry_id = ?";
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

//flexible function to retrieve entries, 3 possibilities:
//1. all entries: call function without $tag_id & $entry_id
//2. all entries per tag: call function with $tag_id, without $enty_id
//3. retrieve details for 1 entry_id: call function with entry_id, without tag_id
function get_entries($entry_id = null,$tag_id = null) {
  include('connection.php');
  $sql = $where = $order = "";
  try {
    $sql = "SELECT entries.* FROM entries";

    if (!empty($tag_id)) {
      $where = " JOIN entries_to_tags ON entries.entry_id = entries_to_tags.entry_id
                WHERE entries_to_tags.tag_id = ?
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

//one function to either add or update an entry;
//for adding: call function without $entry_id;
//for updating: call function with $entry_id
//if succesfull, this function returns the (new) entry_id, so it can be used to link tags
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
    if(empty($entry_id))  {
      $entry_id = $db->lastInsertId();
    }
    return $entry_id;
  }
  else {
    return FALSE;
  }
}

//function to delete one entry_id; it also deletes the references to tags for that specific entry; tags are not impacted
//because 2 queries are called on 2 different tables, I implemented this within a transaction and commit block
//thanks to info on stackoverflow on how to commit 2 query in 1 transaction
//link: https://stackoverflow.com/questions/6598215/prepare-multiple-statments-before-executing-them-in-a-transaction
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

//function to delete one tag_id; it also deletes the references to entries for that specific tag; entries are not impacted
//because 2 queries are called on 2 different tables, I implemented this within a transaction and commit block
//thanks to info on stackoverflow on how to commit 2 query in 1 transaction
//link: https://stackoverflow.com/questions/6598215/prepare-multiple-statments-before-executing-them-in-a-transaction
function delete_tag($tag_id) {

  include('connection.php');
  $entries_sql = $tags_sql = "";
  try {
    $tags_sql = "DELETE FROM tags WHERE tag_id = ?";
    $tags_results = $db->prepare($tags_sql);

    $entries_sql = "DELETE FROM entries_to_tags WHERE tag_id = ?";
    $entries_results = $db->prepare($entries_sql);

    $db->beginTransaction();

    $tags_results->bindParam(1,$tag_id,PDO::PARAM_INT);
    $tags_results->execute();

    $entries_results->bindParam(1,$tag_id,PDO::PARAM_INT);
    $entries_results->execute();

    $db->commit();

  } catch (Exception $e) {
    $db->rollBack();
    echo "Bad query: " . $e->getMessage();
    exit;
  }

  return TRUE;
}

//one function to either add or update a tag;
//for adding: call function without tag_id;
//for updating: call function with tag_id
function add_or_update_tag($title,$tag_id=null) {

  include('connection.php');
  $sql = "";
  try {

    if(!empty($tag_id)) {
      //make sure the chosen tag title doesn't exist
      $sql = "UPDATE tags SET title = ? WHERE tag_id = ? AND NOT ? IN (SELECT title FROM tags WHERE NOT tag_id = ?)";

      $results = $db->prepare($sql);

      $results->bindParam(1,$title,PDO::PARAM_STR);
      $results->bindParam(2,$tag_id,PDO::PARAM_INT);
      $results->bindParam(3,$title,PDO::PARAM_STR);
      $results->bindParam(4,$tag_id,PDO::PARAM_INT);
    }
    else {
      //make sure the chosen tag title doesn't exist
      //thanks to https://stackoverflow.com/questions/267804/sql-server-how-to-insert-a-record-and-make-sure-it-is-unique
      $sql = "INSERT INTO tags (title)
                SELECT ?
                WHERE NOT EXISTS (SELECT 1 FROM tags WHERE title = ?)";

      $results = $db->prepare($sql);

      $results->bindParam(1,$title,PDO::PARAM_STR);
      $results->bindParam(2,$title,PDO::PARAM_STR);
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
    //in case the title from input already existed in the tag table
    return FALSE;
  }

}

//function to link the tags if an entry is added or updated
//for 1 single entry_id and an array of selected tags
//thanks to info on stackoverflow on how to commit 2 query in 1 transaction
//link: https://stackoverflow.com/questions/6598215/prepare-multiple-statments-before-executing-them-in-a-transaction
function link_tags($entry_id,$tags) {

  include('connection.php');
  $delete_sql = $insert_sql = "";

  try {
    //i choose to remove everything and insert all selected tags later
    $delete_sql = "DELETE FROM entries_to_tags WHERE entry_id = ?";
    $delete_results = $db->prepare($delete_sql);

    //if the user didn't select tags, then only the delete part has to run of this function
    if(is_array($tags) && count($tags) > 0) {

      $insert_sql = "INSERT INTO entries_to_tags (entry_id,tag_id) VALUES (?,?)";
      //include enough question marks inserts for all tag-entry couples
      for ($i=1; $i < count($tags); $i++) {
        $insert_sql .= ", (?,?)";
      }

      $insert_results = $db->prepare($insert_sql);

    }


    $db->beginTransaction();

    $delete_results->bindParam(1,$entry_id,PDO::PARAM_INT);
    $delete_results->execute();

    //if the user didn't select tags, then only the delete part has to run of this function
    if(is_array($tags) && count($tags) > 0) {

      for ($i=0; $i < count($tags); $i++) {
        //calculate which question mark numbers should be inserted
        //1st tag: 1 & 2
        //2nd tag: 3 & 4
        //3rd tag: 5 & 6
        $second_param = ($i + 1) * 2;
        $first_param = $second_param  - 1;

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
