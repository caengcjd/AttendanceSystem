<?php

require_once libfile('lib/weixin.class.php');
//require_once libfile('model/SendMsgDB.php');

class   mine  extends  weixin
{
	  
	  private $message_bind='请发送学号or工号进行绑定微信号';
	  private $message_num='请发送数字信息进行选择.a:发送选课表id进行签到;b:查询个人信息;c:修改信息;d:查询课表信息;e:解除绑定';
	  private $message_bind_succeed='绑定成功了!';
	  private $message_bind_failed='绑定失败了!';
	  private $message_tea="请发送数字信息进行选择.a:发送选课表ID查询未来签到的人;\nb:查询信息;\nc:修改信息\nd:解除绑定";
	  private $message_scan_error='扫描签到失败';
	  private $message_scan_success='扫描签到成功';
	  
	 public function beforeProcess($postData)
	  {
	      return true;
	  }
	  
	  
	  public function processRequest($data) {
	      
	      $this->traceHttp();
	      $this->ProcessMessage($data);
	  }
	  
	  
	  
	  
	  
	  function ProcessMessage($data)
    {
    	
        
        
        
        if($this->isTextMsg()){
    	
        
        $num=$this->getRoleByOpenid($data->FromUserName);
    	
        
        switch($num){
    	    
    	    case '2'://教师
              $this->teacherInfoProcess($data);
              break;
    	        
    	    case '1'://学生
    	    
    	      $this->stuInfoProcess($data);
    	      break;
    	    
    	    default://还没有绑定,请输入信息进行绑定(这一步不知道情况的啊)
    	        $this->bind($data);
    	        
    	    
    	}//switch
    	
    	
    	
     
    	
        
        
        }//if isTextMsg()
        else if($this->isLocationMsg()){
           
           
       }
       else if($this->isEventMsg())
       {
          
          switch($data->Event)
          {
             
              case  'subscribe':
                 if(!empty($data->EventKey)){
                 
                    $this->outputText('信息有误!');
                     
                 }else {
                     
                     
                     $this->outputText('您好，欢迎关注武科大签到系统!');
                 }
                 break;
              case 'unsubscribe':
                  $this->outputText('用户取消订阅了的');
                  break;
              case 'SCAN':
                  
                  // $this->outputText("key 值 是".$data->EventKey);
                 //  $this->scan_qiandao($data);

                   $num=$this->getRoleByOpenid($data->FromUserName);
                    
                   
                   switch($num){
                       	
                       case '2'://教师
                          $this->outputText("您是教师,扫描二维码无效");
                           break;
                            
                       case '1'://学生
                           	
                           $this->scan_qiandao($data);
                           break;
                           	
                       default://还没有绑定,请输入信息进行绑定（这一步不知道情况的啊）
                           $this->outputText("请先发送消息绑定学号\n".$this->message_bind);
                            
                           	
                   }//switch
                      
                   
                   
                  
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                  
              default:
         
              
              
              
              
              
              
              
              
              
              
              
          }//switch
           
           
           
           
           
           
       }
       else{
           
       }
    	
    }
	
	
      function   getIdbyScene_id($scene_id){

          $mysql = new SaeMysql();
          $sql = "select  xuankebiaoid  from  qiandao_qrcode  where scene_id=$scene_id";
          $id=$mysql->getVar($sql);
          
          if ($mysql->errno() != 0)
          {
              die("Error:".$mysql->errmsg().$sql);
          }
          $mysql->closeDb();
          return $id;
          
         
          
          
      }
      
    
    
	  function  getRoleByOpenid($id){
	  	 
	  	 
	  	$mysql = new SaeMysql();
        $sql = "select  role  from  weixinbiao  where  openid='$id'";
        $num=$mysql->getVar($sql);

        if ($mysql->errno() != 0)
        {
            die("Error:".$mysql->errmsg().$sql);
        }
        $mysql->closeDb();
        return $num;
	  	
	  	
	  }
	  
	  
	 
	   
	  public  function  getINfo($data){

	      $mysql = new SaeMysql();
	      
	      $sql="select  a.*  from  xuesheng  a,weixinbiao b   where  a.id=b.id and b.openid='".$data->FromUserName."'";
	       
	      $data=$mysql->getLine($sql);
	      
	      if ($mysql->errno() != 0)
	      {
	           
	          die("Error:".$mysql->errmsg());
	          
	      }
	       
	     $content='学号 :'.$data['id']."\n".
	              '姓名:'.$data['name']."\n".
	              '性别:'.$data['sex']."\n".
	              '电话:'.$data['phone']."\n".
	              '班级:'.$data['banjiid']."\n".
	              '学院:'.$data['xueyuanid'];
	      
	     
	     $this->outputText($content);
	     
	 
	      
	      
	      
	      
	  }
	  
	  
	  function  del_relation($data){
	      
	      $mysql = new SaeMysql();
	      
	      $sql = "delete from  weixinbiao  where  openid='$data->FromUserName'";
	      
	      $mysql->runSql($sql);
	      
	       
	       
	      if ($mysql->errno() != 0)
	      {
	          die("Error:".$mysql->errmsg().$sql);
	          return   0;
	      
	      
	      }
	      $mysql->closeDb();
	      
	      
	      $this->outputText('解除绑定成功!');
	      
	      
	      
	      
	      
	      
	      
	  }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  

	  function  scan_qiandao($data)
	  {
	      //扫描二维码签到和发送信息签到的形式是不一样的啊
	  
	      $mysql = new SaeMysql();
	       
	      $id=$this->getStuidByOpenid($data->FromUserName);
	       
	       
	  
	       
	      // id 表id	xueshengid 学生id	xuankebiaoid 选课表id	kaoqin 学生考勤（正常、迟到、早退、旷课）
	      $time=date("H:i:s", time());
	      $xuankebiaoid=$this->getIdbyScene_id($data->EventKey);
	       
	      //检查是否选修过次课
	  
	      if(!($is=$this->checkXuanke($id, $xuankebiaoid))){
	  
	          $this->outputText($this->message_scan_error."\n对不起，你并未选修此门课程");
	          exit(0);
	  
	      }
	       
	       
	       
	       
	       
	      $starttime=$this->getStarttimeByid($xuankebiaoid);
	      $aled=strtotime($time)-strtotime($starttime);
	      $aled=round($aled/60);//换算成分钟
	      if($aled<0&&$aled>-10){
	           
	          $content='签到成功';
	          $kaoqin='正常';
	           
	      }else if($aled<-10){
	           
	          $content='对不起你不能提前签到';
	          $this->outputText($this->message_scan_error."\n".$content);
	          exit(0);
	           
	           
	      }else if(0<$aled&&$aled<10){
	           
	           
	          $content='签到成功,您已迟到'.$aled.'分钟';
	          $kaoqin='迟到';
	           
	      }else if($aled>10&&$aled<45){
	           
	           
	  
	          $content='签到成功,您已旷课'.$aled.'分钟';
	          $kaoqin='旷课';
	           
	      }else{
	           
	          $content='你已经旷课'.$aled.'分钟,请扫描其它二维码';
	          $kaoqin='旷课';
	           
	      }
	       
	      //先判断是否本节课已经签到了的
	      $num=$this->checkQiandao($id,$xuankebiaoid);
	      if($num){
	           
	          $this->outputText('对不起,本节课你已经签到过了');
	          exit(0);
	           
	      }
	      $sql = "insert into  kaoqin(xueshengid,xuankebiaoid,kaoqin)  values('$id','$xuankebiaoid','$kaoqin')";
	       
	      $mysql->runSql($sql);
	       
	      if ($mysql->errno() != 0)
	      {
	  
	          die("Error:".$mysql->errmsg());
	           
	      }
	      $mysql->closeDb();
	       
	      $this->outputText($this->message_scan_success."\n".$content);
	       
	       
	       
	       
	       
	       
	  }
	  
	  function  qiandao($data)
	  {
	      //扫描二维码签到和发送信息签到的形式是不一样的啊
      
	      $mysql = new SaeMysql();
	      
	      $id=$this->getStuidByOpenid($data->FromUserName);
	      
	      
	     
	      
	     // id 表id	xueshengid 学生id	xuankebiaoid 选课表id	kaoqin 学生考勤（正常、迟到、早退、旷课）
	      $time=date("H:i:s", time());
	      $xuankebiaoid=intval($data->Content);
	      
	     //检查是否选修过次课
	     
	     if(!($is=$this->checkXuanke($id, $xuankebiaoid))){
	         
	         $this->outputText('对不起，你并未选修此门课程');
	         exit(0);
	         
	     }
	      
	      
	      
	      
	      
	      $starttime=$this->getStarttimeByid($xuankebiaoid);
	      $aled=strtotime($time)-strtotime($starttime);
	      $aled=round($aled/60);//换算成分钟
	      if($aled<0&&$aled>-10){
	          
	         $content='签到成功';
	         $kaoqin='正常';
	          
	      }else if($aled<-10){
	          
	         $content='对不起你不能提前签到';
	         $this->outputText($content);
	         exit(0);
	      
	      
	      }else if(0<$aled&&$aled<10){
	          
	          
	          $content='签到成功,您已迟到'.$aled.'分钟';
	          $kaoqin='迟到';
	          
	      }else if($aled>10&&$aled<45){
	          
	          
	         
	          $content='签到成功,您已旷课'.$aled.'分钟';
	          $kaoqin='旷课';
	          
	      }else{
	          
	          $content='你已经旷课'.$aled.'分钟,请扫描其它二维码';
	          $kaoqin='旷课'; 
	          
	      }
	      
	      //先判断是否本节课已经签到了的
	      $num=$this->checkQiandao($id,$xuankebiaoid);
	      if($num){
	          
	          $this->outputText('对不起,本节课你已经签到过了');
	          exit(0);
	          
	      }
	      $sql = "insert into  kaoqin(xueshengid,xuankebiaoid,kaoqin)  values('$id','$xuankebiaoid','$kaoqin')";
	      
	      $mysql->runSql($sql);
	      
	      if ($mysql->errno() != 0)
	      {
	           
	          die("Error:".$mysql->errmsg());
	          
	      }
	      $mysql->closeDb();
	      
	      $this->outputText($content);
	      
	      
	      
	      
	      
	      
	  } 
	  
	  
	  
	  
	   
	 function  bind($data){
	  	  
	  	  //正则验证貌似还不正确的

	     $regex = '/^[0-9]*?$/i';
	     
	     
	     if(preg_match($regex, $data->Content, $matches))
	     {
	         
	       $num=$this->checkUserRole($data->Content);
	       switch($num)
	       {
	         
	           case '1':$this->saveStuInfo($data->Content,$data->FromUserName);$ans='绑定学生学号成功';break;
	           case '2':$this->saveTeacherInfo($data->Content,$data->FromUserName);$ans='绑定教师工号成功';break;
	     
	           default:$ans='数据库内查无此账号信息';
	       
	       
	       
	       }//switch
	       
	         $this->outputText($ans);
	     }else
	     {
	         
	        $this->outputText("输入信息格式不正确\n".$this->message_bind);
	        
	        
	     }
	     
	  	  
       
	  	  
	  	
	  }//bind
	
	  
	  function  teacherInfoProcess($data){
	      
	      $tmp=$data->Content;

	      $tea_id=$this->getTeaidByOpenid($data->FromUserName);
	      $regex1 = '/^a([0-9]*?)$/i';
	      $regex2='/^\x{59d3}\x{540d}\:([\x{4e00}-\x{9fa5}]{2,3})$/u';//修改姓名
	      $regex3='/^\x{7535}\x{8bdd}\:([0-9]*?)$/u';
	      
	      if(preg_match($regex1, $tmp, $matches)){
	      
	       
	    
	       $this->getUncheckedList($matches[1],$tea_id);
	          
	          
	          
	      }
	      else if($tmp=='b'){

	       $this->getTeacherInfo($data->FromUserName);
	          
	      }else if($tmp=='c')
	      {
	       
	       $this->outputText("请发送姓名:your name或者电话:your tel进行修改\n");
	      
	     
	      
	      }else if($tmp=='d'){
	          
	       $this->del_relation($data);   
	          
	          
	      }
	      else if(preg_match($regex2, $tmp, $matches)){//修改姓名
	          
	         
	         $ans=$this->modify('name',$matches[1],$tea_id,'jiaoshi');
	         $ans?$this->getTeacherInfo($data->FromUserName):$this->outputText('修改信息失败!');
	          
	          
	          
	      }else if(preg_match($regex3, $tmp, $matches)){
	          
	      
	          $ans=$this->modify('phone',$matches[1],$tea_id,'jiaoshi');
	          $ans?$this->getTeacherInfo($data->FromUserName):$this->outputText('修改信息失败!');
	      
	      }else{
	          
	          
	        $this->outputText($this->message_tea);  
	          
	          
	      }
	      
	  }
	  
	  function  modify($key,$value,$id,$table){
	      

	      $mysql = new SaeMysql();
	       
	      $sql = "update `$table` set `$key`='$value'  where `id`='$id'";
	       
	      $mysql->runSql($sql);
	       
	      
	      
	      if ($mysql->errno() != 0)
	      {
	          die("Error:".$mysql->errmsg().$sql);
	          return   0;
	         
	           
	      }
	      $mysql->closeDb();
	       
	       
	       return 1 ;
	      
	      
	      
	      
	      
	      
	      
	      
	  }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  function  getTeacherInfo($openid){
	      
	      
	      
	      $mysql = new SaeMysql();
	       
	      $sql="select  a.*  from  jiaoshi a,weixinbiao b   where  a.id=b.id and b.openid='".$openid."'";
	      
	      $data=$mysql->getLine($sql);
	       
	      if ($mysql->errno() != 0)
	      {
	      
	          die("Error:".$mysql->errmsg().$sql);
	           
	      }
	      
	      $content='工号 :'.$data['id']."\n".
	          '姓名:'.$data['name']."\n".
	          '性别:'.$data['sex']."\n".
	          '电话:'.$data['phone']."\n".
	          '职称:'.$data['zhicheng']."\n".
	          '学院:'.$data['xueyuanid'];
	       
	      
	      $this->outputText($content);
	      
	      
	    }
	    
	    
	    function  saveTeacherInfo($t_id,$openid)
	    {
	        $mysql = new SaeMysql();
	    
	        $sql = "insert into   weixinbiao(openid,id,role)  values('$openid','$t_id','2')";
	    
	        $mysql->runSql($sql);
	    
	        if ($mysql->errno() != 0)
	        {
	             
	            // die("Error:".$mysql->errmsg());
	            return 0;
	        }
	        $mysql->closeDb();
	    
	        return  1;
	    
	    
	    }
	  
	  
	  
	  function getUnchecked($kechengid,$jiaoshiid){
	      
	     
	    $mysql = new SaeMysql();
	    
	    //先查看有没有授予此课
	    $sql="SELECT id  FROM  xuankebiao  WHERE   jiaoshiid='$jiaoshiid' and kechengid='$kechengid'";
        
	    $ans=$mysql->getLine($sql);
	    if ($mysql->errno() != 0)
	    {
	    
	         die("Error:".$mysql->errmsg());
	        
	    }
	     
	    if(!$ans){$this->outputText('您并未教授此课!');exit(0);}
	    
	    
	    $sql="SELECT  xueshengid  FROM xuankebiao  WHERE xueshengid NOT IN (SELECT xueshengid  FROM  kaoqin  where   xuankebiaoid='$xuankebiaoid') and jiaoshiid='$jiaoshiid' and kechengid='$kechengid' ";
	      
        $data=$mysql->get_data($sql);
	    if ($mysql->errno() != 0)
	    {
	         
	        die("Error:".$mysql->errmsg());
	       
	    }
	    $mysql->closeDb();
	    
	    $ans=array();
	    
	    foreach ($data  as  $key){
	        
	      $ans[]="'".$key['xueshengid']."'";  
	    }
	     
	    return $ans?$ans:false;
	    
	  
	      
	    }
	    
	    
	    
	    
	    function   getUncheckedlist($kechengid,$jiaoshiid){
	        
	     $mysql = new SaeMysql();
	        
	     $list=$this->getUnchecked($kechengid, $jiaoshiid);
	        
	     if($list)
	     {
	         
	       $list=implode(',',$list);
	       
	       $sql="select  id ,name,sex,phone,banjiid,xueyuanid  from xuesheng where id in (".$list.')';
	       
	       $num=$mysql->getData($sql);
	        
	       if ($mysql->errno() != 0)
	       {
	           die("Error:".$mysql->errmsg().var_dump($list).$sql);
	       }
	       $mysql->closeDb();
	       
	       
	       $ans="课程".$kechengid."未来签到的学生:\n";
	       
	       foreach ((array)$num  as  $key)
	       {
	           
	         
	          $ans.="学号:".$key['id'].',姓名:'.$key['name'].',性别:'.$key['sex'].',电话:'.$key['phone'].',班级ID:'.$key['banjiid']."\n";
	           
	       }
	       
	        $this->outputText($ans);
	       
	        
	       
	       
	         
	       
	         
	         
	         
	     }else{
	         
	         
	        $this->outputText('全部学生已经签到了的 !'); 
	         
	     }
	        
	        
	        
	        
	        
	        
	        
	    }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  function  stuInfoProcess($data)
	  {
	      
	      
	      $tmp=$data->Content;
	      $regex1 = '/^[0-9]*?$/i';
	      $regex2='/^\x{59d3}\x{540d}\:([\x{4e00}-\x{9fa5}]{1,4})$/u';//修改姓名
	      $regex3='/^\x{7535}\x{8bdd}\:([0-9]*?)$/u';
	      $stu_id=$this->getStuidByOpenid($data->FromUserName);
	      
	      
	      if($tmp=='a')
	      {
	          $this->outputText('请输入 课程ID');
	      
	      }else if(preg_match($regex1, $tmp, $matches)){
	           
	       $this->qiandao($data);
	      
	           
	      
	      }else if($tmp=='b'){
	      
	          $this->getInfo($data);
	           
	      }else if($tmp=='c')
	      {
	      
	          $this->outputText("请发送姓名:your name或者电话:your tel进行修改\n");
	           
	      
	           
	      }else if($tmp=='d'){
	          
	      
	          $content=$this->getClassInfo($stu_id);
	          
	          $ans="您的课程信息:\n";
	          
	          foreach($content as  $keys){
	              
	              
	          $ans.='课程名:'.$keys['name']."\n课程ID:".$keys['id']."\n上课地点:".$keys['dizhi']
	          ."\n上课时间:".$keys['starttime'].'~'.$keys['endtime']."\n";
	              
	          }	          
	           
	          $this->outputText($ans);
	      
	      
	      
	      }else if($tmp=='e'){
	          
	          $this->del_relation($data);
	          
	      }
	      else if(preg_match($regex2, $tmp, $matches)){//修改姓名
	           
	      
	          $ans=$this->modify('name',$matches[1],$stu_id,'xuesheng');
	          $ans?$this->outputText('修改信息成功!'):$this->outputText('修改信息失败!');
	           
	           
	           
	      }else if(preg_match($regex3, $tmp, $matches)){
	           
	           
	          $ans=$this->modify('phone',$matches[1],$stu_id,'xuesheng');
	          $ans?$this->outputText('修改信息成功!'):$this->outputText('修改信息失败!');
	           
	      }else{
	           
	           
	          $this->outputText($this->message_num);
	           
	           
	      }
	      
	      
	      
	      
	  }
	  
	  
	   function  saveStuInfo($stu_id,$openid)
    {
        $mysql = new SaeMysql();

        $sql = "insert into   weixinbiao(openid,id,role)  values('$openid','$stu_id','1')";
        
        $mysql->runSql($sql);

        if ($mysql->errno() != 0)
        {   
        	  
           // die("Error:".$mysql->errmsg());
            return 0;
        }
        $mysql->closeDb();
    
        return  1;
    
    
    }
	

   
	
	
    function getStuidByOpenid($openid)
    {
        $mysql = new SaeMysql();
        $sql = "select id  from  weixinbiao  where openid ='$openid'";
        $id=$mysql->getVar($sql);
    
        if ($mysql->errno() != 0)
        {
            die("Error:".$mysql->errmsg());
        }
        $mysql->closeDb();
        return $id;
    }
    

    function getTeaidByOpenid($openid)
    {
        $mysql = new SaeMysql();
        $sql = "select id  from  weixinbiao  where openid ='$openid'";
        $id=$mysql->getVar($sql);
    
        if ($mysql->errno() != 0)
        {
            die("Error:".$mysql->errmsg().$sql);
        }
        $mysql->closeDb();
        return $id;
    }
    
	
	
    function getStarttimeByid($id)
    {
        $mysql = new SaeMysql();
        $sql = "select  starttime   from  xuankebiao  where   kechengid ='$id'";
        $time=$mysql->getVar($sql);
    
        if ($mysql->errno() != 0)
        {
            die("Error:".$mysql->errmsg());
        }
        $mysql->closeDb();
        return $time;
    }
    
    
    
    function getEndtimeByid($id)
    {
        $mysql = new SaeMysql();
        $sql = "select endtime   from  xuankebiao  where   kechengid ='$id'";
        $time=$mysql->getVar($sql);
    
        if ($mysql->errno() != 0)
        {
            die("Error:".$mysql->errmsg());
        }
        $mysql->closeDb();
        return $time;
    }
    
	
	function   checkQiandao($id,$xuankebiaoid){
	    
	   
	    $mysql = new SaeMysql();
	    $sql = "select *  from  kaoqin  where   xueshengid='$id' and xuankebiaoid='$xuankebiaoid'";
	    $num=$mysql->getLine($sql);
	    
	    if ($mysql->errno() != 0)
	    {
	        die("Error:".$mysql->errmsg());
	    }
	    $mysql->closeDb();
	    
	     return  $num;
	    
	    
	    
	    
	    
	 }
	
	

	 function   checkXuanke($id,$xuankebiaoid){
	      
	 
	     $mysql = new SaeMysql();
	     $sql = "select *  from  xuankebiao  where   xueshengid='$id' and  kechengid='$xuankebiaoid'";
	     $num=$mysql->getLine($sql);
	      
	     if ($mysql->errno() != 0)
	     {
	         die("Error:".$mysql->errmsg());
	     }
	     $mysql->closeDb();
	      
	     return  $num;
	      
	   }
	   
     
     
     
     function  getClassInfo($id){
         
         
         $mysql = new SaeMysql();
         $sql = "select b.name,b.id,a.dizhi,a.starttime,a.endtime     from  xuankebiao  a,kecheng  b  where a.kechengid=b.id and    a.xueshengid='$id' ";
         $num=$mysql->getData($sql);
          
         if ($mysql->errno() != 0)
         {
             die("Error:".$mysql->errmsg().$sql);
         }
         $mysql->closeDb();
          
         
      //   $this->outputText($sql);
         
         return  $num?$num:false;
          
         
         
         }
	   
	   
	   
	   
	 function  checkUserRole($id)
	 {
	     

         $id=intval($id);
	     $mysql = new SaeMysql();
	     $sql = "select *  from  jiaoshi   where  id='$id'";
	     $num1=$mysql->getLine($sql);
	     
	     
	     
	     $sql = "select *  from  xuesheng  where  id='$id'";
	     $num2=$mysql->getLine($sql);
	     
	     if ($mysql->errno() != 0)
	     {
	         die("Error:".$mysql->errmsg());
	     }
	     $mysql->closeDb();
	      
	    if($num1)return 2;//jiaoshi
	    else if($num2)return 1;//xuesheng
	    else return 0;//查无此号;
	  }
	
	
	
	
	
	
	
	
	
	
	
	
	
}//class














































?>