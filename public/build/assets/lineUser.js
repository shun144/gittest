const URL_ROOT = $(location).attr('origin');
const URL_DASHBOARD = URL_ROOT + '/dashboard';
const URL_LINE_USER_STATUS_UPD = URL_DASHBOARD + '/line-users-upd-status';
const URL_LINE_USER_VIEW = URL_DASHBOARD + '/line-users';


function updateLineUserStatus(e){
  const msg = '退会済み友達更新を開始してよろしいですか?'
  if(!window.confirm(msg))
  {
    return false;
  }

  $('#line_user_status_loader').css('display','block');
  return true;
};