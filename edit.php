<?php
// タイムゾーン
date_default_timezone_set('Asia/Tokyo');

//DB接続情報
define('HOST', '157.112.147.201');
define('USER', 'ppftest_user');
define('PASS', 'N4xib5Xr');
define('NAME', 'ppftest_db');

// 変数の初期化
$MyBBS_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_content = array();
$content_data = array();



if (!empty($_GET['MyBBS_id']) && empty($_POST['MyBBS_id'])) { //指定投稿表示
  $MyBBS_id = (int)htmlspecialchars($_GET['MyBBS_id'], ENT_QUOTES);
  $mysqli = new mysqli(HOST, USER, PASS, NAME);

  if ($mysqli->connect_errno) {
    $error_content[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
  } else {
    $sql = "SELECT * FROM MyBBS WHERE id = $MyBBS_id";
    $res = $mysqli->query($sql);

    if ($res) {
      $content_data = $res->fetch_assoc();
    }
    $mysqli->close();
  }
} elseif (!empty($_POST['MyBBS_id'])) { //更新
  $MyBBS_id = (int)htmlspecialchars($_GET['MyBBS_id'], ENT_QUOTES);


  if (empty($_POST['poster'])) {
    $error_content[] = '表示名を入力してください。';
  } else {
    $content_data['poster'] = htmlspecialchars($_POST['poster'], ENT_QUOTES);
  }
  if (empty($_POST['content'])) {
    $error_content[] = 'メッセージを入力してください。';
  } else {
    $content_data['content'] = htmlspecialchars($_POST['content'], ENT_QUOTES);
  }


  if (empty($error_content)) {
    $mysqli = new mysqli(HOST, USER, PASS, NAME);

    if ($mysqli->connect_errno) {
      $error_content[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
    } else {
      $sql = "UPDATE MyBBS
              set poster = '$content_data[poster]',
                  content = '$content_data[content]'
              WHERE id = $MyBBS_id";
      $res = $mysqli->query($sql);
    }
    $mysqli->close();

    if ($res) {
      header("Location: ./index.php");
    }
  }
}


?>







<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MyBBS 編集</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <h1>MyBBS　編集</h1>

  <!-- エラーメッセージ -->
  <?php if (!empty($error_content)): ?>
      <ul class="error_content">
      <?php foreach($error_content as $value): ?>
              <li><?php echo $value; ?></li>
      <?php endforeach; ?>
      </ul>
  <?php endif; ?>

  <!-- 投稿フォーム -->
  <section>
    <form method="post">
      <div class="d">
        <label for="poster">投稿者</label>
        <input id="poster" type="text" name="poster"
               value="<?php if(!empty($content_data['poster'])){ echo $content_data['poster']; } ?>">
      </div>
      <div>
        <label for="content">本文</label>
        <textarea id="content" type="text" name="content"><?php if (!empty($content_data['content'])){echo$content_data['content'];} ?></textarea>
      </div>
      <a href="index.php" class="btn_cancel">キャンセル</a>
      <input type="submit" name="submit" value="更新" id="submit">
      <input type="hidden" name="MyBBS_id" value="<?php echo $content_data['id']; ?>">
    </form>
  </section>
</body>
</html>