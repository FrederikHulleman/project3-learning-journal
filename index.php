<?php
include('inc/functions.php');
$tag_id = $error_message = null;
$page_title = "All available entries";

if(isset($_POST['delete'])) {
  if(delete_entry(filter_input(INPUT_POST,'delete',FILTER_SANITIZE_NUMBER_INT))) {
    header('location: index.php?msg=Entry+Deleted');
    exit;
  }
  else {
    header('location: index.php?msg=Unable+to+Delete+Entry');
    exit;
  }

}

if(!empty($_GET['tag_id'])) {
  $tag_id = trim(filter_input(INPUT_GET,'tag_id',FILTER_SANITIZE_NUMBER_INT));
  if(empty($tag_id) || !(list($tag_id,$title) = get_tags($tag_id,null))) {
    $error_message .= "No Valid Tag<br>";
  }
  else {
    $page_title = "#" . $title . " entries";
  }
}

if(!($entries = get_entries(null,$tag_id))) {
  $error_message .= "No journal entries available.<br>";
}

include('inc/header.php');
?>
<div class="entry-list">
  <?php

  foreach($entries as $entry) {
      echo "<article>\n";
      echo "<h2><a href='detail.php?entry_id=".$entry['entry_id']."'>".$entry['title']."</a></h2>\n";
      echo "<time datetime='".$entry['date']."'>".date("F j, Y",strtotime($entry['date']))."</time><br>\n";
      if($tags = get_tags(null,$entry['entry_id'])) {
        echo "<p class='tags'>\n";

        foreach($tags as $tag) {
          echo "<a href='index.php?tag_id=".$tag['tag_id']."'>#".$tag['title']."</a> \n";

        }

        echo "</p>\n";


      }

      echo "<br><form method='post' action='index.php' onsubmit=\"return confirm('Are you sure you want to delete this entry?');\">\n";
      echo "<input type='hidden' value='". $entry['entry_id'] ."' name='delete'>\n";
      echo "<a href=\"add_or_edit.php?entry_id=".$entry['entry_id']."\" class=\"button\">Edit</a>";
      echo "&nbsp;<input type='submit' class='button' value='Delete'>\n";
      echo "</form>\n";

      echo "</article>\n";
    }
  ?>
</div>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
