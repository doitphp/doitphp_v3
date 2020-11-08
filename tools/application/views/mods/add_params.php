<div class="padding15" style="width: 480px;">
  <form action="<?php echo $this->getActionUrl('ajax_add_params'); ?>" method="post" name="add-params-form" id="add-params-form">
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 120px;">参数名称：</label>
      <input type="text" name="param_name" placeholder="参数名称" class="input" id="param-name">          
    </div>
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 120px;">数据类型：</label>
      <select name="param_type" id="params_type" class="input" style="width: 120px;">
        <option value="integer">integer</option>
        <option value="string">string</option>
        <option value="array">array</option>
        <option value="boolean">boolean</option>
        <option value="object">object</option>
        <option value="void">void</option>
        <option value="mixed">mixed</option>
        <option selected="selected" value="unknown">unknown</option>
      </select>
    </div>    
    <div class="form-group form-inline">
      <label class="form-label justify-content-end" style="width: 120px;">描 述：</label>
      <input type="text" name="param_desc" placeholder="说明描述" class="input">          
    </div>
    <div class="form-group form-inline">
      <label class="form-label justify-content-end" style="width: 120px;">默认值：</label>
      <input type="text" name="param_default" placeholder="参数默认值,可为空" class="input">          
    </div>
    <div class="form-group form-inline">
      <label class="form-label" style="width: 120px;"><input type="hidden" name="method_id" value="<?php echo $methodId; ?>"></label>
      <button type="submit" name="add-params-btn" class="btn btn-success">添加</button>
    </div>
  </form>  
</div>
<script type="text/javascript">
function addParamsRequest(){
  var paramName=$("#param-name").val();
  if(paramName==''){
    showMsg("参数名称不能为空!", "danger")
    borderDanger($("#param-name"));
    return false;
  }
  return true;
}

$(function(){
  $("#add-params-form").ajaxForm({beforeSubmit:addParamsRequest,success:ajaxResponse,dataType:"json"});
});
</script>