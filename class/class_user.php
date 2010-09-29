<?php
  class User {
    private $db;
    
    
    public function __construct() {
      $this->db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
      mysql_select_db(DB_DATABASE, $this->db);
    }
    
    public function __destruct() {
      mysql_close($this->db);
    }
    
    /**
    * check user's session
    * 
    */
    public function checkEntry() {
      if (isset($_SESSION['hash'])) {
        $hash = mysql_real_escape_string($_SESSION['hash']);
        $userId = intval($_SESSION['userId']);
        $query = "SELECT * FROM entry WHERE entry_hash = '$hash' AND entry_user_id = $userId";
        $result = mysql_fetch_assoc(mysql_query($query));
        return ($result['entry_agent'] == $_SERVER['HTTP_USER_AGENT']) ? true : false;
      }
    }
    
    /**
    * login or register new user
    * 
    * @param string $email
    * @param string $password
    * @return string
    */
    public function loginOrRegister($email, $password) {
      $error = array();
      $email = trim($email);
      $password = trim($password);
      
      if (!preg_match("#^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$#ix", $email)) {
        $error['email'] = 'Bad email format';
      }
      if (strlen($password) < 8) {
        $error['password'] = 'Password should be more than 8 characters';
      }
      
      if (count($error) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM users WHERE user_email = '$email'";
        $emailExists = mysql_num_rows(mysql_query($query));
        
        // email already exists, try to login
        if ($emailExists > 0) {
          $loginRes = $this->openSession($email, $password);
          if (!$loginRes) {
            $error['email'] = 'Email already exists';
          }
        // register new user
        } else {
          $query = "INSERT INTO users SET user_email = '$email', user_password = '$password'";
          mysql_query($query);
          $this->openSession($email, $password);
        }
      }
      
      return $error;
    }
    
    /**
    * register user's login and opens a session
    * 
    * @param string $email
    * @param string $password
    */
    private function openSession($email, $password) {
      $result = false;
        
      $query = "SELECT * FROM users WHERE user_email = '$email' AND user_password = '$password'";
      $login = mysql_query($query);
      if (mysql_num_rows($login) > 0) {
        $login = mysql_fetch_assoc($login);
        $_SESSION['userId'] = $login['user_id'];
        $_SESSION['userEmail'] = $email;
        $_SESSION['hash'] = session_id();
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO entry SET entry_user_id = {$_SESSION['userId']}, entry_hash = '{$_SESSION['hash']}', entry_date = '$date', entry_ip = '{$_SERVER['REMOTE_ADDR']}', entry_agent = '{$_SERVER['HTTP_USER_AGENT']}'";
        mysql_query($query);
        $result = true;
      }
      return $result;
    }
    
    /**
    * logout and destroy current session
    * 
    */
    public function logout() {
      unset($_SESSION['hash']);
      unset($_SESSION['userId']);
      session_destroy();
    }
    
    /**
    * upload file to the server
    * 
    * @param string $name file name
    * @param array $file array with file's data
    * @return string $error success if string empty
    */
    public function uploadFile($name, $file) {
      $error = array();
      $name = trim(mysql_real_escape_string($name));
      
      if ($name != '') {
        if ($file['error'] != 4) {
          for ($i = 0, $fileName = ''; $i < 6; $i++) {
            $fileName .= chr(rand(97,122));
          }
          preg_match('#.*?\.(.*?)$#', $file['name'], $match);
          $fileName .= '.' . strtolower($match[1]);
          $fullPath = UPLOAD_PATH . $fileName;
          
          if (!copy($file['tmp_name'], $fullPath)) {
            $error['file'] = 'File copy error';
          }
        } else {
          $error['file'] = 'Select file please';
        }
      } else {
        $error['name'] = 'Enter file name please';
      }
      
      if (count($error) == 0) {
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO files SET file_name = '$name', file_user_id = {$_SESSION['userId']}, file_path = '$fileName', file_type = '{$file['type']}', file_size = '{$file['size']}', file_date = '$date'";
        mysql_query($query);
      }
      return $error;
    }
    
    /**
    * get owner's files only
    * 
    * @param integer $page
    */
    public function getUserFiles($page) {
      $sort = $this->defineSortParams();
      $query = "SELECT * FROM files WHERE file_user_id = {$_SESSION['userId']} ORDER BY {$sort['by']} {$sort['order']} LIMIT $page, " . ITEMS_ON_PAGE;
      $files = mysql_query($query);
      if (mysql_num_rows($files) > 0) {
        $result = array();
        while ($file = mysql_fetch_assoc($files)) {
          array_push($result, array(
            'id' => $file['file_id'],
            'name' => $file['file_name'],
            'date' => $file['file_date'],
            'access' => $file['file_access']
          ));
        }
      } else {
        $result = false;
      }
      return $result;
    }
    
    /**
    * returns real(physical) file name
    * 
    * @param int $fileId
    * @return string
    */
    public function getFilePath($fileId) {
      $fileId = intval($fileId);
      $query = "SELECT file_path FROM files WHERE file_id = $fileId";
      $file = mysql_query($query);
      if (mysql_num_rows($file) > 0) {
        $file = mysql_fetch_assoc($file);
        $file = $file['file_path'];
      } else {
        $file = false;
      }
      return $file;
    }
    
    /**
    * get files list for all users
    * 
    * @param integer $page
    * @return array
    */
    public function getFileList($page) {
      $sort = $this->defineSortParams();
      $query = "SELECT * FROM files ORDER BY {$sort['by']} {$sort['order']} LIMIT $page, " . ITEMS_ON_PAGE;
      $files = mysql_query($query);
      if (mysql_num_rows($files) > 0) {
        $result = array();
        while ($file = mysql_fetch_assoc($files)) {
          array_push($result, array(
            'id' => $file['file_id'],
            'name' => $file['file_name'],
            'date' => $file['file_date'],
            'access' => $file['file_access']
          ));
        }
      } else {
        $result = false;
      }
      return $result;
    }
    
    /**
    * method defines parameters for sorting, table filed (file_name, file_date or file_id) and ASC/DESC
    * 
    * @return array
    */
    private function defineSortParams() {
      $sort = array('order' => '', 'by' => '');
      if (isset($_SESSION['by'])) {
        $sort['order'] = $_SESSION['order'] == 'asc' ? 'ASC' : 'DESC';
        switch ($_SESSION['by']) {
          case 'name':
            $sort['by'] = 'file_name';
            break;
          case 'date':
            $sort['by'] = 'file_date';
            break;
          default:
            $sort['by'] = 'file_id';
            break;
        }
      } else {
        $sort['order'] = 'DESC';
        $sort['by'] = 'file_id';
      }
      return $sort;
    }
    
    /**
    * returns amount of files
    * 
    * @param integer $userId
    * @return integer
    */
    public function getItemCount($userId = 0) {
      $where = $userId != 0 ? "WHERE file_user_id = $userId" : '';
      $query = "SELECT COUNT(*) AS cnt FROM files $where";
      $items = mysql_fetch_assoc(mysql_query($query));
      return $items['cnt'];
    }
    
    /**
    * claculates data for pagination
    * 
    * @param array $data
    * @param string $scr
    * @return array
    */
    public function pagination($data, $scr) {
      $page = (isset($data['page']) && $data['page'] != 1) ? ($data['page'] * ITEMS_ON_PAGE - ITEMS_ON_PAGE) : 0;
      
      $count = $scr == 'filelist' ? $this->getItemCount() : $this->getItemCount($_SESSION['userId']);
      $items = $count / ITEMS_ON_PAGE;
      $pagination = '';
      for ($i = 0; $i < $items; $i++) {
        $p = $i + 1;
        $pagination .= '<a href="'.$scr.'.php?page=' . $p . '">' . $p . '</a>&nbsp;';
      }
      return array('list' => $pagination, 'page' => $page);
    }
   
   /**
   * removes file from the system 
   *  
   * @param array $files
   */
    public function deleteFiles($files) {
      $ids = '';
      foreach ($files as $id) {
        $ids .= $id . ',';
      }
      $ids = substr($ids, 0, -1);
      $query = "SELECT file_path FROM files WHERE file_id IN ($ids) AND file_user_id = {$_SESSION['userId']}";
      
      $pathes = mysql_query($query);
      if (mysql_num_rows($pathes) > 0) {
        $query = "DELETE FROM comments, files USING files LEFT OUTER JOIN comments ON files.file_id = comments.comment_file_id WHERE file_id IN ($ids) AND file_user_id = {$_SESSION['userId']}";
        mysql_query($query);
        while ($path = mysql_fetch_assoc($pathes)) {
          unlink(UPLOAD_PATH . $path['file_path']);
        }
      }
    }
    
    /**
    * return file info by ID
    * 
    * @param int $fileId
    */
    public function getFileById($fileId) {
      $fileId = intval($fileId);
      $query = "SELECT * FROM files WHERE file_id = $fileId";
      $file = mysql_query($query);
      return mysql_num_rows($file) > 0 ? mysql_fetch_assoc($file) : false;
    }
    
    /**
    * returns all comment belonging to current file
    * 
    * @param int $fileId
    */
    public function getComments($fileId) {
      $fileId = intval($fileId);
      $query = "SELECT comment_id, comment_text, comment_left_key, comment_right_key, comment_level, comment_date FROM comments WHERE comment_file_id = $fileId AND comment_level != 0 ORDER BY comment_left_key";
      
      $comments = mysql_query($query);
      if (mysql_num_rows($comments) > 0) {
        $result = array();
        while ($comment = mysql_fetch_assoc($comments)) {
          array_push($result, array(
            'id' => $comment['comment_id'],
            'user' => $comment['user_email'],
            'text' => $comment['comment_text'],
            'date' => $comment['comment_date'],
            'left' => $comment['comment_left_key'],
            'right' => $comment['comment_right_key'],
            'level' => $comment['comment_level']
          ));
        }
      } else {
        $result = false;
      }
      return $result;
    }
    
    /**
    * post user's comment
    * 
    * @param int $left left key
    * @param int $right right key
    * @param int $level comment deep
    * @param string $comment
    * @param int $fileId
    */
    public function postComment($left, $right, $level, $comment, $fileId) {
      $fileId = intval($fileId);
      $comment = mysql_real_escape_string(strip_tags(trim($comment)));
      $error = '';
      
      // check access to comments for user
      if (empty($_SESSION['userId'])) {
        $error = $this->quickCheckAccess($fileId);
      }
      
      // check comment text
      if ($error == '' && strlen($comment) == 0) {
        $error = 'Please enter comment';
      }
      
      // valid
      if ($error == '') {
        $checkFirst = mysql_query("SELECT * FROM comments WHERE comment_file_id = $fileId");
        $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
        
        if (mysql_num_rows($checkFirst) == 0) {
          mysql_query("INSERT INTO comments SET comment_left_key = 1, comment_right_key = 2, comment_level = 0, comment_file_id = $fileId");
          $left = 1; $right = 2; $level = 0;
        }
        
        $date = date('Y-m-d H:i:s');
        mysql_query("UPDATE comments SET comment_right_key = comment_right_key + 2, comment_left_key = IF(comment_left_key > $right, comment_left_key + 2, comment_left_key) WHERE comment_right_key >= $right AND comment_file_id = $fileId");
        mysql_query("INSERT INTO comments SET comment_left_key = $right, comment_right_key = $right + 1, comment_level = $level + 1, comment_text = '$comment', comment_user_id = $userId, comment_file_id = $fileId, comment_date = '$date'");
      }
      return $error;
    }
    
    /**
    * returns tree's parent keys for current file
    * 
    * @param int $fileId
    */
    public function getTreeKeys($fileId) {
      $fileId = intval($fileId);
      $keys = mysql_query("SELECT comment_left_key AS left_key, comment_right_key AS right_key FROM comments WHERE comment_file_id = $fileId AND comment_level = 0");
      return (mysql_num_rows($keys) > 0) ? mysql_fetch_assoc($keys) : array('left_key' => 1, 'right_key' => 2, 'level' => 0);
    }
    
    /**
    * allows or prohibits post comments to a file for not logged users
    * 
    * @param string $status
    * @param int $fileId
    */
    public function changeFileStatus($status, $fileId) {
      $status = $status == 'open' ? 'close' : 'open';
      $fileId = intval($fileId);
      $userId = intval($_SESSION['userId']);
      mysql_query("UPDATE files SET file_access = '$status' WHERE file_id = $fileId AND file_user_id = $userId");
    }
    
    /**
    * check user's permissions for posting comments to a file
    * 
    * @param int $fileId
    */
    public function quickCheckAccess($fileId) {
      $fileId = intval($fileId);
      $access = mysql_query("SELECT file_access FROM files WHERE file_id = $fileId");
      if (mysql_num_rows($access) > 0) {
        $access = mysql_fetch_assoc($access);
        $error = $access['file_access'] == 'open' ? '' : 'You have not permission to comment this file. Please login';
      } else {
        $error = 'Bad file';
      }
      return $error;
    }
    
  }
?>
