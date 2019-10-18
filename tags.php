<?php
include('inc/functions.php');
$tag_id = $title = $error_message = "";
$page_title = "Edit tags";
$tags = array();

//variables to control the 3 components on the screen
$show_list = TRUE;
$show_add_form = TRUE;
$show_update_form = FALSE;
$submit_validation = FALSE;

if(!empty($_GET['edit'])) {
  $tag_id = trim(filter_input(INPUT_GET,'edit',FILTER_SANITIZE_NUMBER_INT));

  $show_list = FALSE;
  $show_add_form = FALSE;
  $show_update_form = TRUE;
  $submit_validation = FALSE;
}

if(isset($_POST['delete'])) {
  $show_list = TRUE;
  $show_add_form = TRUE;
  $show_update_form = FALSE;
  $submit_validation = FALSE;

  if(delete_tag(filter_input(INPUT_POST,'delete',FILTER_SANITIZE_NUMBER_INT))) {
    header('location: tags.php?msg=Tag+Deleted');
    exit;
  }
  else {
    header('location: tags.php?msg=Unable+to+Delete+Tag');
    exit;
  }
}

if(isset($_POST['edit']) || isset($_POST['new'])) {
  $tag_id = trim(filter_input(INPUT_POST,'edit',FILTER_SANITIZE_NUMBER_INT));
  $title = ltrim(trim(filter_input(INPUT_POST,'title',FILTER_SANITIZE_STRING)),'#');

  if(empty($title)) {
    $error_message .= 'Please fill in the Title' . "<br>";
    $show_list = FALSE;
    $show_add_form = FALSE;
    $show_update_form = TRUE;
    $submit_validation = TRUE;
  }
  else {
    if(add_or_update_tag($title,$tag_id)) {
      if (empty($tag_id)) $msg = "Tag+Added";
      else $msg = "Tag+Updated";
      header('location: tags.php?msg='.$msg);
      exit;
    }
    else {
      $error_message .= 'Tag name already exists. Choose new name' . "<br>";
      $show_list = FALSE;
      $show_add_form = FALSE;
      $show_update_form = TRUE;
      $submit_validation = TRUE;
    }
  }

}

if($show_list) {
  if(!($tags = get_tags(null,null))) {
    $error_message .= "No tags available<br>";
    $show_list = TRUE;
    $show_add_form = FALSE;
    $show_update_form = FALSE;
    $submit_validation = FALSE;
  }
}
elseif(!$submit_validation && $show_update_form) {
  if(!(list($tag_id,$title) = get_tags($tag_id,null))) {
      $error_message .= "No tag to be updated<br>";
      $show_list = TRUE;
      $show_add_form = FALSE;
      $show_update_form = FALSE;
      $submit_validation = FALSE;
  }
}


include('inc/header.php');



if($show_list || $show_add_form || $show_update_form) {

  echo "<div class=\"edit-entry\">";
}

if($show_add_form) {
  //New tag
  echo "<form action=\"tags.php\" method=\"post\" \">\n";
  echo "<label for=\"tag_title\">New Tag:</label>\n";
  echo "<input id=\"tag_title\" type=\"text\" name=\"title\" value=\"#".$title."\" />\n";
  echo "<input type='hidden' value='new' name='new'>\n";
  echo "&nbsp;<input type=\"submit\" value=\"Add\" class=\"button\">\n";
  echo "</form>\n";
}
if ($show_list) {
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
}

if($show_update_form) {

    echo "<form action=\"tags.php\" method=\"post\">\n";
    echo "<input type='hidden' value='". $tag_id ."' name='edit'>\n";
    echo "<input id=\"tag_title\" type=\"text\" name=\"title\" value=\"#".$title ."\" />\n";
    echo "&nbsp;<input type=\"submit\" value=\"Update\" class=\"button\">\n";
    echo "<a href=\"tags.php\" class=\"button button-secondary\">Cancel</a>";
    echo "</form>\n";

}

if($show_list || $show_add_form || $show_update_form) {

  echo "</div>";
}


?>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
