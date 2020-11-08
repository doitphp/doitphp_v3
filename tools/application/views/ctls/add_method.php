<div class="padding15" style="width: 480px;">
  <form action="<?php echo $this->getActionUrl('ajax_add_method'); ?>" method="post" name="add-method-form" id="add-method-form">
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 130px;">Method 名称：</label>
      <input type="text" name="method_name" placeholder="Method 名称" class="input" id="method-name">          
    </div>
    <div class="form-group form-inline">
      <label class="form-label justify-content-end" style="width: 130px;">描 述：</label>
      <input type="text" name="method_desc" placeholder="说明描述,可为空" class="input">          
    </div>
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 130px;">访问权限：</label>
      <select id="method_access" name="method_access" class="input" style="width: 120px;">
        <option selected="selected" value="protected">protected</option>
        <option value="private">private</option>
      </select>          
    </div>
    <div class="form-group form-inline mt15">
      <label class="form-label justify-content-end" style="width: 130px;">返回数据类型：</label>
      <select name="method_type" id="method_type" class="input" style="width: 120px;">
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
      <label class="form-label" style="width: 130px;"> </label>
      <button type="submit" name="add-method-btn" class="btn btn-success">添加</button>
    </div>
  </form>  
</div>
<script type="text/javascript">
function addMethodRequest(){
  var methodName=$("#method-name").val();
  if(methodName==''){
    showMsg("Method名称不能为空!", "danger")
    borderDanger($("#method-name"));
    return false;
  }
  return true;
}

$(function(){
  $("#add-method-form").ajaxForm({beforeSubmit:addMethodRequest,success:ajaxResponse,dataType:"json"});
});
</script>