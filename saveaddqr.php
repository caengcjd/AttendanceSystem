<?php


$xuankebiaoid = isset($_POST['xuankebiaoid'])?  floatval($_POST['xuankebiaoid']):0;
$sceneid = isset($_POST['sceneid'])?intval($_POST['sceneid']):1;
require "lib/weixin.class.php";
$ticket = weixin::getQrcodeTicket($sceneid,0);//生成二维码ticket
echo " ticket   is  the ".$ticket;
$imgbin = weixin::getQrcodeImgByTicket($ticket);//用ticket去换二维码图片
//以下代码将图片保存到SaeStorage
$storage = new SaeStorage();
$domain = 'weixin';
$destFileName = md5(time()).'.jpg';
$attr = array('encoding'=>'gzip');
$imgurl = $storage->write($domain,$destFileName, $imgbin, -1, $attr, true);
if(!$imgurl){
    exit('error');
}
include_once 'model/SaeDB.class.php';
$mysql = SaeDB::getInstance();
if(!$xuankebiaoid || !$sceneid){
    header("Location:addqr.php?msg=emptyparams");
}
$sql = "INSERT INTO `qiandao_qrcode` (`id`, `xuankebiaoid`, `scene_id`,`qrcode`) VALUES (NULL, '{$xuankebiaoid}', '{$sceneid}','{$imgurl}');";
$mysql->runSql($sql);
if ($mysql->errno() != 0)
{
    die("Error:" . $mysql->errmsg());
}
$mysql->closeDb();
?>
<body>
<div class="qrcontainer">
    <p>您的二维码已经生成了</p>
<img class="qrimg" src="<?php echo $imgurl;?>"/>
<span><a href="qrlist.php">返回列表</a></span>
</div>
</body>
</html>