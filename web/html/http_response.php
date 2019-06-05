<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
　<title>レスポンスタイム</title> 
</head>
<body>
  <h1>レスポンスタイム</h1>
    <form method="post">
      <input type="date" name="date">
      <select name="time">
          <option value="all">すべて</option>
<?php
for ($i=0; $i<24; $i++) {
    print("<option value='".$i."'>".$i."時台</option>");
}
?>
      </select>
      <input type="submit" value="送信">
    </form>
    <?php require('../php/get_response.php'); ?>
</body>

</html>
