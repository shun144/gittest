jQuery(function(t){t(document).on("click",".btn_del_file",function(e){let n=e.target,i=t(n).closest(".input-group"),a=i.siblings(".image_preview"),d=i.children("input.filename_view");t(n).next().val("0"),i.find(".image_form").val(null),a.empty(),d.val(null)}),t(document).on("change",".image_form",function(e){let n=e.target,a=t(n).closest(".btn-group").find(".has_file");n.files.length!=0&&(a.val("1"),u(e))});function u(e){let n=[],i=[],a=e.target,d=t(a).closest(".input-group"),l=d.siblings(".image_preview"),s=d.children("input.filename_view");if(t.each(a.files,function(o,r){["image/jpeg","image/png"].indexOf(r.type)!==-1&&(n.push(r),i.push(r.name))}),l.empty(),!n||n.length>1)return s.val(null),!1;t.each(n,function(o,r){let f=new FileReader;f.onload=function(v){l.append('<img src="'+v.target.result+'">')},f.readAsDataURL(r)}),s.val(i.join(" "))}t(document).on("keydown",".title_form",function(e){t(e.target).removeClass("is-invalid")}),t(document).on("keydown",".content_form",function(e){t(e.target).removeClass("is-invalid")}),t(document).on("keydown",".datetime_form",function(e){t(e.target).removeClass("is-invalid")}),t(document).on("click",".btnPostImi",function(e){e.preventDefault(),_("#post_message","#form_post_message")}),t("#post_message").on("hidden.bs.modal",function(e){t(e.target).find("textarea.content_form").removeClass("is-invalid")}),t(document).on("click",".btn_add_template",function(e){e.preventDefault(),c("#add_template","#form_add_template")}),t("#add_template").on("hidden.bs.modal",function(e){const n=t(e.target);n.find("input.title_form").removeClass("is-invalid"),n.find("textarea.content_form").removeClass("is-invalid")}),t(document).on("click",".btn_edit_template",function(e){e.preventDefault(),c("#edit_template","#form_edit_template")}),t("#edit_template").on("hidden.bs.modal",function(e){const n=t(e.target);n.find("input.title_form").removeClass("is-invalid"),n.find("textarea.content_form").removeClass("is-invalid")}),t(document).on("click",".btn_add_schedule",function(e){e.preventDefault(),m("#add_schedule","#form_add_schedule")}),t("#add_schedule").on("hidden.bs.modal",function(e){const n=t(e.target);n.find("input.title_form").removeClass("is-invalid"),n.find("textarea.content_form").removeClass("is-invalid"),n.find("input.datetime_form").removeClass("is-invalid")}),t(document).on("click",".btn_edit_schedule",function(e){e.preventDefault(),m("#edit_schedule","#form_edit_schedule")}),t("#edit_schedule").on("hidden.bs.modal",function(e){const n=t(e.target);n.find("input.title_form").removeClass("is-invalid"),n.find("textarea.content_form").removeClass("is-invalid"),n.find("input.datetime_form").removeClass("is-invalid")});function _(e,n){const i=t(e),a=i.find("textarea.content_form"),d=i.find(".content_feedback");let l=!1;a.val()||(d.text("必須項目です"),a.addClass("is-invalid"),l=!0),a.val().length>1e3&&(d.text("入力可能文字数は1000文字です"),a.addClass("is-invalid"),l=!0),!l&&t(n).submit()}function c(e,n){const i=t(e),a=i.find("input.title_form"),d=i.find(".title_feedback"),l=i.find("textarea.content_form"),s=i.find(".content_feedback");let o=!1;a.val()||(d.text("必須項目です"),a.addClass("is-invalid"),o=!0),a.val().length>50&&(d.text("入力可能文字数は50文字です"),a.addClass("is-invalid"),o=!0),l.val()||(s.text("必須項目です"),l.addClass("is-invalid"),o=!0),l.val().length>1e3&&(s.text("入力可能文字数は1000文字です"),l.addClass("is-invalid"),o=!0),!o&&t(n).submit()}function m(e,n){const i=t(e),a=i.find("input.title_form"),d=i.find(".title_feedback"),l=i.find("textarea.content_form"),s=i.find(".content_feedback"),o=i.find("input.datetime_form"),r=i.find(".datetime_feedback");let f=!1;o.val()||(r.text("必須項目です"),o.addClass("is-invalid"),f=!0),a.val()||(d.text("必須項目です"),a.addClass("is-invalid"),f=!0),a.val().length>50&&(d.text("入力可能文字数は50文字です"),a.addClass("is-invalid"),f=!0),l.val()||(s.text("必須項目です"),l.addClass("is-invalid"),f=!0),l.val().length>1e3&&(s.text("入力可能文字数は1000文字です"),l.addClass("is-invalid"),f=!0),!f&&t(n).submit()}});