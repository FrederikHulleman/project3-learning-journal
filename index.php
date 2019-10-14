<?php
include('inc/functions.php');

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

$entries = get_entries();
if(empty($entries)) {
  $error_message = "No journal entries available.";

}

include('inc/header.php');
?>
<div class="entry-list">
  <?php

  foreach($entries as $entry) {
      echo "<article>\n";
      echo "<h2><a href='detail.php?id=".$entry['id']."'>".$entry['title']."</a></h2>\n";
      echo "<time datetime='".$entry['date']."'>".date("F j, Y",strtotime($entry['date']))."</time><br>\n";

      echo "<br><form method='post' action='index.php' onsubmit=\"return confirm('Are you sure you want to delete this entry?');\">\n";
      echo "<input type='hidden' value='". $entry['id'] ."' name='delete'>\n";
      echo "<a href=\"add_or_edit.php?id=".$entry['id']."\" class=\"button\">Edit</a>";
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
