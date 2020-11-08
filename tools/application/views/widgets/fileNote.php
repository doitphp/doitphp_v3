<div class="mt20 padding5 pl15 bg-info">注释信息</div>
<div class="mt15" style="padding-left: 90px;">
  <div class="form-group form-inline">
    <label class="form-label">文件描述：</label>
    <input type="text" name="note_description" placeholder="文件描述说明" class="input" style="width: 320px;" id="note-description">          
  </div>
  <div class="form-group form-inline">
    <label class="form-label">开发作者：</label>
    <input type="text" name="note_author" placeholder="开发作者, 多作者可使用逗号(,)隔开" class="input" style="width: 320px;" value="<?php echo $noteInfo['author']; ?>">          
  </div>
  <div class="form-group form-inline">
    <label class="form-label">版权信息：</label>
    <input type="text" name="note_copyright" placeholder="版权信息, 可为空" class="input" style="width: 320px;" value="<?php echo $noteInfo['copyright']; ?>">          
  </div>
  <div class="form-group form-inline">
    <label class="form-label">发行协议：</label>
    <input type="text" name="note_license" placeholder="软件发行协议, 可为空" class="input" style="width: 320px;" value="<?php echo $noteInfo['lisence']; ?>">          
  </div>
  <div class="form-group form-inline">
    <label class="form-label">相关链接：</label>
    <input type="text" name="note_link" placeholder="软件的相关链接, 可为空" class="input" style="width: 320px;" value="<?php echo $noteInfo['link']; ?>">          
  </div>
</div>