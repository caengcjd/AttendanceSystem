<?php
$title = '生成二维码签到';
include 'header.php';
?>
<body>
<?php
if(isset($_GET['msg']) && $_GET['msg'] == 'emptyparams'){
    echo '<p class="error">选课表ID和验证码不能为空</p>';
}

 
?>
<form action="saveaddqr.php" method="post" onsubmit="return check();">
    <label for="discount">选课表</label>
    <input type="text" name="xuankebiaoid" id="xuankebiaoid"/>
    
    <label for="sceneid">验证码（用于顾客使用时校验真伪）</label>
    <input type="text" id="sceneid" name="sceneid"/>
    <button onclick="getrandomid();return false;">随机生成</button> 
    
    <input type="submit"/>
</form>

</body>
<script type="text/javascript">
//生成10000到99999之间的随机数
function getrandomid(){
    var randomid = Math.round(Math.random()*(99999-10000))+10000;
    document.getElementById('sceneid').value = randomid;
}

function check(){
    var discount = document.getElementById("xuankebiaoid").value;
    var sceneid = document.getElementById("sceneid").value;    
    if(!discount){
        alert('选课表ID不能为空');
        return false;
    }
    if(!sceneid){
        alert('验证码不能为空');
        return false;
    }          
    return true;
}
</script>
</html>