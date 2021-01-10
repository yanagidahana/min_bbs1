<?php
 
session_start();
 
require('../dbconnect.php');
 
// empty=空
// $_POST(ポスト変数)を利用することで、HTML入力フォームの値を受信して処理することが出来ます
if(!empty($_POST)) {
 
if ($_POST['name'] === '') {
$error['name'] = 'blank';
}
 
if ($_POST['email'] === '') {
$error['email'] = 'blank';
}
 
 
if (strlen($_POST['password']) < 4) {
$error['password'] = 'length';
}
 
if ($_POST['password'] === '') {
$error['password'] = 'blank';
}
 
// if (!empty($fileName))=ファイルのアップロードがあるばあいは
// $ext = substr($fileName, -3);＝ファイルの後ろ３文字。（拡張子）
// jpg、gif、pngの場合認識。それ以外はtypeエラーを出す
$fileName = $_FILES['image']['name'];
if (!empty($fileName)) {
$ext = substr($fileName, -3);
if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
$error['image'] = 'type';
}
}
 
 
// 画像のアップロード
// ['tmp_name']一時的にアップロード。（消える可能性あり）
// $_FILES['image']['tmp_name']＝写真が今ある場所
// member_picture/' . $image＝移動先
// $_SESSION['join']['image'] = $image;=確認画面に移動
if (empty($error)) {
$image = date('YmdHis') . $_FILES['image']['name'];
move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/' . $image);
$_SESSION['join'] = $_POST;
$_SESSION['join']['image'] = $image;
header('Location: check.php');
exit();
}
}
 
// 'action'='rewrite'をURLで送られた時は書き直しを意味する
// if ($_REQUEST['action'] == 'rewrite') {= urlパラメーターにリンクがついていれば
// $_SESSION= サイトを訪れたユーザデータを個別に管理できます。
// && isset($_SESSION['join']＝セッションが正しく入力されている時のみ
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
$_POST = $_SESSION['join'];
}
 
 
// アカウントの重複チェック
// SELECT COUNT(*)=件数が何件か
// AS ＝取得
// cntと言うショートカットに格納
// FROM members＝メンバーズテーブルから取得
// WHERE email=?を？で絞り取る
// if ($record['cut'])=0か1か入る
// $member->execute(array($_POST['email']));=メールアドレスのメンバーがいるか
if(empty($error)) {
$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
$member->execute(array($_POST['email']));
$record = $member->fetch();
if ($record['cut'] > 0){
$error['email'] = 'duplicate';
}
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
<p>次のフォームに必要事項をご記入ください。</p>
 
<!-- ↓＝決まり文。フィルをアップロードする。 -->
<form action="" method="post" enctype="multipart/form-data">
<dl>
<dt>ニックネーム<span class="required">必須</span></dt>
<dd>
<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
<?php if ($error['name'] === 'blank'): ?>
<p class="error">ニックネームを入力してください</p>
<?php endif; ?>
</dd>
 
 
<dt>メールアドレス<span class="required">必須</span></dt>
<dd>
<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
<?php if ($error['email'] === 'blank'): ?>
<p class="error">メールアドレスを入力してください</p>
<?php endif; ?>
 
<?php if ($error['email'] === 'duplicate'): ?>
<p class="error">指定されたメールアドレスは既に登録されています</p>
<?php endif; ?>
 
 
 
<dt>パスワード<span class="required">必須</span></dt>
<dd>
<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
 
<?php if ($error['password'] === 'length'): ?>
<p class="error">パスワードは４文字以上で書いてください</p>
<?php endif; ?>
 
<?php if ($error['password'] === 'blank'): ?>
<p class="error">パスワードを入力してください</p>
<?php endif; ?>
 
 
</dd>
 
 
<dt>写真など</dt>
<dd>
<input type="file" name="image" size="35" value="test" />
 
<?php if ($error['image'] === 'type'): ?>
<p class="error">写真などは「.gif」「.jpg」「.png」の画像を指定してください</p>
<?php endif; ?>
<?php if (!empty($error)): ?>
<p class="error">恐れ入りますが、画像を改めて指定してください</p>
<?php endif; ?>
 
 
 
</dd>
</dl>
<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
 

 
 
<?php
session_start();
 
// requireとは、外部ファイルを読み込み、実行
require('../dbconnect.php');
 
// joinに名前が入っていない場合に実行
if (!isset($_SESSION['join'])) {
// header関数で別のページに移動させる(リダイレクト処理)
header('Location: index.php');
exit();
}
 
 
if (!empty($_POST)) {
// dbの登録
$statement = $db->prepare('INSERT INTO members SET
name=?, email=?, password=?, picture=?, created=NOW
()');
 
// prepareの？に値を入れる
$statement->execute(array(
$_SESSION['join']['name'],
$_SESSION['join']['email'],
$_SESSION['join']['password'],
sha1($_SESSION['join']['password']),
$_SESSION['join']['image']
));
 
// 登録情報消去
// unset=joinを空にする
unset($_SESSION['join']);
 
// 完了画面にアクセス
header('Location: thanks.php');
exit();
}
?>


