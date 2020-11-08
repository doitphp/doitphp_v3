<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">    
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php if(isset($pageTitle)){ echo $pageTitle, ' |'; } ?> doitphp tools</title>
  <meta name="keywords" content="doitphp, doitphp tools">
  <meta name="description" content="doit tools是doitphp开发项目的脚手架">
  <link href="<?php echo $baseAssetUrl; ?>/css/main.css?ver=20200418" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/jquery.form.min.js"></script>
  <script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/jquery.cookie.min.js"></script>
  <script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/layer/layer.min.js"></script>
  <script type="text/javascript" src="<?php echo $baseAssetUrl; ?>/js/common.js"></script>
</head>
<style type="text/css">
.list-label{
  width: 190px;
  padding-left: 15px;
  font-weight: 600;
}
</style>
<body>
<!-- 顶部 -->
<header>
  <div  class="container">
    <div class="text-left">
      <a href="#"><img src="<?php echo $baseAssetUrl; ?>/images/logo.jpg"></a>
    </div>
    <div class="mt10 text-right">
      欢迎使用: <span class="text-danger">DoitPHP Tools ( 标准版 )</span> <a href="<?php echo $logoutUrl; ?>">退出</a>
    </div>
  </div>
</header>
<!-- /顶部 -->

<!-- 主菜单 -->
<nav class="mt20 mb30">
  <div  class="container">
    <?php $this->widget('mainMenu', array('basePath'=>$webappPath)); ?>
  </div>
</nav>
<!-- /主菜单 -->

<!-- 内容 -->
<div  class="container">  
<?php echo $viewContent; ?>
</div>
<!-- /内容 -->

<!-- 底部 -->
<footer class="mt40">
  <div  class="container text-center">
    CopyRight  <a href="http://www.doitphp.com" target="_blank">www.doitphp.com</a> 2009 - 2020
  </div>  
</footer>
<!-- /底部 -->
</body>
</html>