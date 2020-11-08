<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title>提示信息</title>
<style type="text/css">
*,*::before,*::after{box-sizing:border-box}
body{margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Helvetica,"PingFang SC","Hiragino Sans GB","Microsoft YaHei","微软雅黑",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";font-size:16px;font-weight:400;line-height:2;color:#444;text-align:left;background-color:#f8f8f8}
a{color:#409eff;text-decoration:none;background-color:transparent}
a:hover,a::focus{color:#da5430;text-decoration:underline}
.container{width:100%;margin-right:auto;margin-left:auto}
@media(min-width:375px){.container{max-width:340px}}
@media(min-width:414px){.container{max-width:380px}}
@media(min-width:576px){.container{max-width:540px}}
.card{width:100%;background-color:#fff}
.title{width:100%;color:#fff;background-color:#58addb}
.content{width:100%;background-color:#ecf5ff}
@media(min-height:100px){.card{margin-top:0;border:0}.title{padding:0 10px}.content{margin-top:0;padding:0 10px;min-height:65px}}
@media(min-height:128px){.card{margin-top:5px;border:5px solid transparent}.title{padding:0 10px}.content{margin-top:5px;padding:0 10px;min-height:65px}}
@media(min-height:170px){.card{margin-top:10px;border:10px solid transparent}.title{padding:5px 15px}.content{margin-top:10px;padding:5px 10px;min-height:75px}}
@media(min-height:225px){.card{margin-top:20px;border:20px solid transparent}.title{padding:5px 15px}.content{margin-top:15px;padding:10px;min-height:85px}}
@media(min-height:285px){.card{margin-top:30px;border:30px solid transparent}.title{padding:5px 15px}.content{margin-top:15px;padding:20px;min-height:105px}}
@media(min-height:310px){.card{margin-top:40px;border:30px solid transparent}.title{padding:5px 15px}.content{margin-top:15px;padding:20px;min-height:110px}}
@media(min-height:390px){.card{margin-top:80px;border:30px solid transparent}.title{padding:5px 15px}.content{margin-top:15px;padding:20px;min-height:110px}}
</style>
</head>

<body>
<div class="container">
  <div class="card">
    <div class="title">提示信息:</div>
    <div class="content"><?php if(isset($message)){ echo $message; } ?></div>
  </div>
</div>
</body>
</html>