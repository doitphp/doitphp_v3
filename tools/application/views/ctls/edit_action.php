<div class="padding15" style="width: 480px;">
<form action="<?php echo $this->getActionUrl('ajax_edit_action'); ?>" method="post" name="add_action_form" id="edit-action-form">
  <div class="form-group form-inline">
    <label class="form-label justify-content-end" style="width: 120px;">Action 名称：</label>
    <input type="text" name="action_name" placeholder="Action 名称" class="input" id="action-name" value="<?php echo $actionInfo['name']; ?>">          
  </div>
  <div class="form-group form-inline">
    <label class="form-label justify-content-end" style="width: 120px;">描 述：</label>
    <input type="text" name="action_desc" placeholder="说明描述,可为空" class="input" value="<?php echo $actionInfo['description']; ?>">          
  </div>
  <div class="form-group form-inline mt5">
    <label class="form-label" style="width: 120px;"><input type="hidden" name="action_id" value="<?php echo $actionId; ?>"></label>
    <button type="submit" name="add-act-btn" class="btn btn-success">编辑</button>
  </div>
</form>  
</div>
<script type="text/javascript">
function editActionRequest(){
  var actionName=$("#action-name").val();
  if(actionName==''){
    showMsg("Action名称不能为空!", "danger")
    borderDanger($("#action-name"));
    return false;
  }
  return true;
}

$(function(){
  $("#edit-action-form").ajaxForm({beforeSubmit:editActionRequest,success:ajaxResponse,dataType:"json"});
});
</script>