<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>File store</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <script type="text/javascript" src="js/script.js"></script>
</head>
<body>
<?php if ($logged): ?>
  <div style="margin-bottom:8px;">Welcome, <b><?php echo $_SESSION['userEmail']; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;
  [<a href="console.php?ref=1">home</a>]
  [<a href="filelist.php?ref=1">files list</a>]
  [<a href="logout.php">logout</a>]</div>
<?php else: ?>
  [<a href="index.php">home</a>][<a href="filelist.php?ref=1">files list</a>]
<?php endif; ?>