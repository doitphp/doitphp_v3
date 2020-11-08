<?php if(!$webappStatus){ ?>
  <div class="alert alert-danger mt40">对不起，您还没有创建所要开发的项目目录，请点击“创建WebApp目录”按钮创建项目目录。</div>
<?php } ?>

<div class="mt40 pb50">
  <fieldset class="pb30">
    <legend>创建WebApp目录:</legend>
    <form name="webapp_manage" method="post" action="<?php echo $actionUrl; ?>" id="webapp-project-form">
      <div class="list-inline">
        <div class="list-inline-item list-label" style="width: 140px;">Server Software :</div>
        <div class="list-inline-item">
          <select name="webserver_name" class="input" style="width: 90px;" id="webserver-software">
            <option value="nginx">Nginx</option>
            <option value="apache" <?php if($isApache){ echo 'selected'; } ?>>Apache</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="list-inline-item pl20">
          <input type="checkbox" name="rewrite_status" value="1" class="form-check-input" id="apache-rewrite-status" <?php if(!$isApache){ echo 'disabled'; } ?>>
          <label for="apache-rewrite-status">.htaccess文件(仅限apache)</label>
        </div>
        <div class="list-inline-item pl20">
          <button type="submit" name="create-webapp-btn" class="btn btn-success">创建WebApp目录</button>
        </div>
      </div>
    </form>
  </fieldset>
</div>
<script type="text/javascript">
function createWebappRequest() {
  return true;
}
$(function(){
  $("#webserver-software").on("change", function(){
    var opt=$(this).val();
    if(opt!='apache'){
      $("#apache-rewrite-status").prop("disabled", true).prop("checked", false);
    }else{
      $("#apache-rewrite-status").prop("disabled", false);
    }
  });
  
  //表单提交
  $("#webapp-project-form").ajaxForm({beforeSubmit:createWebappRequest,success:ajaxResponse,dataType:'json'});
});
</script>