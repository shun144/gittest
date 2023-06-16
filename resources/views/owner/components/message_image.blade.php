<label for="input_file">画像</label>
<div class="image_component" style="width:100%">
  <div class="input-group">
    <div class="btn-group">
      <label>
        <span class="btn btn-outline-primary">選択            
          <input type="file" style="display:none;" multiple="multiple" class="form-control-file image_form" name="imagefile[]" accept="image/jpeg,image/png" aria-describedby="imgFileHelp"/>
        </span>
      </label>
      <label>
        <span class="btn btn-outline-danger btn_del_file">削除</span>
        <input type="hidden" name='has_file' class='has_file'>
      </label>
    </div>
    <input type="text" class="filename_view form-control" readonly="">
  </div> 
  <small id="imgFileHelp" class="mt-0 mb-2 form-text text-muted">送信可能な拡張子はpng,jpegです（上限ファイル数：1）</small>
  <p class="col-12 m-0 p-0 image_preview"></p>
</div>