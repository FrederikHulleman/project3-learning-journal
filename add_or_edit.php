<?php
include('inc/functions.php');
$id=$title=$date=$time_spent=$learned=$resources="";

if(!empty($_GET['id'])) {
  $id = trim(filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT));

  if(!empty($id))
  {
    list($id,$title,$date,$time_spent,$learned,$resources) = get_entries($id);
  }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = trim(filter_input(INPUT_POST,'id',FILTER_SANITIZE_NUMBER_INT));
  $title = trim(filter_input(INPUT_POST,'title',FILTER_SANITIZE_STRING));
  $date = trim(filter_input(INPUT_POST,'date',FILTER_SANITIZE_STRING));
  $time_spent = trim(filter_input(INPUT_POST,'time_spent',FILTER_SANITIZE_STRING));
  $learned = trim(filter_input(INPUT_POST,'learned',FILTER_SANITIZE_STRING));
  $resources = trim(filter_input(INPUT_POST,'resources',FILTER_SANITIZE_STRING));

  //date format from form is yyyy-mm-dd
  $datearray = explode("-",$date);

  if(empty($title) || empty($date) || empty($time_spent)) {
   $error_message = 'Please fill in the required fields: Title, Date and Time Spent';
  }
  //checkdate expects 1 month, 2 day, 3 year
  elseif(!checkdate($datearray[1],$datearray[2],$datearray[0])) {
    $error_message = 'Invalid date';
  }
  else {
    if(add_or_edit_entry($title,$date,$time_spent,$learned,$resources,$id)) {
      header('location: index.php');
      exit;
    }
    else {
      $error_message = "Could not add entry";
    }
  }
}


include('inc/header.php');
?>

<div class="edit-entry">
    <h2><?php
            if (!empty($id)) {
              echo "Update";
            } else {
              echo "Add";
            }?> Entry</h2>
    <form method="post" action="add_or_edit.php">
        <label for="title">Title</label>
        <input id="title" type="text" name="title" value="<?php echo $title; ?>"><br>
        <label for="date">Date</label>
        <input id="date" type="date" name="date" value="<?php echo $date; ?>"><br>
        <label for="time-spent"> Time Spent</label>
        <input id="time-spent" type="text" name="time_spent" value="<?php echo $time_spent; ?>"><br>
        <label for="what-i-learned">What I Learned</label>
        <textarea id="what-i-learned" rows="5" name="learned"><?php echo $learned; ?></textarea>
        <label for="resources-to-remember">Resources to Remember</label>
        <textarea id="resources-to-remember" rows="5" name="resources"><?php echo $resources; ?></textarea>
        <?php
          if(!empty($id)) {
            echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\">";
          }
        ?>
        <input type="submit" value="Publish Entry" class="button">
        <a href="index.php" class="button button-secondary">Cancel</a>
    </form>
</div>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
