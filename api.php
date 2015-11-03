<?php
/**
 入口文件
 */
defined('BASEDIR') || define('BASEDIR', dirname (__FILE__));
require 'lib/common.func.php';
require 'lib/defaultweixin.php';
require 'kaoqin/mine.php';

//构造函数会初始化$this->postdata
$weixin = new mine();

$weixin->run();
exit(0);