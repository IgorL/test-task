<?php
  require_once 'config/config.php';
  require_once 'class/class_user.php';
  session_start();
  
  $user = new User();
  $logged = $user->checkEntry();
  if (!$logged) {
    header('Location: index.php');
    exit;
  }
  
  // pagination
  $pagination = $user->pagination($_GET, 'console');
  
  // sort by
  if (isset($_GET['ref'])) {
    unset($_SESSION['by']);
    unset($_SESSION['order']);
  }
  if (isset($_GET['by'])) {
    $_SESSION['by'] = $_GET['by'];
    $_SESSION['order'] = $_GET['ord'];
  }
  
  $files = $user->getUserFiles($pagination['page']);
  
  // upload file
  if (isset($_POST['name'])) {
    $uploadRes = $user->uploadFile($_POST['name'], $_FILES['userfile']);
    if (count($uploadRes) == 0) {
      header('Location: console.php');
    }
  }
  
  // change file status
  if (isset($_GET['status'])) {
    $user->changeFileStatus($_GET['status'], $_GET['file_id']);
    header('Location: console.php');
  }
  
  // delete selected files
  if (isset($_POST['delete'])) {
    $user->deleteFiles($_POST['delete']);
    header('Location: console.php');
  }
  
  require_once 'header.php';
?>

<h3>File manager</h3>
<div>
  <form action="console.php" method="post" enctype="multipart/form-data">
    <div class="file-row">
      <div style="width:70px;">Name</div>
      <div><input type="text" id="name" name="name" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" /></div>
      <div id="err-name"><?php if (isset($uploadRes['name'])) echo $uploadRes['name']; ?></div>
      <div class="clr"></div>
    </div>
    <div class="file-row">
      <div style="width:70px;">File</div>
      <div><input type="file" id="userfile" name="userfile" /></div>
      <div style="margin-left:50px;" id="err-file"><?php if (isset($uploadRes['file'])) echo $uploadRes['file']; ?></div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
    <div><input type="submit" name="submit" value="Upload" style="margin-right:15px;" /><input type="button" value="Cancel" onclick="clearForm()" /></div>
  </form>
</div>

<hr />
<?php if (is_array($files)): ?>
  <div>
    <form action="console.php" method="post" id="file-form">
      <input type="hidden" name="delete" value="1">
      <div class="file-head file-row">
        <div style="width: 25px;"><input type="checkbox" title="Select all" onclick="checkAll()" id="check-all"></div>
        <div style="width: 40px;">Id</div>
        <div><a href="console.php?by=name&ord=<?php if (isset($_GET['by']) && $_GET['by'] == 'name' && $_GET['ord'] == 'asc'): ?>desc<?php else: ?>asc<?php endif;?>" class="lnk">Name</a></div>
        <div><a href="console.php?by=date&ord=<?php if (isset($_GET['by']) && $_GET['by'] == 'date' && $_GET['ord'] == 'asc'): ?>desc<?php else: ?>asc<?php endif;?>" class="lnk">Date</a></div>
        <div>Download</div>
        <div>Allow comments</div>
        <div class="clr"></div>
      </div>
      <?php foreach ($files as $file): ?>
        <div class="file-row">
          <div style="width: 25px;"><input type="checkbox" name="delete[]" value="<?php echo $file['id']; ?>"></div>
          <div style="width: 40px;"><?php echo $file['id']; ?></div>
          <div><a href="comments.php?file=<?php echo $file['id']; ?>"><?php echo $file['name']; ?></a></div>
          <div><?php echo $file['date']; ?></div>
          <div>[<a href="download.php?file_id=<?php echo $file['id']; ?>">download</a>]</div>
          <div>[<a href="console.php?status=<?php echo $file['access']; ?>&file_id=<?php echo $file['id']; ?>"><?php echo $file['access']; ?></a>]</div>
          <div class="clr"></div>
        </div>
      <?php endforeach; ?>
    </form>
  </div>
  <div class="clr"></div>
  <div>[<a href="javascript:deleteSelected()">delete selected</a>]</div>
  <div style="width:700px;text-align:center;"><?php echo $pagination['list']; ?></div>
<?php else: ?>
  No files
<?php endif; ?>
<?php
  require_once 'footer.php';
?>

