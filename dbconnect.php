<?php
try {
  $db = new PDO('mysql:host=localhost;dbname=min_bbs1;charset=utf8','root','root');

} catch(PDOException $e) {
  print('DB接続エラー:' . $e->getMessage());
}
?>o
