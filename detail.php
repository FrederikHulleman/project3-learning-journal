<?php
include('inc/functions.php');
$id=$title=$date=$time_spent=$learned=$resources="";
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
    <p><a href="add_or_edit.php?id=<?php echo $id; ?>">Edit Entry</a></p>
</div>

<?php
include('inc/footer.php');
?>
