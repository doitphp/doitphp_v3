<div class="padding15" style="width: 480px;">
  <form action="<?php echo $this->getActionUrl('ajax_edit_params'); ?>" method="post" name="edit-params-form" id="edit-params-form">
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 120px;">参数名称：</label>
      <input type="text" name="param_name" placeholder="参数名称" class="input" id="param-name" value="<?php echo $paramInfo['name']; ?>">          
    </div>
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 120px;">数据类型：</label>
      <select name="param_type" id="params_type" class="input" style="width: 120px;">
        <option value="integer"<?php if($paramInfo['type']=='integer'){ echo ' selected="selected"'; } ?>>integer</option>
        <option value="string"<?php if($paramInfo['type']=='string'){ echo ' selected="selected"'; } ?>>string</option>
        <option value="array"<?php if($paramInfo['type']=='array'){ echo ' selected="selected"'; } ?>>array</option>
        <option value="boolean"<?php if($paramInfo['type']=='boolean'){ echo ' selected="selected"'; } ?>>boolean</option>
        <option value="object"<?php if($paramInfo['type']=='object'){ echo ' selected="selected"'; } ?>>object</option>
        <option value="void"<?php if($paramInfo['type']=='void'){ echo ' selected="selected"'; } ?>>void</option>
        <option value="mixed"<?php if($paramInfo['type']=='mixed'){ echo ' selected="selected"'; } ?>>mixed</option>
        <option value="unknown"<?php if($paramInfo['type']=='unknown'){ echo ' selected="selected"'; } ?>>unknown</option>
      </select>
    </div>    
    <div class="form-group form-inline">
      <label class="form-label justify-content-end" style="width: 120px;">描 述：</label>
      <input type="text" name="param_desc" placeholder="说明描述" class="input" value="<?php echo $paramInfo['description']; ?>">          
    </div>
    <div class="form-group form-inline">
      <label class="form-label justify-content-end" style="width: 120px;">默认值：</label>
      <input type="text" name="param_default" placeholder="参数默认值,可为空" class="input" value="<?php echo $paramInfo['default']; ?>">          
    </div>
    <div class="form-group form-inline">
      <label class="form-label" style="width: 120px;">
      <input type="hidden" name="method_id" value="<?php echo $methodId; ?>">
      <input type="hidden" name="param_id" value="<?php echo $paramId; ?>">      
      </label>
      <button type="submit" name="edit-params-btn" class="btn btn-success">编辑</button>
    </div>
  </form>  
</div>
<script type="text/javascript">
function editParamsRequest(){
  var paramName=$("#param-name").val();
  if(paramName==''){
    showMsg("参数名称不能为空!", "danger")
    borderDanger($("#param-name"));
    return false;
  }
  return true;
}

$(function(){
  $("#edit-params-form").ajaxForm({beforeSubmit:editParamsRequest,success:ajaxResponse,dataType:"json"});
});
</script>