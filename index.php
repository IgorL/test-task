<?php
  require_once 'config/config.php';
  require_once 'class/class_user.php';
  session_start();
  
  $user = new User();
  if ($user->checkEntry()) {
    header('Location: console.php');
    exit;
  }
  
  if (isset($_POST['email'])) {
    $login = $user->loginOrRegister($_POST['email'], $_POST['password']);
    if (count($login) == 0) {
      header('Location: console.php');
    }
  }
  
  require_once 'header.php';
  
?>

<h3>Signin or register</h3>
<div>
  <form action="index.php" method="post">
    <div class="file-row">
      <div style="width:70px;">Email</div>
      <div><input type="text" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" /></div>
      <div style="width:280px;"><?php if (isset($login['email'])) echo $login['email'];?></div>
      <div class="clr"></div>
    </div>
    <div class="file-row">
      <div style="width:70px;">Password</div>
      <div><input type="password" name="password" value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>" /></div>
      <div style="width:280px;"><?php if (isset($login['password'])) echo $login['password'];?></div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
    <div><input type="submit" name="submit" value="Login" /></div>
  </form>
</div>
<?php
  require_once 'footer.php';
?>