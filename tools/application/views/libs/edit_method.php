<div class="padding15" style="width: 480px;">
  <form action="<?php echo $this->getActionUrl('ajax_edit_method'); ?>" method="post" name="edit-method-form" id="edit-method-form">
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 130px;">Method 名称：</label>
      <input type="text" name="method_name" placeholder="Method 名称" class="input" id="method-name" value="<?php echo $methodInfo['name']; ?>">
    </div>
    <div class="form-group form-inline">
      <label class="form-label justify-content-end" style="width: 130px;">描 述：</label>
      <input type="text" name="method_desc" placeholder="说明描述,可为空" class="input" value="<?php echo $methodInfo['description']; ?>">          
    </div>
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 130px;">访问权限：</label>
      <select id="method_access" name="method_access" class="input" style="width: 120px;">
        <option value="public"<?php if($methodInfo['access']=='public'){ echo ' selected="selected"'; } ?>>public</option>
        <option value="protected"<?php if($methodInfo['access']=='protected'){ echo ' selected="selected"'; } ?>>protected</option>
        <option value="private"<?php if($methodInfo['access']=='private'){ echo ' selected="selected"'; } ?>>private</option>
      </select>          
    </div>
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 130px;">返回数据类型：</label>
      <select name="method_type" id="method_type" class="input" style="width: 120px;">
        <option value="integer"<?php if($methodInfo['type']=='integer'){ echo ' selected="selected"'; } ?>>integer</option>
        <option value="string"<?php if($methodInfo['type']=='string'){ echo ' selected="selected"'; } ?>>string</option>
        <option value="array"<?php if($methodInfo['type']=='array'){ echo ' selected="selected"'; } ?>>array</option>
        <option value="boolean"<?php if($methodInfo['type']=='boolean'){ echo ' selected="selected"'; } ?>>boolean</option>
        <option value="object"<?php if($methodInfo['type']=='object'){ echo ' selected="selected"'; } ?>>object</option>
        <option value="void"<?php if($methodInfo['type']=='void'){ echo ' selected="selected"'; } ?>>void</option>
        <option value="mixed"<?php if($methodInfo['type']=='mixed'){ echo ' selected="selected"'; } ?>>mixed</option>
        <option value="unknown"<?php if($methodInfo['type']=='unknown'){ echo ' selected="selected"'; } ?>>unknown</option>
      </select>
    </div>
    <div class="form-group form-inline">
      <label class="form-label" style="width: 130px;"><input type="hidden" name="method_id" value="<?php echo $methodId; ?>"></label>
      <button type="submit" name="add-method-btn" class="btn btn-success">编辑</button>
    </div>
  </form>  
</div>
<script type="text/javascript">
function editMethodRequest(){
  var methodName=$("#method-name").val();
  if(methodName==''){
    showMsg("Method名称不能为空!", "danger")
    borderDanger($("#method-name"));
    return false;
  }
  return true;
}

$(function(){
  $("#edit-method-form").ajaxForm({beforeSubmit:editMethodRequest,success:ajaxResponse,dataType:"json"});
});
</script>