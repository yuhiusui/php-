<?php
// タイムゾーン
date_default_timezone_set('Asia/Tokyo');

//DB接続情報
define('HOST', '157.112.147.201');
define('USER', 'ppftest_user');
define('PASS', 'N4xib5Xr');
define('NAME', 'ppftest_db');

// 変数の初期化
$now_date = null;
$content = array();
$content_array = array();
$success_content = null;
$error_content = array();
$clean = array();

//投稿
if (!empty($_POST['submit'])) {

//バリデーション
  if (empty($_POST['poster'])) {
    $error_content[] = '投稿者を入力してください。';
  } else {
    $clean['poster'] = htmlspecialchars($_POST['poster'], ENT_QUOTES);
  }
  if (empty($_POST['content'])) {
    $error_content[] = '本文を入力してください。';
  } else {
    $clean['content'] = htmlspecialchars( $_POST['content'], ENT_QUOTES);
  }

  if (empty($error_content)) {
    //DB接続
    $mysqli = new mysqli(HOST, USER, PASS, NAME);

    if ($mysqli->connect_errno) { //接続エラー確認
      $error_content[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
    } else {
      $mysqli->set_charset('utf8');
      $now_date = date("Y-m-d H:i:s");
      $sql = "INSERT INTO MyBBS (poster, content, created_at)
              VALUES ('$clean[poster]', '$clean[content]', '$now_date')";
      $res = $mysqli->query($sql);

      if ($res) {
        $success_content = 'メッセージを書き込みました。';
      } else {
        $error_content[] = '書き込みに失敗しました。';
      }
      $mysqli->close();
    }
  }
}


//一覧
$mysqli = new mysqli(HOST, USER, PASS, NAME);
if ($mysqli->connect_errno) { //接続エラー確認
  $error_content[] = 'データの読み込みに失敗しました。エラー番号'.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
  $sql = "SELECT id, poster, content, created_at FROM MyBBS ORDER BY created_at DESC";
  $res = $mysqli->query($sql);

  if($res){
    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
      $content_array[] = $row;
    }
  }
  $mysqli->close();
}

?>







<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MyBBS</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <h1>MyBBS</h1>

<!-- エラーメッセージ -->
  <?php if (!empty($success_content) ): ?>
    <p class="success_content"><?= $success_content; ?></p>
  <?php endif; ?>
  <?php if (!empty($error_content)): ?>
      <ul class="error_content">
      <?php foreach($error_content as $value): ?>
              <li><?= $value; ?></li>
      <?php endforeach; ?>
      </ul>
  <?php endif; ?>



<!-- 一覧表示 -->
  <section class="scroll-box">
    <?php if (!empty($content_array)): ?>
      <?php foreach ($content_array as $value): ?>
        <article>
          <div class="info underline">
            <h2><?= $value['poster']; ?></h2>
            <time><?= date('Y年m月d日 H:i', strtotime($value['created_at'])); ?></time>
            <p><a href="edit.php?MyBBS_id=<?= $value['id']; ?>">編集</a>&nbsp;&nbsp;<a href="delete.php?MyBBS_id=<?= $value['id']; ?>">削除</a></p>
          </div>
          <p><?= nl2br($value['content']); ?></p>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <hr>

<!-- 投稿フォーム -->
  <section>
    <form method="post">
      <div class="d">
        <label for="poster">投稿者</label>
        <input id="poster" type="text" name="poster">
      </div>
      <div>
        <label for="content">本文</label>
        <textarea id="content" type="text" name="content"></textarea>
      </div>
      <input type="submit" name="submit" value="投稿" id="submit">
    </form>
  </section>
</body>
</html>