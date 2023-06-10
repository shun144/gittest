<div class="datetime_component" style="width:100%">
  <label class="form-label">配信日時</label>
  <div class="input-group">
    <div class="col">
      <input type="date" class="datetime_form form-control" name="date">
      <div class="invalid-feedback datetime_feedback"></div> 
      {{-- <input type="text" class="datetime_form form-control" name="date"> --}}
    </div>
    <div class="col">
      <select class="custom-select hh_form" aria-label="hh" name="hh">
        @for ($i = 0; $i <= 23; $i++)
        <option value="{{sprintf('%02d', $i)}}">{{sprintf('%02d', $i)}}時</option>
        @endfor
      </select>
    </div>
    <div class="col ma-0">
      <select class="custom-select mm_form" aria-label="mm" name="mm">
        @for ($i=0; $i <= 50; $i=$i+10)
        <option value="{{sprintf('%02d', $i)}}">{{sprintf('%02d', $i)}}分</option>
        @endfor
      </select>
    </div>
  </div>
</div>