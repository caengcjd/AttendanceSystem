<?php


$querystring="http://tjsic.tongji.edu.cn/WebServices/adminWS.asmx/checkAdmin";

$cookie_jar="pgv_pvi=4450805760; Hm_lvt_d5a76608e07e4903e91fe94d34b3cc0d=1434014983; AMAuthCookie=AQIC5wM2LY4SfcyPohZxM3Nu8MIlqFTcJQopLoz0RzULPco%3D%40AAJTSQACMDE%3D%23; amlbcookie=01";

$data="{name:'".$_GET['admin']."',key:'".$_GET['pass']."'}";

$ch=curl_init();

$urlrefer='http://tjsic.tongji.edu.cn/admin/login.htm';

curl_setopt($ch, CURLOPT_URL, $querystring);

 

curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

 
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json;charset=UTF-8', 'X-Requested-With:XMLHttpRequest','Referer:$urlrefer'));

curl_setopt($ch, CURLOPT_HEADER, false);

curl_setopt($ch, CURLOPT_COOKIE, $cookie_jar);

curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result=curl_exec($ch);

curl_close($ch);

$aa=(array)json_decode($result);

var_dump($aa);
 


















?>