<?php
include('inc/functions.php');

$page_title = "Manage tags";

include('inc/header.php');
?>

<div class="edit-entry">

<?php

if(!($tags = get_tags(null,null))) {
  $error_message .= "No tags available<br>";
}
else {

    foreach($tags as $key=>$tag) {
      echo "<form action=\"tags.php\" method=\"post\">";

      //echo "<label for=\"tag_".$tag['tag_id']."\">Tag ". ($key+1) ."</label>\n";
      echo "<p class=\"tag_name\" id=\"tag_".$tag['tag_id']."\">".$tag['title']."</p>";
      echo "<input type='hidden' value='". $tag['tag_id'] ."' name='delete'>\n";
      echo "<a href=\"tags.php?tag_id=".$tag['tag_id']."\" class=\"button\">Edit</a>";
      echo "&nbsp;<input type=\"submit\" value=\"Delete\" class=\"button\">\n";
      //echo "<input type=\"text\" id=\"tag_".$tag['tag_id']."\" name=\"tags[".$tag['tag_id']."]\" value=\"".$tag['title'] ."\" />";


      echo "</form>";
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
