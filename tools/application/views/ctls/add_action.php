<div class="padding15" style="width: 480px;">
<form action="<?php echo $this->getActionUrl('ajax_add_action'); ?>" method="post" name="add_action_form" id="add-action-form">
  <div class="form-group form-inline">
    <label class="form-label justify-content-end" style="width: 120px;">Action 名称：</label>
    <input type="text" name="action_name" placeholder="Action 名称" class="input" id="action-name">          
  </div>
  <div class="form-group form-inline">
    <label class="form-label justify-content-end" style="width: 120px;">描 述：</label>
    <input type="text" name="action_desc" placeholder="说明描述,可为空" class="input">          
  </div>
  <div class="form-group form-inline mt5">
    <label class="form-label" style="width: 120px;"> </label>
    <button type="submit" name="add-act-btn" class="btn btn-success">添加</button>
  </div>
</form>  
</div>
<script type="text/javascript">
function addActionRequest(){
  var actionName=$("#action-name").val();
  if(actionName==''){
    showMsg("Action名称不能为空!", "danger")
    borderDanger($("#action-name"));
    return false;
  }
  return true;
}

$(function(){
  $("#add-action-form").ajaxForm({beforeSubmit:addActionRequest,success:ajaxResponse,dataType:"json"});
});
</script>