<?php
include('inc/functions.php');
$tag_id = $title = $error_message = "";
$page_title = "Edit tags";
$tags = array();

//variables to control the 3 components on the screen, and to understand whether the user submitted a form yes or no
$show_list = TRUE;//should all tags be displayed, with edit and delete buttons?
$show_add_form = TRUE;//should the add tag form be displayed?
$show_update_form = FALSE;//should the update tag form be displayed?
$submit_validation = FALSE;//did the user submit a form and should possible errors be displayed?

//---------------- in case the user wants to edit one specific tag -----------------
if(!empty($_GET['edit'])) {
  $tag_id = trim(filter_input(INPUT_GET,'edit',FILTER_SANITIZE_NUMBER_INT));

  //if a valid tag_id was provided
  if(!empty($tag_id)) {
    //only show update form
    $show_list = FALSE;
    $show_add_form = FALSE;
    $show_update_form = TRUE;
    $submit_validation = FALSE;
  }
  //if an invalid tag_id was provided
  else {
    $error_message .= "No tag to be updated<br>";
    //show full tag list and add form incl. error
    $show_list = TRUE;
    $show_add_form = TRUE;
    $show_update_form = FALSE;
    $submit_validation = FALSE;
  }
}

//---------------- in case the user wants to delete one specific tag -----------------
if(isset($_POST['delete'])) {
  //show full tag list and add form
  $show_list = TRUE;
  $show_add_form = TRUE;
  $show_update_form = FALSE;
  $submit_validation = FALSE;

  if(delete_tag(filter_input(INPUT_POST,'delete',FILTER_SANITIZE_NUMBER_INT))) {
    $error_message .= "Tag deleted<br>";
  }
  else {
    $error_message .= "Tag could not be deleted<br>";
  }
}

//---------------- in case the user submitted the form -----------------
if(isset($_POST['edit']) || isset($_POST['new'])) {
  $tag_id = trim(filter_input(INPUT_POST,'edit',FILTER_SANITIZE_NUMBER_INT));
  $title = ltrim(trim(filter_input(INPUT_POST,'title',FILTER_SANITIZE_STRING)),'#');

  //in case the user didn't fill in a title
  if(empty($title)) {
    $error_message .= 'Please fill in the Title' . "<br>";
    //only show update form, with error
    $show_list = FALSE;
    $show_add_form = FALSE;
    $show_update_form = TRUE; //so the user can fill in a title
    $submit_validation = TRUE;
  }
  else {
    //in case the user filled in a title and the results were submitted succesfully
    if(add_or_update_tag($title,$tag_id)) {
      if (empty($tag_id)) $error_message .= "Tag Added";
      else $error_message .= "Tag Updated";
      //show full tag list and add form incl. message
      $show_list = TRUE;
      $show_add_form = TRUE;
      $show_update_form = FALSE;
      $submit_validation = FALSE;

      //remove stored post title
      $title = "";
    }

    // in case the function returned false, because the title already exists
    else {
      $error_message .= 'Tag name already exists. Choose new name' . "<br>";
      //only show update form, with error
      $show_list = FALSE;
      $show_add_form = FALSE;
      $show_update_form = TRUE; //so the user can fill in a title
      $submit_validation = TRUE;
    }
  }

}

//---------------- retrieve the tag details, in case the form was not submitted -----------------

//if one tag should be updated, retrieve 1 tag
if(!$submit_validation && $show_update_form) {
  if(!(list($tag_id,$title) = get_tags($tag_id,null))) {
      $error_message .= "No tag to be updated<br>";
      //show full tag list and add form, including error
      $show_list = TRUE;
      $show_add_form = TRUE;
      $show_update_form = FALSE;
      $submit_validation = FALSE;

  }
}
//is the full tag list should be displayed, retrieve all tags
if($show_list) {
  if(!($tags = get_tags(null,null))) {
    $error_message .= "No tags available<br>";
    //only show add form and error
    $show_list = FALSE;
    $show_add_form = TRUE;
    $show_update_form = FALSE;
    $submit_validation = FALSE;
  }
}

include('inc/header.php');

if($show_list || $show_add_form || $show_update_form) {

  echo "<div class=\"edit-entry\">";
}

//in case the 'add tag form' should be displayed
if($show_add_form) {
  //New tag
  echo "<form action=\"tags.php\" method=\"post\" \">\n";
  echo "<label for=\"tag_title\">New Tag:</label>\n";
  echo "<input id=\"tag_title\" type=\"text\" name=\"title\" value=\"#".$title."\" />\n";
  echo "<input type='hidden' value='new' name='new'>\n";
  echo "&nbsp;<input type=\"submit\" value=\"Add\" class=\"button\">\n";
  echo "</form>\n";
}
//in case the full tag list should be displayed
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

//in case the 'update tag form' should be displayed
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
