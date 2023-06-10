jQuery(function($) {

  $(document).on('click','.btn_del_file', function(e){
    let target = e.target
    let parent = $(target).closest('.input-group');
    let preview = parent.siblings('.image_preview')
    let text_form = parent.children('input.filename_view')
    let has_file = $(target).next();
    has_file.val('0')

    let input_file =  parent.find('.image_form')

    input_file.val(null);
    preview.empty();
    text_form.val(null);
  });

  $(document).on('change','.image_form', function(e){
    let target = e.target
    let parent = $(target).closest('.btn-group');
    let has_file = parent.find('.has_file')
    if (target.files.length != 0){
      has_file.val('1')
      preview_image(e)
    }
  });

  function preview_image(event)
  {
    let fileList = []
    let filesNameList = []
    let target = event.target
    let parent = $(target).closest('.input-group');
    let preview = parent.siblings('.image_preview')
    let text_form = parent.children('input.filename_view')
    
    $.each(target.files, function (idx, file) {
      if (['image/jpeg','image/png'].indexOf(file.type) !== -1) {
        fileList.push(file)
        filesNameList.push(file.name)
      }
    });

    // プレビュー初期化
    preview.empty();
    if (!fileList || fileList.length > 5) {
      text_form.val(null);
      return false;
    }
    else {
      $.each(fileList, function (idx, file) {
        let reader = new FileReader();
        reader.onload = (function (elem) { preview.append('<img src="' + elem.target.result + '">');});
        reader.readAsDataURL(file);
      });
      text_form.val(filesNameList.join(' '));
    }
  }

  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // メッセージ共有
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // タイトル_エラーフィードバック解除
  $(document).on('keydown','.title_form', function(e){
    $(e.target).removeClass("is-invalid");
  })

  // 内容_エラーフィードバック解除
  $(document).on('keydown','.content_form', function(e){
    $(e.target).removeClass("is-invalid");
  })

  // 日付_エラーフィードバック解除
  $(document).on('keydown','.datetime_form', function(e){
    $(e.target).removeClass("is-invalid");
  })


  
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // テンプレート追加
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  $(document).on('click','.btn_add_template', function(e){
    e.preventDefault();
    validateTemplateInput('#add_template', "#form_add_template")
  })

  $('#add_template').on('hidden.bs.modal', function(e) {
    const modal = $(e.target)
    modal.find('input.title_form').removeClass("is-invalid");
    modal.find('textarea.content_form').removeClass("is-invalid");
  })


  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // テンプレート編集
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  $(document).on('click','.btn_edit_template', function(e){
    e.preventDefault();
    validateTemplateInput('#edit_template', "#form_edit_template")
  })

  $('#edit_template').on('hidden.bs.modal', function(e) {
    const modal = $(e.target)
    modal.find('input.title_form').removeClass("is-invalid");
    modal.find('textarea.content_form').removeClass("is-invalid");
  })





  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // スケジュール追加
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  $(document).on('click','.btn_add_schedule', function(e){
    e.preventDefault();
    validateScheduleInput('#add_schedule', "#form_add_schedule")
  })

  $('#add_schedule').on('hidden.bs.modal', function(e) {
    const modal = $(e.target)
    modal.find('input.title_form').removeClass("is-invalid");
    modal.find('textarea.content_form').removeClass("is-invalid");
    modal.find('input.datetime_form').removeClass("is-invalid");
  })


  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // スケジュール編集
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  $(document).on('click','.btn_edit_schedule', function(e){
    e.preventDefault();
    validateScheduleInput('#edit_schedule', "#form_edit_schedule")
  })

  $('#edit_schedule').on('hidden.bs.modal', function(e) {
    const modal = $(e.target)
    modal.find('input.title_form').removeClass("is-invalid");
    modal.find('textarea.content_form').removeClass("is-invalid");
    modal.find('input.datetime_form').removeClass("is-invalid");
  })





  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  // 関数
  // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
  function validateTemplateInput(modalId, submitId){
    const modal = $(modalId)
    const inputTitle = modal.find('input.title_form')
    const titleFeedback = modal.find('.title_feedback')
    const inputContent = modal.find('textarea.content_form')
    const contentFeedback = modal.find('.content_feedback')
    let is_err = false

    if (!inputTitle.val()) {
      titleFeedback.text('必須項目です');
      inputTitle.addClass("is-invalid");
      is_err = true
    }
    if (inputTitle.val().length > 50) {
      titleFeedback.text('入力可能文字数は50文字です');
      inputTitle.addClass("is-invalid");
      is_err = true
    }

    if (!inputContent.val()) {
      contentFeedback.text('必須項目です');
      inputContent.addClass("is-invalid");
      is_err = true
    }
    if (inputContent.val().length > 1000)
    {
      contentFeedback.text('入力可能文字数は1000文字です');
      inputContent.addClass("is-invalid");
      is_err = true
    }

    if (is_err) {
      return;
    }
    else {
      $(submitId).submit();
    }
  }

  function validateScheduleInput(modalId, submitId){
    const modal = $(modalId)
    const inputTitle = modal.find('input.title_form')
    const titleFeedback = modal.find('.title_feedback')
    const inputContent = modal.find('textarea.content_form')
    const contentFeedback = modal.find('.content_feedback')

    const inputDatetime = modal.find('input.datetime_form')
    const datetimeFeedback = modal.find('.datetime_feedback')
    let is_err = false

    if (!inputDatetime.val()) {
      datetimeFeedback.text('必須項目です');
      inputDatetime.addClass("is-invalid");
      is_err = true
    }

    if (!inputTitle.val()) {
      titleFeedback.text('必須項目です');
      inputTitle.addClass("is-invalid");
      is_err = true
    }
    if (inputTitle.val().length > 50) {
      titleFeedback.text('入力可能文字数は50文字です');
      inputTitle.addClass("is-invalid");
      is_err = true
    }

    if (!inputContent.val()) {
      contentFeedback.text('必須項目です');
      inputContent.addClass("is-invalid");
      is_err = true
    }
    if (inputContent.val().length > 1000)
    {
      contentFeedback.text('入力可能文字数は1000文字です');
      inputContent.addClass("is-invalid");
      is_err = true
    }

    if (is_err) {
      return;
    }
    else {
      $(submitId).submit();
    }
  }

});

