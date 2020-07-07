<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>联行号查询-大额支付行号查询-CNAPS查询-开户行号查询</title>
<meta name="keywords" content=" 联行号查询,大额支付行号查询,CNAPS查询,开户银行行号查询" />
<meta name="description" content="全国银行网点：联行号查询,大额支付行号查询,CNAPS查询,开户银行行号查询。" />
<link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<center><h1>联行号查询</h1>
<h4>全国银行网点：联行号查询-大额支付行号查询-CNAPS查询-银行开户行号查询</h4>
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
$sql = "SELECT * FROM aps WHERE (aps LIKE '%".$qb."%' OR bank LIKE '%".$qb."%') LIMIT 100";
$ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "<p>";
      echo "银行名称：". $row['bank'] . "<br>";
      echo "联行号码：<b>". $row['aps'] ."</b>\n";
      echo "</P>";
   }
   echo "---查询完成，显示全部结果---";
   $db->close();
 }
?>
</div>
<center><form action=cnaps.php method=post>
<p>银行名称/联行号：<input type=text name=bank /></p>
<input type=submit value="查 询"/></form><br/>
数据更新：2020年7月3日，合计149391家银行。<br/>提示：部分银行网点搬迁，信息未及时入库，请沿用原支行名称查询！<br/>
银行联行号是一个地区银行的唯一识别标志，用于人民银行所组织的跨区域支付结算业务。它是由12位组成：3位银行代码+4位城市代码+4位银行编号+1位校验位。<br/><br/>
<a href="cnaps.php">联行号查询</a>&nbsp;&nbsp;<a href="cds.php">电子汇票行号查询</a>&nbsp;&nbsp;<a href="pay.php">清算行号查询</a>
<br/>
<br/>
<a href="http://b910.cn/">B910&REG;</a>提供本查询服务，数据来源中国人民银行。
<script type="text/javascript" src="https://js.users.51.la/20150533.js"></script></center>

</body>
</html>