<?php
  require_once 'config/config.php';
  require_once 'class/class_user.php';
  session_start();
  
  $user = new User();
  $logged = $user->checkEntry();
  
  // pagination
  $pagination = $user->pagination($_GET, 'filelist');
  
  // sort by
  if (isset($_GET['ref'])) {
    unset($_SESSION['by']);
    unset($_SESSION['order']);
  }
  if (isset($_GET['by'])) {
    $_SESSION['by'] = $_GET['by'];
    $_SESSION['order'] = $_GET['ord'];
  }
  
  $files = $user->getFileList($pagination['page']);
  
  
  require_once 'header.php';
  
?>

<h3>Files list</h3>

<?php if (is_array($files)): ?>
  <div>
    <div class="file-head file-row">
      <div style="width: 40px;">Id</div>
      <div><a href="filelist.php?by=name&ord=<?php if (isset($_GET['by']) && $_GET['by'] == 'name' && $_GET['ord'] == 'asc'): ?>desc<?php else: ?>asc<?php endif;?>" class="lnk">Name</a></div>
      <div><a href="filelist.php?by=date&ord=<?php if (isset($_GET['by']) && $_GET['by'] == 'date' && $_GET['ord'] == 'asc'): ?>desc<?php else: ?>asc<?php endif;?>" class="lnk">Date</a></div>
      <div>Download</div>
      <div class="clr"></div>
    </div>
    <?php foreach ($files as $file): ?>
      <div class="file-row">
        <div style="width: 40px;"><?php echo $file['id']; ?></div>
        <div><a href="comments.php?file=<?php echo $file['id']; ?>"><?php echo $file['name']; ?></a></div>
        <div><?php echo $file['date']; ?></div>
        <div>[<a href="download.php?file_id=<?php echo $file['id']; ?>">download</a>]</div>
        <div class="clr"></div>
      </div>
    <?php endforeach; ?>
    <div class="clr"></div>
    <div style="width:500px;text-align:center;"><?php echo $pagination['list']; ?></div>
  </div>
<?php else: ?>
  No files
<?php endif; ?>

<?php
  require_once 'footer.php';
?>