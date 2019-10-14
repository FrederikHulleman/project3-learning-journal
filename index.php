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
include('inc/header.php');
?>
<div class="entry-list">
  <?php
    foreach(get_entries() as $entry) {
      echo "<article>\n"
              ."<h2><a href='detail.php?id=".$entry['id']."'>".$entry['title']."</a></h2>\n"
              ."<time datetime='".$entry['date']."'>".date("F j, Y",strtotime($entry['date']))."</time>\n";

      echo "<form method='post' action='index.php' onsubmit=\"return confirm('Are you sure you want to delete this entry?');\">\n"
              . "<input type='hidden' value='". $entry['id'] ."' name='delete'>\n"
              . "<input type='submit' class='button--delete' value='Delete'>\n"
              . "</form>\n";

      echo "</article>\n";
    }
  ?>
</div>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
