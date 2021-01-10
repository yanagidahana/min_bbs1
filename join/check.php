<?php 
session_start();

// requireとは、外部ファイルを読み込み、実行
require('../dbconnect.php');
 
// joinに名前が入っていない場合に実行
if(!isset($_SESSION['join'])){
		// header関数で別のページに移動させる(リダイレクト処理)
    header('Location:index.php');
    exit();
}
if(!empty($_POST)){
		// dbの登録
    $statement=$db->prepare('INSERT INTO members SET
		name=?, email=?, password=?, picture=?,created=NOW()');
		
		// prepareの？に値を入れる
    echo $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        sha1($_SESSION['join']['password']),
        $_SESSION['join']['image']));
        // var_dump($_SESSION['join']);
				// exit();
				// 登録情報消去
      	// unset=joinを空にする
				unset($_SESSION['join']);
					// 完了画面にアクセス
        header('Location:thanks.php');
        exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>会員登録</title>
 
    <link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>
 
<div id="content">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
<!-- type="hidden"を指定すると、 ブラウザ上に表示されない非表示データを送信することができます。 -->
    <input type="hidden" name="action" value="submit" />
    <dl>
        <dt>ニックネーム</dt>
        <dd>
						<!-- htmlspecialchars=安全に画面出力 -->
        <?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES));?>
        </dd>
        <dt>メールアドレス</dt>
        <dd>
        <?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES));?>
        </dd>
        <dt>パスワード</dt>
        <dd>
        【表示されません】
        </dd>
        <dt>写真など</dt>
        <dd>
						<!-- joinのimageが空でなければ 
						member_pictureの中のSESSIONのjoinのimageを呼び出す
						ENT_QUOTES画面出力 -->
        <?php if($_SESSION['join'][image]!==''): ?>
            <img src="../member_picture/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES)); ?>">
            <?php endif;?>
        </dd>
    </dl>
    <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</div>
 
</div>
</body>
</html>
