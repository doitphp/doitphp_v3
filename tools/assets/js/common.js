//常用的JS函数

//表单的边框颜色(成功)
function borderSuccess(obj){
  if(obj==""){
    return false;
  }
  if(obj.hasClass("border-danger")){
    obj.removeClass("border-danger");
  }
  if(!obj.hasClass("border-success")){
    obj.addClass("border-success");
  }
  return true;
}
//表单的边框颜色(失败)
function borderDanger(obj){
  if(obj==""){
    return false;
  }
  if(obj.hasClass("border-success")){
    obj.removeClass("border-success");
  }
  if(obj.hasClass("border-primary")){
    obj.removeClass("border-primary");
  }
  if(!obj.hasClass("border-danger")){
    obj.addClass("border-danger");
  }
  obj.focus();
  return true;
}

//文本表单(验证)成功气泡提示
function tipSuccess(obj){
  if(obj==""){
    return false;
  }
  borderSuccess(obj);
  //删掉原有的提示信息
  obj.next("span.tip-danger").remove();
  obj.next("span.tip-success").remove();
  obj.after("<span class=\"tip-success\"><i class=\"icon icon-success\"></span>");
}

//文本表单(验证)错误气泡提示
function tipDanger(obj, message){
  if(obj==""){
    return false;
  }
  borderDanger(obj);
  //删掉原有的错误提示信息
  obj.next("span.tip-success").remove();
  obj.next("span.tip-danger").remove();
  obj.after("<span class=\"tip-danger\"><i class=\"icon icon-warning\"></i>"+message+"</span>");
}

//正则表达式验证邮箱
function validateEmail(email){
  if(email==""){
    return false;
  }
  var emailRegexp =/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/ig;
  if(emailRegexp.test(email)){
    return true;
  }
  return false;
}

//正则表达式验证网址
function validateUrl(url){
  if(url==""){
    return false;
  }
  var urlRegexp =/^(http|https|ftp|ftps):\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?$/ig;
  if(urlRegexp.test(url)){
    return true;
  }
  return false;
}

//信息提示, 注：图标(iconType),info:信息，success:成功信息，danger:失败信息。
function showMsg(message, iconType='info'){
  if(message==""){
    return false;
  }
  var iconId;
  switch(iconType){
    case "success":
      iconId=1;
      break;
    case "danger":
      iconId=2;
      break;
    case "info":
      iconId=0;
      break;
    default:
      iconId=0;
  }
  layer.msg(message, {icon:iconId});
}

//警告信息(替代原javascript alert弹窗)。注：图标(iconType),info:信息，success:成功信息，danger:失败信息。
function alertMsg(message, iconType='danger'){
  if(message==""){
    return false;
  }
  var iconId;
  switch(iconType){
    case "success":
      iconId=1;
      break;
    case "danger":
      iconId=2;
      break;
    case "info":
      iconId=0;
      break;
    default:
      iconId=2;
  }
  layer.alert(message, {icon:iconId});
}

//关闭弹层对话
function layerClose(){
  layer.closeAll("dialog");
}

//询问信息(替代原javascript confirm弹窗)。注：sure为确认按钮绑定的JS函数，cancle为取消按钮绑定的JS函数
function confirmMsg(message, sure, cancle=""){
  if(message==""||sure==""){
    return false;
  }
  if(cancle==""){
    cancle=layerClose;
  }
  layer.confirm(message,{icon:3, title:"操作确认"}, sure, cancle);
}

//表单内容不能为空验证。isTip:错误的信息提示类型(气泡提示/弹窗提示),true:气泡提示;false:弹窗提示。
function checkEmpty(obj, message, isTip=true){
  if(obj==""||message==""){
    return false;
  }
  var content=obj.val();
  if(content==""){
    if(!isTip){
      borderDanger(obj);
      alertMsg(message, "danger");
      return false;
    }
    tipDanger(obj, message);
    return false;
  }
  //当验证通过时
  if(isTip){
    tipSuccess(obj);
    return true;
  }
  borderSuccess(obj);
  return true;
}

//ajax调用回调函数
function ajaxResponse(json) {

  if(json.status==true){
    //执行成功时
    if(json.msg!='undefined'&&json.msg){
      //当有返回信息时
      if(json.data.targeturl!='undefined'&&json.data.targeturl){
        layer.msg(json.msg, {icon:5,time:1000}, function(){
          if(json.data.targeturl=='refresh'){
            location.reload();
          }else{
            location.href=json.data.targeturl;
          }            
        });        
      }else{
        layer.msg(json.msg, {icon:5});
      }
    }else{
      //当没有返回信息时
      if(json.data.targeturl!='undefined'&&json.data.targeturl){
        if(json.data.targeturl=='refresh'){
          location.reload();
        } else {
          location.href=json.data.targeturl;
        }
      }
    }
  }else{
    //执行失败时
    if(json.msg!='undefined'&&json.msg){
      //当有跳转网址时
      if(json.data.targeturl!='undefined'&&json.data.targeturl){
        layer.alert(json.msg, {icon:4,time:2000}, function(){
          if(json.data.targeturl=='refresh'){
            location.reload();
          }else{
            location.href=json.data.targeturl;
          }            
        });        
      }else{
        layer.alert(json.msg, {icon:4});
      }      
    }else{
      //当没有返回信息时
      if(json.data.targeturl!='undefined'&&json.data.targeturl){
        if(json.data.targeturl=='refresh'){
          location.reload();
        } else {
          location.href=json.data.targeturl;
        }
      }
    }   
  }

  return true;
}

$(function(){
  //加载AJAX页面弹窗
  $("a.lightbox").on("click", function(){
    //网址分析处理，防止IE低版本浏览器缓存
		var url=$(this).prop("href");
		if(url === undefined){
			return false;
		}

		if(url.indexOf("?")!==-1){
			url+='&';
		} else {
			url+='?';
    }

    var lightboxId=new Date().getTime();
    url+='random_id='+ lightboxId;

		//标题分析
		var title=$(this).prop("title");
		if(title===undefined){
			title=false;
    }
    
		//弹层的宽和高
		var width=$(this).data("width");
    var height=$(this).data("height"); 
		if(width!==undefined && height!==undefined){
			width+= 'px';
			height+= 'px';
    }
    
    $.get(url, function(ajaxContent){
      console.log(ajaxContent);
      layer.open({
        type: 1,
        title: title,
        id:'lightbox-id-'+lightboxId,
        area: [width, height],
        content: ajaxContent
      });	
    });
    
    return false;
  }); 

  //图片弹窗
  $("a.thickbox").on("click", function(){
    //网址分析处理，防止IE低版本浏览器缓存
    var url=$(this).prop("href");
    if(url === undefined){
      return false;
    }		

    if(url.indexOf("?")!==-1){
      url+='&';
    } else {
      url+='?';
    }

    var lightboxId=new Date().getTime();
    url+='random_id='+ lightboxId;

    //标题分析
    var title=$(this).prop("title");
    if(title===undefined){
      title=false;
    }
    
    //弹层的宽和高
    var width=$(this).data("width");
    var height=$(this).data("height"); 
    if(width!==undefined && height!==undefined){
      width+= 'px';
      height+= 'px';
    }
   
    layer.open({
      type: 1,
      title: title,
      shadeClose:true,
      id:'lightbox-id-'+lightboxId,
      area: [width, height],
      content: "<img src='"+url+"'>"		  
    });
    
    return false;
  }); 
});