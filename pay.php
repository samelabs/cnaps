<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>清算行号查询</title>
<meta name="keywords" content=" 清算行号查询" />
<meta name="description" content="全国银行（中国）清算行号查询" />
<link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<center><h1>全国银行清算行号查询</h1>
<h4>为您提供最新的人行网上支付跨行清算参与机构码查询，供您进行跨行速汇、他行查询签约、他行扣款签约等功能的跨行交易时使用。</h4>
</center>
<div style="padding:20px;color:#336600;font-size:14px;background-color:#eeeeee;">
<?php
class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('B910cn.db3');
      }
   }
$qb=$_POST["bank"];
if(!empty($qb)){
   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   }
$sql = "SELECT * FROM pay WHERE (aps LIKE '%".$qb."%' OR bank LIKE '%".$qb."%') LIMIT 100";
$ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "<p>";
      echo "银行名称：". $row['bank'] . "<br>";
      echo "清算行号：<b>". $row['aps'] ."</b>";
      echo "</P>";
   }
   echo "---查询完成，显示全部结果---";
   $db->close();
 }
?>
</div>
<center><form action=pay.php method=post>
<p>银行名称/清算行号：<input type=text name=bank /></p>
<input type=submit value="查 询"/></form><br/><br/>
数据更新：2020年7月3日<br/>

<a href="cnaps.php">联行号查询</a>&nbsp;&nbsp;<a href="cds.php">电子汇票行号查询</a>&nbsp;&nbsp;<a href="pay.php">清算行号查询</a>
<br/>
<br/>
<a href="http://b910.cn/">B910&REG;</a>提供本查询服务，数据来源中国人民银行。
<script type="text/javascript" src="https://js.users.51.la/20150533.js"></script></center>
</body>
</html>