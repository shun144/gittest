
<form action="{{ route('admin.store.add') }}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="modal fade text-left" id="ModalAddStore" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">


          <button type="submit" class="btn btn-success">店舗追加</button>

          

          {{-- <a href="{{route('admin.store.add')}}" class="btn btn-success">
            <span style="color:white">店舗追加</span>
          </a> --}}
          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            
          <div class="mb-3">
            <label for="store_name" class="form-label">店舗名</label>
            <input type="text" class="form-control" id="store_name" name="store_name">
          </div>
            
          <div class="mb-3">
            <label for="store_addr" class="form-label">住所</label>
            <input type="text" class="form-control" id="store_addr" name="store_addr">
          </div>

          <div class="mb-3">
            <label for="store_tel" class="form-label">電話番号</label>
            <input type="text" class="form-control" id="store_tel" name="store_tel">
          </div>

          <div class="mb-3">
            <label for="client_id" class="form-label">LINEサービスID</label>
            <input type="text" class="form-control" id="client_id" name="client_id">
          </div>

          <div class="mb-3">
            <label for="client_secret" class="form-label">LINEサービスパスワード</label>
            <input type="text" class="form-control" id="client_secret" name="client_secret">
          </div>

        </div>
      </div>
    </div>
  </div>

</form>