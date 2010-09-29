<?php
  require_once 'config/config.php';
  require_once 'class/class_user.php';
  session_start();
  
  $user = new User();
  $logged = $user->checkEntry();
  
  // get file info
  $fileId = isset($_GET['file']) ? $_GET['file'] : $_POST['file'];
  $file = $user->getFileById($fileId);
  
  // comment access
  $access = isset($_SESSION['userId']) ? '' : $user->quickCheckAccess($fileId);
  
  // get comments
  $comments = $user->getComments($fileId);
  
  // get tree keys
  $key = $user->getTreeKeys($fileId);
  
  // post comment
  if (isset($_POST['submit'])) {
    $result = $user->postComment($_POST['left_key'], $_POST['right_key'], $_POST['level'], $_POST['comment'], $fileId);
    if (strlen($result) == '') {
      header('Location: comments.php?file=' . $fileId);
    }
  }
  
  require_once 'header.php';
  
?>

<h3>Comments</h3>
<?php if (is_array($file)): ?>
  <div>File: <b><?php echo $file['file_name'] ?></b><br />Uploaded: <?php echo $file['file_date']; ?>&nbsp;&nbsp;&nbsp;[<a href="download.php?file_id=<?php echo $file['file_id']; ?>">download</a>]</div>
  <hr />
  
  <div style="text-decoration:underline;">Comments</div>
  <?php if (is_array($comments)): ?>
    <?php $i = 1; foreach ($comments as $comment): ?>
      <div style="border-bottom:solid 1px #C0C0C0;margin-bottom:10px;margin-left:<?php echo (25 * $comment['level']); ?>px;">
        <div class="comment">
          #<?php echo $i; ?> 
          <?php echo $comment['text']; ?>
          <?php if ($access == ''): ?>[<a href="javascript:answerTo(<?php echo $comment['left']; ?>, <?php echo $comment['right']; ?>, <?php echo $comment['level']; ?>, <?php echo $i; ?>)">answer</a>]<?php endif; ?><br />
          <span><?php echo $comment['date']; ?></span>
        </div>
      </div>
    <?php $i++; endforeach; ?>
  <?php endif; ?>
  <div>
  </div>
  
  <?php if ($access == ''): ?>
    <div>
      <form action="comments.php" method="post">
        <input type="hidden" id="left_key" name="left_key" value="<?php echo $key['left_key']; ?>">
        <input type="hidden" id="right_key" name="right_key" value="<?php echo $key['right_key']; ?>">
        <input type="hidden" id="level" name="level" value="<?php echo $key['level']; ?>">
        
        <input type="hidden" id="temp_left_key" value="<?php echo $key['left_key']; ?>">
        <input type="hidden" id="temp_right_key" value="<?php echo $key['right_key']; ?>">
        <input type="hidden" id="temp_level" value="<?php echo $key['level']; ?>">
        
        <input type="hidden" name="file" value="<?php echo $fileId; ?>">
        <div>Add comment <span id="answer" style="font-style:italic;"></span></div>
        <div><textarea name="comment" style="width:400px;height:100px;" rows="200" cols="10"></textarea></div>
        <div style="width:400px;text-align:right;"><input type="submit" name="submit" value="Add">&nbsp;&nbsp;<input type="button" value="Cancel" onclick="window.location='filelist.php?ref=1'"></div>
      </form>
      <div><?php if (isset($result)) echo $result; ?></div>
    </div>
  <?php else: ?>
    Please login to comment this file
  <?php endif; ?>
  
<?php else: ?>
  Bad file
<?php endif; ?>


<?php
  require_once 'footer.php';
?>