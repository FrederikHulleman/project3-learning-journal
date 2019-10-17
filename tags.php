<?php
include('inc/functions.php');
$tag_id = $update = $title = $error_message = $edit = "";
$page_title = "Manage tags";
$tags = array();
$mode = "show_all";

if(!empty($_GET['edit'])) {
  $tag_id = trim(filter_input(INPUT_GET,'edit',FILTER_SANITIZE_NUMBER_INT));
  $mode = "edit_one";
}

if(isset($_POST['delete'])) {
  if(delete_tag(filter_input(INPUT_POST,'delete',FILTER_SANITIZE_NUMBER_INT))) {
    header('location: tags.php?msg=Tag+Deleted');
    exit;
  }
  else {
    header('location: tags.php?msg=Unable+to+Delete+Tag');
    exit;
  }
  $mode = "show_all";
}

if(isset($_POST['edit'])) {
  $tag_id = trim(filter_input(INPUT_POST,'edit',FILTER_SANITIZE_NUMBER_INT));
  $title = ltrim(trim(filter_input(INPUT_POST,'title',FILTER_SANITIZE_STRING)),'#');

  if(empty($title)) {
    $error_message .= 'Please fill in the Title' . "<br>";
    $mode = "edit_one_missing";
  }
  else {
    if(add_or_update_tag($tag_id,$title)) {
      header('location: tags.php?msg=Tag+Updated');
      exit;
    }
    else {
      $error_message .= 'Tag name already exists. Choose new name' . "<br>";
      $mode = "edit_one_missing";
    }
  }

}

switch ($mode) {
//edit_one_missing has no case and is filtered out, since the submitted title should be shown
  case 'edit_one':
    if(!(list($tag_id,$title) = get_tags($tag_id,null))) {
      $error_message .= "No tags available<br>";
      $mode = "show_none";
    }
    break;

  case 'show_all':
    if(!($tags = get_tags(null,null))) {
      $error_message .= "No tags available<br>";
      $mode = "show_none";
    }
    break;
}

include('inc/header.php');

switch ($mode) {
//show_none has no case and is filtered out
  case 'edit_one_missing':
  case 'edit_one':
    echo "<div class=\"edit-entry\">";
    echo "<form action=\"tags.php\" method=\"post\">\n";
    echo "<input type='hidden' value='". $tag_id ."' name='edit'>\n";
    echo "<input id=\"tag_title\" type=\"text\" name=\"title\" value=\"#".$title ."\" />\n";
    echo "&nbsp;<input type=\"submit\" value=\"Update\" class=\"button\">\n";
    echo "<a href=\"tags.php\" class=\"button button-secondary\">Cancel</a>";
    echo "</form>\n";
    echo "</div>\n";
    break;

  case 'show_all':
    echo "<div class=\"edit-entry\">";

    //New tag
    echo "<form action=\"tags.php\" method=\"post\" \">\n";
    echo "<label for=\"tag_title\">New Tag:</label>\n";
    echo "<input id=\"tag_title\" type=\"text\" name=\"title\" value=\"#\" />\n";
    echo "<input type='hidden' value='new' name='new'>\n";
    echo "&nbsp;<input type=\"submit\" value=\"Add\" class=\"button\">\n";
    echo "</form>\n";

    //existing tags
    echo "<p>Existing tags:</p>";

    foreach($tags as $tag) {
      echo "<form action=\"tags.php\" method=\"post\" onsubmit=\"return confirm('Are you sure you want to delete this tag?');\">\n";
      echo "<p class=\"tag_name\">#".$tag['title']."</p>\n";
      echo "<input type='hidden' value='". $tag['tag_id'] ."' name='delete'>\n";
      echo "<a href=\"tags.php?edit=".$tag['tag_id']."\" class=\"button\">Edit</a>\n";
      echo "&nbsp;<input type=\"submit\" value=\"Delete\" class=\"button\">\n";
      echo "</form>\n";

    }
    echo "<br><br><a href=\"index.php\" class=\"button button-secondary\">Cancel</a>";
    echo "</div>\n";
    break;

}

?>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
