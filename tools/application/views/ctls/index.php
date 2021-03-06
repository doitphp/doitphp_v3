<fieldset class="mb60 padding20 pt15 pb50">
  <legend>创建Controller文件</legend>
  <div class="padding5 pl15">
    <img src="<?php echo $baseAssetUrl; ?>/images/file_topdir.gif"> <a href="<?php echo $this->createUrl('files/index'); ?>/?path=/application/controllers" class="text-primary mr20">返回Controllers目录</a>
    <img src="<?php echo $baseAssetUrl; ?>/images/tree_folderopen.gif"> 当前目录：<span class="font-weight-bold"><?php echo $webappPath; ?>/application/controllers</span>
  </div>

  <form action="<?php echo $this->getActionUrl('ajax_create_file'); ?>" method="post" name="create-file-form" id="create-file-form">
  <!--基本信息-->
  <div class="mt10 padding5 pl15 bg-info">基本信息</div>
  <div class="mt10 ml20 mr20">
    <div class="form-group form-inline">
      <label class="form-label font-weight-bold">Controller名称:</label>
      <input type="text" name="controller_name" placeholder="请输入Controller名称" class="input" id="controller-name">

      <input type="checkbox" name="is_view_dir" value="1" class="form-check-input ml15" id="is-view-dir">
      <label for="is-view-dir">视图目录</label>

      <input type="checkbox" name="is_view_file" value="1" class="form-check-input ml15" id="is-view-file">
      <label for="is-view-file">视图文件</label>        
    </div>
  </div>
  <!--/基本信息-->

  <!--Actions Lists-->
  <div class="padding5 pl15 bg-info">Action</div>
  <div class="mt10">
    <div class="text-right"><a href="<?php echo $this->getActionUrl('add_action'); ?>" class="lightbox btn btn-success" style="margin-right: 15px;" data-width="480" data-height="220" title="添加Action">添加Action</a></div>
    <?php if(is_array($fileData['actions']) && $fileData['actions']){ ?>    
    <div class="mt15 padding5 pb15 border border-light">    
      <div class="row padding5 bg-light text-center">
        <div class="col-1">ID</div>
        <div class="col-3">Action Name</div>
        <div class="col-5 text-left">描述</div>
        <div class="col-3">操作 <input type="hidden" name="delete_actionid" value="0" id="delete-action-id"></div>
      </div>
      <?php  $listId = 0; foreach($fileData['actions'] as $actionId => $actionInfo){ ?>
      <div class="row mt5">
        <div class="col-1 text-center"><?php echo ++ $listId; ?></div>
        <div class="col-3 text-center font-weight-bold"><?php echo $actionInfo['name']; ?></div>
        <div class="col-5"><?php echo $actionInfo['description']; ?></div>
        <div class="col-3 text-center">
          <a href="<?php echo $editActionUrl; ?>/id/<?php echo $actionId; ?>" class="lightbox btn btn-sm btn-outline-warning" data-width="480" data-height="220" title="编辑Action">编辑</a>
          <a href="javascript:void(0);" class="delete-action-btn btn btn-sm btn-outline-danger ml15" data-id="<?php echo $actionId; ?>">删除</a>
        </div>
      </div>
      <?php } ?>  
    </div>
    <?php } ?>
  </div>
  <!--/Actions Lists-->

  <!--Methods Lists-->
  <div class="mt20 padding5 pl15 bg-info">Method</div>

  <div class="mt10">
    <div class="text-right">
    <a href="<?php echo $this->getActionUrl('add_method'); ?>" class="lightbox btn btn-success" style="margin-right: 15px;" title="添加Method" data-width="480" data-height="340">添加Method</a>
    <input type="hidden" name="delete-method-id" value="0" id="delete-method-id">
    <input type="hidden" name="delete-params-id" value="0-0" id="delete-params-id">    
    </div>
    <?php if(is_array($fileData['methods']) && $fileData['methods']){ ?>
      <?php $listId = 0; foreach($fileData['methods'] as $methodId => $methodInfo){ ?>
      <div class="mt15 padding5 border border-light">
        <div class="padding5 pb15 bg-success">
          <div class="row text-left">
            <div class="col-1"><span class="font-weight-bold">ID</span> : <span class="font-weight-bold"><?php echo ++ $listId; ?></span></div>
            <div class="col-5">Method Name：<span class="font-weight-bold"><?php echo $methodInfo['name']; ?></span></div>
            <div class="col-3">访问权限：<span class="font-weight-bold"><?php echo $methodInfo['access']; ?></span></div>
            <div class="col-3">返回数据类型：<span class="font-weight-bold"><?php echo $methodInfo['type']; ?></span></div>           
          </div>
          <div class="row text-left">
            <div class="col-1"></div>
            <div class="col-7">描述：<?php echo $methodInfo['description']; ?></div>
            <div class="col-4 text-center">
              <a href="<?php echo $addParamsUrl; ?>/mid/<?php echo $methodId; ?>" class="lightbox btn btn-sm btn-outline-success" data-width="480" data-height="340" title="添加参数">添加参数</a>
              <a href="<?php echo $editMethodUrl; ?>/id/<?php echo $methodId; ?>" class="lightbox btn btn-sm btn-outline-warning ml15" title="编辑Method" data-width="480" data-height="340">编辑</a>
              <a href="javascript:void(0);" class="delete-method-btn btn btn-sm btn-outline-danger ml15" data-id="<?php echo $methodId; ?>">删除</a>              
            </div>
          </div>
        </div>

        <?php if(is_array($methodInfo['params']) && $methodInfo['params']){ ?>
          <div class="mt5 pb15">
            <div class="row bg-light text-center">
              <div class="col-2">参数名称</div>
              <div class="col-2">数据类型</div>
              <div class="col-4 text-left">参数描述</div>
              <div class="col-2">默认值</div>
              <div class="col-2">操作</div>
            </div>
          <?php foreach($methodInfo['params'] as $paramId => $paramsInfo){ ?>
            <div class="row mt10">
              <div class="col-2 font-weight-bold text-center"><?php echo $paramsInfo['name']; ?></div>
              <div class="col-2 text-center"><?php echo $paramsInfo['type']; ?></div>
              <div class="col-4"><?php echo $paramsInfo['description']; ?></div>
              <div class="col-2 text-center"><?php echo $paramsInfo['default']; ?></div>
              <div class="col-2 text-center">
                <a href="<?php echo $editParamsUrl; ?>/mid/<?php echo $methodId; ?>/pid/<?php echo $paramId; ?>" class="lightbox btn btn-sm btn-outline-warning" data-width="480" data-height="340" title="编辑参数">编辑</a>
                <a href="javascript:void(0);" class="delete-params-btn btn btn-sm btn-outline-danger ml15" data-id="<?php echo $methodId; ?>-<?php echo $paramId; ?>">删除</a>
              </div>
            </div>
          <?php } ?>
          </div>
        <?php } ?>        
      </div>       
      <?php } ?>
    <?php } ?>
  </div>
  <!--/Methods Lists-->

  <!--注释信息-->
  <?php $this->widget('fileNote'); ?>
  <!--/注释信息-->

  <!--提交按钮-->
  <div class="mt40 text-center">
    <button type="submit" name="submit-btn" class="btn btn-success btn-lg">创建Controller文件</button>
  </div>
  <!--/提交按钮-->
  </form> 
</fieldset>
<script type="text/javascript">
function deleteAction(){
  //get action id
  var actionId=$("#delete-action-id").val();
  $.post("<?php echo $this->getActionUrl('ajax_delete_action'); ?>", {action_id:actionId}, ajaxResponse, "json");
}

function deleteMethod(){
	//get method id
	var methodId=$("#delete-method-id").val();
	$.post("<?php echo $this->getActionUrl('ajax_delete_method'); ?>", {method_id:methodId}, ajaxResponse, "json");
}

function deleteParams(){
	//get params id
	var paramId=$("#delete-params-id").val();
	$.post("<?php echo $this->getActionUrl('ajax_delete_params'); ?>", {param_key:paramId}, ajaxResponse, "json");
}

function createFileRequest(){
  var controllerName=$("#controller-name").val();
  if(controllerName==''){
    showMsg("Controller 名称不能为空!", "danger");
    borderDanger($("#controller-name"));
    return false;
  }
  return true;
}

//ajax回调函数
function ajaxFormResponse(json) {
  if(json.status==true){  
    //执行成功时
    layer.msg(json.msg, {icon:5,time:1000}, function(){      
      //删除冗余数据
      $.removeCookie('filename');
      $.removeCookie('viewdir');
      $.removeCookie('viewfile');
      $("#controller-name").val('');
      $("#note-description").val('');
      $("#is-view-dir").prop("checked", false);
      $("#is-view-file").prop("checked", false);

      //网址跳转
      if(json.data.targeturl=='refresh'){
        location.reload();
      }else{
        location.href=json.data.targeturl;
      }      
    });    
  }else{
  //执行失败时
    if(json.msg!='undefined'&&json.msg){
      layer.alert(json.msg, {icon:4});
    }  
  }
}

$(function(){
  var controllerName=$.cookie('filename');
  if(controllerName){
    $("#controller-name").val(controllerName);
  }

  var isDirChecked = $.cookie('viewdir');
  if(isDirChecked=='true'){
    $("#is-view-dir").prop("checked", true);
  }
  var isFileChecked = $.cookie('viewfile');
  if(isFileChecked=='true'){
    $("#is-view-file").prop("checked", true);
  }

  $("#controller-name").on("blur", function(){
    var controllerName=$(this).val();
    if(controllerName){
      $.cookie('filename', controllerName);
    }    
  });

  $("#is-view-dir").on("click", function(){
    var isChecked=$(this).prop("checked");
    $.cookie('viewdir', isChecked);
  });

  $("#is-view-file").on("click", function(){
    var isChecked=$(this).prop("checked");
    $.cookie('viewfile', isChecked);
  }); 

  //删除Actions
  $(".delete-action-btn").on("click", function(){
    var actId=$(this).data("id");
    if(actId===undefined){
      return false;
    }
    $("#delete-action-id").val(actId);
    //confirm event
    confirmMsg("您确定要进行删除操作？", deleteAction);    
  }); 
  
  //删除类方法
  $(".delete-method-btn").on("click", function(){
    var methodId=$(this).data("id");
    if(methodId===undefined){
      return false;
    }
    $("#delete-method-id").val(methodId);
    //confirm event
    confirmMsg("您确定要进行删除操作？", deleteMethod);    
  });

  //删除类方法的参数
  $(".delete-params-btn").on("click", function(){
    var paramId=$(this).data("id");
    if(paramId===undefined){
      return false;
    }
    $("#delete-params-id").val(paramId);
    //confirm event
    confirmMsg("您确定要进行删除操作？", deleteParams);    
  });

  //创建文件
  $(function(){
    $("#create-file-form").ajaxForm({beforeSubmit:createFileRequest,success:ajaxFormResponse,dataType:"json"});
  });   
});
</script>