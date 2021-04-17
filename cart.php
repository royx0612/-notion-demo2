<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>天竺鼠車車活動商場 - 處理器</title>
</head>

<body>
  <?php
  // 開啟 session
  session_start();
  // print_r($_POST);
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
  // 測試驗證 token
  // printf("
  //   <p>session token:%s</p>
  //   <p>form token:%s</p>
  // ", $_SESSION['csrf_token'], $_POST['csrf_token']);

  ### 先驗證 csrf_token，確認合法來源
  if (
    empty($_SESSION['csrf_token']) || // session 沒有 csrf_token 
    empty($_POST['csrf_token']) || //表單沒有沒有 csrf_token
    $_SESSION['csrf_token'] != $_POST['csrf_token'] //兩者比對不相符
  ) {
    // 刪除 session 中的 csrf_token
    unset($_SESSION['csrf_token']);

    // 程式終止，並顯示訊息
    die('CSRF Token 驗證失敗');
  }

  ### 驗證資料是否合法
  foreach ($_POST as $key => $value) {
    // 將 $value 轉為 數字型態在做驗證
    $value = intval($value);

    // 排除 csrf_token 的內容
    if ($key == 'csrf_token') continue;

    ## 檢查 $key 是否有不再資料庫中的項目
    if (!in_array($key, array_keys($items))) {  // 如果送過來的表單項目名稱($key) 不再 $item 陣列內
      ## 1.無效項目，刪除內容
      unset($_POST[$key]);

      // ## 2.或因為有額外的輸入，判定這次表單驗證失效
      // // 刪除 session 中的 csrf_token
      // unset($_SESSION['csrf_token']);

      // // 程式終止，並顯示訊息
      // die('表單中有無法辨認的項目');
    }

    ## 檢查 value 是否有 負數、小數點、非數字以外的內容
    if (!is_int($value) || $value < 0) { // 內容非法
      ## 判定這次表單驗證失效，提示訊息並返回上一頁
      $msg = "
        <h2>購項商品數量有誤，請重新操作。</h2>
        <a href='index.php'>返回上一頁</a>
      ";
      die($msg);
    }
  }

  ### 統計項目、數量、金額放入 $cart_list
  $cart_list = []; // 購買清單
  $total_price = 0; // 所有商品總價
  foreach ($_POST as $key => $value) {
    // 排除 csrf_token 的內容
    if ($key == 'csrf_token') continue;

    // 將數量大於 0 的商品
    if ($value > 0) {
      ## 設定折扣
      $now_hour = date('H'); // 取得當前小時(24小時)
      $discount = 1.00; // 預設沒打折
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

      ## 加入購買清單 $cart_list，裡面放入【品名】、【數量】、【原始單價】、【打折後的單價】
      // 設定單價變數(折扣後)，有小數點四捨五入
      $discount_price = round($items[$key] * $discount); 

      // 總金額加總：每次迴圈商品金額加入變數內 
      $total_price += ($discount_price * $value); 

      // 新增至 $cart_list變數
      $cart_list[] = [
        "name" => $key, // 名稱
        "count" => $value, // 數量
        "origin_price" => $items[$key], // 原始價格
        "discount_price" => $discount_price, // 折購後價格
        "item_price" => $discount_price* $value // 商品金額
      ];
    }
  }

  ?>
  <header class="header">
    <h2>天竺鼠車車活動商場<br>購物車清單</h2>
  </header>
  <div class="marquee">
    <marquee>全日 00 ~ 12 時：全館打 85 折。全日 13 ~ 20 時：全館打 9 折。全日 21 ~ 23 時：全館打 8 折。</marquee>
  </div>
  <a href="index.php" class="back-to-page">回購買頁面</a>
  <div class="time">購買項目</div>
  <div class="main">
    <table>
      <thead>
        <tr>
          <td>名稱</td>
          <td>單價</td>
          <td>數量</td>
          <td>金額</td>
        </tr>
      </thead>
      <tbody>
        <!-- 將 $cart_list 的商品列印出來 -->
        <?php foreach ($cart_list as $item) : ?>
          <tr>
            <!-- 商品名稱 -->
            <td class="item-name"><?php print $item['name'] ?></td>
            <!-- 商品單價，會顯示原價及折扣後的價格 -->
            <td class="item-price">
              <span class="origin-price"><?php print $item['origin_price'] ?></span> →
              <span class="discount-price"><?php print $item['discount_price'] ?></span>
              <!-- round 將小數點位數四捨五入至整數 -->
              <span class="discount">
                (<?php print str_replace('0', '', intval($discount * 100)) ?>折)
                <!-- str_replace 將折扣的 0 位數去除 -->
                <!-- intval 將內容轉為整數型態 -->
              </span>
            </td>
            <!-- 購買數量 -->
            <td class="item-quantity"><?php print $item['count'] ?></td>
            <!-- 商品總價 -->
            <td class="item-total-price">
              <span class="origin-price"><?php print ($item['origin_price'] * $item['count']) ?></span> →
              <span class="discount-price"><?php print $item['item_price'] ?></span>
            </td>
          </tr>
        <?php endforeach ?>
        <tr>
          <!-- 以下列印總價金額 -->
          <td colspan="4" class="total-price">購買金額 $<?php print number_format($total_price) ?></td>
        </tr>
      </tbody>
    </table>
  </div>

</body>

</html>