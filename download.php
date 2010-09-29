<?php
  require_once 'config/config.php';
  require_once 'class/class_user.php';
  session_start();
  
  $user = new User();
  $fileName = $user->getFilePath($_GET['file_id']);
  if ($fileName) {
    $fp = fopen(UPLOAD_PATH . $fileName, "rb");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");;
    header("Content-Disposition: attachment;filename=$fileName"); 
    header("Content-Transfer-Encoding: binary ");
    
    while (!feof($fp)) {
      echo fread($fp, 8192);
    }
    fclose($fp);
  } else {
    echo 'bad file';
  }
?>
