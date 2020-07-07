<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>电子汇票行号查询系统</title>
<meta name="keywords" content=" 电子汇票行号，联行号,清算号，查询系统" />
<meta name="description" content="全国银行（中国）电子汇票行号查询系统为您提供最新的人行电子汇票央行系统行号码查询，供您在电子汇票服务中使用。" />
<link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<center><h1>电子汇票行号查询</h1>
<h4>数据覆盖全国所有银行网点，为您提供最新的中国人民银行电子汇票央行系统行号码查询，供您在电子汇票服务中使用。</h4>
</center>
<div style="padding:20px;color:#c00000;font-size:14px;background-color:#eeeeee;">
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
$sql = "SELECT * FROM cds WHERE (aps LIKE '%".$qb."%' OR bank LIKE '%".$qb."%') LIMIT 100";
$ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "<p>";
      echo "银行名称：". $row['bank'] . "<br>";
      echo "电子汇票行号：<b>". $row['aps'] ."</b>";
      echo "</P>";
   }
   echo "---查询完成，显示全部结果---";
   $db->close();
 }
?>
</div>
<center><form action=cds.php method=post>
<p>银行名称/电子汇票行号：<input type=text name=bank /></p>
<input type=submit value="查 询"/></form><br/>
数据更新：2020年7月3日，合计149391家银行。<br/>
<br/>
<a href="cnaps.php">联行号查询</a>&nbsp;&nbsp;<a href="cds.php">电子汇票行号查询</a>&nbsp;&nbsp;<a href="pay.php">清算行号查询</a>
<br/>
<br/>
<a href="http://b910.cn/">B910&REG;</a>提供本查询服务，数据来源中国人民银行。
<script type="text/javascript" src="https://js.users.51.la/20150533.js"></script></center>
</body>
</html>