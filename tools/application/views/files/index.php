<div class="ml40">
  <span class="text-info font-weight-bold">WebApp Path</span> : <span class="text-success font-size-16"><?php echo $webappPath; ?></span> 
  (<?php if($isWritable==true){ ?><span class="text-success"><i class="icon icon-ok-circle"></i> 目录可写</span><?php } else { ?><span class="text-danger"><i class="icon icon-close-fill"></i> 目录没有写入权限</span><?php } ?>)
</div>

<fieldset class="mb60 padding20 pt15 pb30">
  <legend>文件列表：</legend>
  <div class="padding5 pl15 bg-info">
  <?php if($returnUrl){ ?>
    <img src="<?php echo $baseAssetUrl; ?>/images/file_topdir.gif"> <a href="<?php echo $selfUrl, $returnUrl; ?>" target="_self" class="text-primary mr20">返回上级目录</a>
  <?php } ?>
    <img src="<?php echo $baseAssetUrl; ?>/images/tree_folderopen.gif"> 当前目录：<span class="font-weight-bold"><?php echo $webappPath, $path; ?></span>
  </div>

  <div class="pl10">
    <ul class="list-inline">
      <li class="list-inline-item">[<a href="<?php echo $selfUrl; ?>/?path=/" target="_self">根目录</a>]</li>
      <?php if($folderRole!='system'){ ?>
      <li class="list-inline-item">[<a href="javascript:void(0);" id="upload-file-btn">上传文件</a>]</li>
      <?php } ?>
      <?php if($roleLinks){ ?>
        <li class="list-inline-item">[<a href="<?php echo $roleLinks['link']; ?>"><?php echo $roleLinks['text']; ?></a>]</li>
      <?php } ?>
      
    </ul>
  </div>

  <!--文件上传-->
  <div class="mt10 padding10 pl15 bg-warning" id="uploadfile-box" style="display:none;">
    <form action="<?php echo $uploadUrl; ?>" method="post" enctype="multipart/form-data" name="uploadfile_form" id="uploadfile-form">
      文件上传:<input type="hidden" name="upload_dirname" value="<?php echo $path; ?>" >
      <input type="file" name="upload_file" id="uploadfile-name"/>
      <button type="submit" name="upload_submit_btn" class="btn btn-primary btn-sm">上传</button>
      <button type="button" name="cancel_upload_btn" class="btn btn-link" id="remove-fileupload-btn">取消上传</button>
    </form>
  </div>
  <!--/文件上传-->

  <!-- 文件列表 -->
  <div class="mt10">
    <div class="row padding5 bg-success text-center">
      <div class="col-5 text-left">文件名</div>
      <div class="col-1">文件大小</div>
      <div class="col-3">修改时间</div>
      <div class="col-1">权限</div>
      <div class="col-2">操作 <input type="hidden" name="delete-file-name" id="delete-filename-btn" value=""></div>
    </div>
<?php if(is_array($fileLists)){ foreach($fileLists as $fileInfo){ ?>
    <div class="row pb5 text-center">
      <div class="col-5 text-left font-size-16">
      <img src="<?php echo $baseAssetUrl; ?>/images/<?php if($fileInfo['isdir']) { echo 'tree_folder.gif'; } else { echo $fileInfo['ico']; } ?>"> 
      <?php if($fileInfo['isdir']){ ?>
        <a href="<?php echo $selfUrl; ?>/?path=<?php echo $path, '/', $fileInfo['name']; ?>" target="_self"><span class="text-primary"><?php echo $fileInfo['name']; ?></span></a>
      <?php } else { ?>
        <span class="text-primary"><?php echo $fileInfo['name']; ?></span>
      <?php } ?>
      </div>
      <div class="col-1"><?php if(!$fileInfo['isdir']){ echo $fileInfo['size']; } ?></div>
      <div class="col-3"><?php echo $fileInfo['time']; ?></div>
      <div class="col-1"><?php echo $fileInfo['mod']; ?></div>
      <div class="col-2">
      <?php if(!$fileInfo['isdir'] && $folderRole != 'system'){ ?>
        <button type="button" name="cancel_file" class="btn btn-outline-danger btn-sm delete-file-btn" data-filename="<?php echo $fileInfo['name']; ?>">删除</button>
      <?php } ?>
      </div>
    </div>
<?php } } ?>  
  </div>
</fieldset>
<script type="text/javascript">
//文件上传请求分析
function uploadRequest(){
  var fileName=$('#uploadfile-name').val();
  if(fileName==''){
    alertMsg('上传文件不能为空');
    borderDanger($('#uploadfile-name'));
    return false;
  }
  return true;
}
function deleteFile(){
  dirName='<?php echo $path; ?>';
  fileName=$("#delete-filename-btn").val();
  $.post("<?php echo $deleteUrl; ?>", {dir_name:dirName, file_name:fileName}, ajaxResponse, "json");
}
$(function(){
  //文件上传
  $("#upload-file-btn").on("click", function(){
    $('#uploadfile-box').show(200);
  });
  //取消文件上传
  $("#remove-fileupload-btn").on("click", function(){
    $('#uploadfile-box').hide(200);
  });
  //文件删除
  $(".delete-file-btn").on("click", function(){
    fileName=$(this).data("filename");
    $("#delete-filename-btn").val(fileName);
    confirmMsg("您确认要删除该文件？", deleteFile);
  });
  
  
  $('#uploadfile-form').ajaxForm({beforeSubmit:uploadRequest,success:ajaxResponse,dataType:'json'});
});
</script>