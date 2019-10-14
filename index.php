<?php
include('inc/functions.php');

if(isset($_GET['msg'])) {
  $error_message = trim(filter_input(INPUT_GET,'msg',FILTER_SANITIZE_STRING));
}


include('inc/header.php');
?>
<div class="entry-list">
  <?php
    foreach(get_entries() as $entry) {
      echo "<article>
              <h2><a href='detail.php?id=".$entry['id']."'>".$entry['title']."</a></h2>
              <time datetime='".$entry['date']."'>".date("F j, Y",strtotime($entry['date']))."</time>
              </article>";
    }
      /*
      `id`	INTEGER,
    	`title`	TEXT,
    	`date`	TEXT,
    	`time_spent`	INTEGER,
    	`learned`	BLOB,
    	`resources`	BLOB,
      */
  ?>
</div>
</div> <!-- closing 'container' div tag from header.php  -->
<?php
include('inc/footer.php');
?>
