<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">    
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>管理员登陆页 | doitphp tools</title>
  <meta name="keywords" content="doitphp, doitphp tools">
  <meta name="description" content="doit tools是doitphp开发项目的脚手架">
  <link href="<?php echo $baseAssetUrl; ?>/css/main.css?ver=20200415" rel="stylesheet" type="text/css">
</head>
<style type="text/css">
.login{
  margin: 120px auto 0;
  width: 480px;
}
</style>
<body>
<div  class="container">
  <div class="login border border-primary pb30">
    <div class="mt30 ml60"><img src="<?php echo $baseAssetUrl; ?>/images/logo.jpg"></div>
    <form name="login-form" method="post" action="<?php echo $actionUrl; ?>" id="login-form">
      <div class="mt40 ml40">
        <div class="form-group form-inline">
          <label class="form-label">用户名：</label>
          <input type="text" name="user_name" placeholder="请输入用户名" class="input border border-primary" style="width:240px;" id="user-name">          
        </div>
        <div class="form-group form-inline">
          <label class="form-label form-label-lg">密 码：</label>
          <input type="password" name="user_password" placeholder="请输入密码" class="input border border-primary" style="width:240px;" id="user-password">
        </div>
      </div>
      <div class="mt40 ml60 mr60 pb30">         
        <button type="submit" name="submit-btn" class="btn btn-success btn-block" id="submit-btn">登录</button>
      </div>
    </form>
  </div>
</div>

<script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/jquery.form.min.js"></script>
<script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/layer/layer.min.js"></script>
<script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/common.js"></script>
<script type="text/javascript">
function loginRequest(){
  var user=$("#user-name").val();  
  if(user==''){
    borderDanger($("#user-name"));
    alertMsg("用户名不能为空!", "danger");
    return false;
  }
  var password=$("#user-password").val();
  if(password==''){
    borderDanger($("#user-password"));
    alertMsg("密码不能为空!", "danger");
    return false;
  }
  return true;
}
$(function(){    
  $("#login-form").ajaxForm({beforeSubmit:loginRequest,success:ajaxResponse,dataType:"json"});
});
</script>
</body>
</html>