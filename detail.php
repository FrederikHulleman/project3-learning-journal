<?php
include('inc/functions.php');
$id=$title=$date=$time_spent=$learned=$resources="";

if(isset($_POST['delete'])) {
  if(delete_entry(filter_input(INPUT_POST,'delete',FILTER_SANITIZE_NUMBER_INT))) {
    header('location: index.php?msg=Entry+Deleted');
    exit;
  }
  else {
    header('location: detail.php?msg=Unable+to+Delete+Entry');
    exit;
  }

}

if(!empty($_GET['id'])) {
  $id = trim(filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT));

  if(!empty($id))
  {
    list($id,$title,$date,$time_spent,$learned,$resources) = get_entries($id);
  }
}

if(empty($id)) {
  header('location: index.php?msg=No+journal+could+be+selected');
  exit;
}

include('inc/header.php');
?>
<div class="entry-list single">
    <article>
        <h1><?php echo $title; ?></h1>
        <time datetime="<?php echo $date; ?>"><?php echo date("F j, Y",strtotime($date)); ?></time>
        <div class="entry">
            <h3>Time Spent: </h3>
            <p><?php echo $time_spent; ?></p>
        </div>
        <?php
        if(!empty($learned)) {
          echo "<div class='entry'>\n"
              . "<h3>What I Learned:</h3>"
              . "<p>".$learned."</p>"
              . "</div>";
        }
        if(!empty($resources)) {
          echo "<div class='entry'>\n"
              . "<h3>Resources to Remember:</h3>"
              . "<p>".$resources."</p>"
              . "</div>";
        }
        ?>


        <!-- <div class="entry">
            <h3>Resources to Remember:</h3>
            <ul>
                <li><a href="">Lorem ipsum dolor sit amet</a></li>
                <li><a href="">Cras accumsan cursus ante, non dapibus tempor</a></li>
                <li>Nunc ut rhoncus felis, vel tincidunt neque</li>
                <li><a href="">Ipsum dolor sit amet</a></li>
            </ul>
        </div>-->
    </article>
</div>

</div> <!-- closing 'container' div tag from header.php  -->
<div class="edit">
  <form method="post" action="detail.php" onsubmit="return confirm('Are you sure you want to delete this entry?');">
  <input type="hidden" value="<?php echo $id;?>" name="delete">
  <a class="button" href="add_or_edit.php?id=<?php echo $id;?>">Edit</a>
  <input type="submit" class="button" value="Delete">
  </form>
    <!-- <p><a class="button" href="add_or_edit.php?id=<?php echo $id; ?>">Edit Entry</a></p> -->
</div>

<?php
include('inc/footer.php');
?>
