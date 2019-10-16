<?php
include('inc/functions.php');
$error_message = "";
$page_title = "Manage tags";

if(isset($_POST['delete'])) {
  if(delete_tag(filter_input(INPUT_POST,'delete',FILTER_SANITIZE_NUMBER_INT))) {
    header('location: tags.php?msg=Tag+Deleted');
    exit;
  }
  else {
    header('location: tags.php?msg=Unable+to+Delete+Tag');
    exit;
  }

}

include('inc/header.php');
?>

<div class="edit-entry">

<?php

if(!($tags = get_tags(null,null))) {
  $error_message .= "No tags available<br>";
}
else {

    foreach($tags as $tag) {
      echo "<form action=\"tags.php\" method=\"post\" onsubmit=\"return confirm('Are you sure you want to delete this tag?');\">\n";

      //echo "<label for=\"tag_".$tag['tag_id']."\">Tag ". ($key+1) ."</label>\n";
      echo "<p class=\"tag_name\" id=\"tag_".$tag['tag_id']."\">".$tag['title']."</p>\n";
      echo "<input type='hidden' value='". $tag['tag_id'] ."' name='delete'>\n";
      echo "<a href=\"tags.php?tag_id=".$tag['tag_id']."\" class=\"button\">Edit</a>\n";
      echo "&nbsp;<input type=\"submit\" value=\"Delete\" class=\"button\">\n";
      //echo "<input type=\"text\" id=\"tag_".$tag['tag_id']."\" name=\"tags[".$tag['tag_id']."]\" value=\"".$tag['title'] ."\" />";


      echo "</form>\n";
    }
  ?>

  <br><br><a href="index.php" class="button button-secondary">Cancel</a>
<?php
}
?>
</div>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
