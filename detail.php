<?php
include('inc/functions.php');
if(!empty($_GET['id'])) {
  $id = trim(filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT));
  $entry = get_entries($id)[0];
  /*
  `id`	INTEGER,
  `title`	TEXT,
  `date`	TEXT,
  `time_spent`	INTEGER,
  `learned`	BLOB,
  `resources`	BLOB,
  */
  if(empty($entry)) {
    header('location: index.php?msg=No+valid+journal');
  }
}

include('inc/header.php');
?>
<div class="entry-list single">
    <article>
        <h1><?php echo $entry['title']; ?></h1>
        <time datetime="2016-01-31">January 31, 2016</time>
        <div class="entry">
            <h3>Time Spent: </h3>
            <p>15 Hours</p>
        </div>
        <div class="entry">
            <h3>What I Learned:</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ut rhoncus felis, vel tincidunt neque.</p>
            <p>Cras egestas ac ipsum in posuere. Fusce suscipit, libero id malesuada placerat, orci velit semper metus, quis pulvinar sem nunc vel augue. In ornare tempor metus, sit amet congue justo porta et. Etiam pretium, sapien non fermentum consequat, <a href="">dolor augue</a> gravida lacus, non accumsan. Vestibulum ut metus eleifend, malesuada nisl at, scelerisque sapien.</p>
        </div>
        <div class="entry">
            <h3>Resources to Remember:</h3>
            <ul>
                <li><a href="">Lorem ipsum dolor sit amet</a></li>
                <li><a href="">Cras accumsan cursus ante, non dapibus tempor</a></li>
                <li>Nunc ut rhoncus felis, vel tincidunt neque</li>
                <li><a href="">Ipsum dolor sit amet</a></li>
            </ul>
        </div>
    </article>
</div>

</div> <!-- closing 'container' div tag from header.php  -->
<div class="edit">
    <p><a href="edit.php">Edit Entry</a></p>
</div>

<?php
include('inc/footer.php');
?>
