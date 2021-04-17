<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>天竺鼠購物車車 - 表單</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
  session_start();

  // $item 是模擬資料庫的內容
  $items = [
    "被褥" => "2000",
    "床單" => "1350",
    "被套" => "2120",
    "床罩" => "600",
    "炒鍋" => "1800",
    "煎鍋" => "2100",
    "蒸鍋" => "1850",
    "奶鍋" => "1380",
    "電腦" => "48000",
    "冰箱" => "68000",
    "微波爐" => "3100",
    "電磁爐" => "1900",
    "豆漿機" => "1650",
    "榨汁機" => "2350",
    "洗衣機" => "38000",
    "浴霸" => "4500",
    "電熱水器" => "8300",
    "檯燈" => "350",
    "臉盆" => "90",
    "杯子" => "15",
    "牙刷(4隻)" => "200",
    "熱水器" => "700",
    "電飯鍋" => "3600"
  ];

  // 產生 csrf token
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;
  // 取得當前小時(24小時)
  $now_hour = date('H');

  // 設定折扣，預設沒打折
  $discount = 1.00;

  // 0 ~ 12 點
  if ($now_hour >= 0 && $now_hour <= 12) {
    $discount = 0.85; // 打85折

    // 13 ~ 20 點
  } elseif ($now_hour >= 13 && $now_hour <= 20) {
    $discount = 0.9; // 打9折

    // 剩下的時間 21 ~ 23 點
  } else {
    $discount = 0.8; // 打8折
  }

  ?>
  <header class="header">
    <h2>天竺鼠車車活動商場<br>全館活動折扣中</h2>
  </header>
  <div class="marquee">
    <marquee>全日 00 ~ 12 時：全館打 85 折。全日 13 ~ 20 時：全館打 9 折。全日 21 ~ 23 時：全館打 8 折。</marquee>
  </div>
  <div class="time">現在時間：<?php print date('m月d日 H點i分') ?></div>
  <div class="main">
    <form action="cart.php" method="POST">
      <table>
        <thead>
          <td>品名</td>
          <td>單價</td>
          <td>數量</td>
        </thead>
        <!-- 將商品透過 foreach 列印出來 -->
        <?php foreach ($items as $name => $price) : ?>
          <tr>
            <td class="item-name"><?php print $name ?></td>
            <td class="item-price">
              <span class="origin-price"><?php print $price ?></span> →
              <span class="discount-price"><?php print round($price * $discount) ?></span>
              <!-- round 將小數點位數四捨五入至整數 -->
              <span class="discount">
                (<?php print str_replace('0', '', intval($discount * 100)) ?>折)
                <!-- str_replace 將折扣的 0 位數去除 -->
                <!-- intval 將內容轉為整數型態 -->
              </span>
            </td>
            <td class="item-quantity">
              <input type="number" name="<?php print $name ?>" value="0" min="0" step="1">
            </td>
          </tr>
        <?php endforeach ?>
        <tr>
          <td colspan="3">
            <input type="hidden" name="csrf_token" value="<?php print $csrf_token ?>">
            <input type="submit" value="購買" class="mysubmit">
          </td>
        </tr>
      </table>
    </form>
  </div>




</body>

</html>