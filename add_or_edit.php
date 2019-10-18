<?php
include('inc/functions.php');
$entry_id=$title=$date=$time_spent=$time_unit=$learned=$resources=$error_message="";
$operation="Add";
$tags = array();

//---------------- in case the user wants to edit one specific entry -----------------
if(!empty($_GET['entry_id'])) {
  $entry_id = trim(filter_input(INPUT_GET,'entry_id',FILTER_SANITIZE_NUMBER_INT));

  if(!empty($entry_id))
  {
    list($entry_id,$title,$date,$time_spent,$time_unit,$learned,$resources) = get_entries($entry_id,null);
    //make sure the tags array only contains tag_id's
    foreach(get_tags(null,$entry_id) as $key=>$value) {
      $tags[$key] = $value['tag_id'];
    }
    $operation="Update";
  }
}

//---------------- in case the user submitted the form  -----------------
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $entry_id = trim(filter_input(INPUT_POST,'entry_id',FILTER_SANITIZE_NUMBER_INT));

  if(!empty($entry_id)) {
    $operation="Update";
  }

  $title = trim(filter_input(INPUT_POST,'title',FILTER_SANITIZE_STRING));
  $date = trim(filter_input(INPUT_POST,'date',FILTER_SANITIZE_STRING));
  $time_spent = trim(filter_input(INPUT_POST,'time_spent',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION));
  $time_unit = trim(filter_input(INPUT_POST,'time_unit',FILTER_SANITIZE_STRING));
  $learned = trim(filter_input(INPUT_POST,'learned',FILTER_SANITIZE_STRING));
  $resources = trim(filter_input(INPUT_POST,'resources',FILTER_SANITIZE_STRING));
  $tags = filter_input(INPUT_POST,'tags',FILTER_VALIDATE_INT,FILTER_REQUIRE_ARRAY);

  //date format from form is yyyy-mm-dd
  $datearray = explode("-",$date);

  if(empty($title) || empty($date) || empty($time_spent) || empty($time_unit)) {
   $error_message .= 'Please fill in the required fields: Title, Date and Time Spent' . "<br>";
  }
  //checkdate expects 1 month, 2 day, 3 year
  elseif(!checkdate($datearray[1],$datearray[2],$datearray[0])) {
    $error_message .= 'Invalid date' . "<br>";
  }
  else {
    //add or update the entry
    //if successfully, then continue to link tags, based on the returned entry_id from add_or_edit_entry
    //if not succesfully, show error
    if($entry_id = add_or_edit_entry($title,$date,$time_spent,$time_unit,$learned,$resources,$entry_id)) {

      //link selected tags to entry
      //if succesfully, continue to main page
      //if not succesfully, continue to main page with error message, so the user can link tags later to the created entry
      if(link_tags($entry_id,$tags)) {
        header('location: index.php');
        exit;
      }
      else {
        $location = 'location: index.php?msg=Entry+';
        if ($operation == "Add") $location .= $operation . "e";
        $location .= "d+but+Tags+not+Updated";
        header($location);
        exit;
      }


    }
    else {
      $error_message .= "Could not add entry" . "<br>";
    }
  }
}

$page_title = "$operation Entry";
include('inc/header.php');
?>
<!-- Building  up the form -->
<div class="edit-entry">

    <form method="post" action="add_or_edit.php">
        <label for="title">Title</label>
        <input id="title" type="text" name="title" value="<?php echo $title; ?>"><br>
        <label for="date">Date</label>
        <input id="date" type="date" name="date" value="<?php echo $date; ?>"><br>
        <label for="time_spent"> Time Spent & Unit <i>(minutes, hours, days, weeks)</i></label>
        <input id="time_spent" type="text" name="time_spent" value="<?php echo $time_spent; ?>">
        <!-- new select for time units -->
        <select id="time_unit" name="time_unit">
          <option value="minutes" <?php if($time_unit == "minutes") echo " SELECTED";?>>Minutes</option>
          <option value="hours" <?php if($time_unit == "hours") echo " SELECTED";?>>Hours</option>
          <option value="days" <?php if($time_unit == "days") echo " SELECTED";?>>Days</option>
          <option value="weeks" <?php if($time_unit == "weeks") echo " SELECTED";?>>Weeks</option>
        </select>
        <br>
        <label for="learned">What I Learned</label>
        <textarea id="learned" rows="5" name="learned"><?php echo $learned; ?></textarea>
        <label for="resources">Resources to Remember</label>
        <textarea id="resources" rows="5" name="resources"><?php echo $resources; ?></textarea>

          <?php
          //show available tags as checkboxes 
          if($items = get_tags(null,null)) {

            echo "<fieldset>\n";
            echo "<legend>Tags:</legend>\n";

            foreach($items as $item) {

              echo "<input type=\"checkbox\" id=\"tag_".$item['tag_id']."\" name=\"tags[]\" value=\"".$item['tag_id'] ."\"";

              if(is_array($tags)) {
                if(in_array($item['tag_id'],$tags)) {
                  echo " CHECKED";
                }
              }
              echo " />\n";
              echo "<label class='check_label' for='tag_".$item['tag_id']."'>".$item['title']."</label><br>\n";

            }

            echo "</fieldset><br>\n";


          }
          ?>


        <?php
          if(!empty($entry_id)) {
            echo "<input type=\"hidden\" name=\"entry_id\" value=\"".$entry_id."\">";
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
