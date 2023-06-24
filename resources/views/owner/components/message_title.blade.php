<div class="title_component" style="width:100%">
  <label class="form-label">タイトル</label>
    <ul class="fc-color-picker">
      <li>
        <label style="cursor:pointer">
          <input type="radio" class="title_color" name="title_color" style="display:none" value="#E60012"  data-color="#E60012" onchange ="colorChange(event)" checked>
          <i style="color:#E60012" class="fas fa-check-square"></i>
        </label>
      </li>
      @foreach (array("#F39800", "#FFF100", "#8FC31F", "#009944", "#009E96", "#00A0E9", "#0068B7", "#1D2088", "#920783", "#E4007F", "#000") as $color)
      <li>
        <label style="cursor:pointer">
          <input type="radio" class="title_color" name="title_color" style="display:none" value="{{strtoupper($color)}}" data-color="{{strtoupper($color)}}" onchange ="colorChange(event)">
          <i style="color:{{$color}}" class="fas fa-square"></i>
        </label>
      </li>
      @endforeach
    </ul>
    
  <input type="text" class="title_form form-control" name="title" maxlength="50" aria-describedby="titleHelp">
  <div class="invalid-feedback title_feedback"></div> 
  <small id="titleHelp" class="form-text text-muted">タイトルはLINEで通知されません（上限文字数：50）</small>
</div>

<script>
  function colorChange(event){
    let comp = event.target.closest('.title_component')
    let ragioColors = comp.getElementsByClassName("title_color");
    for(let i = 0; i < ragioColors.length; i++){
      if(ragioColors[i].checked) {
        ragioColors[i].nextElementSibling.className = 'fas fa-check-square'
      }
      else
      {
        ragioColors[i].nextElementSibling.className = 'fas fa-square'
      }
    }
  }
</script>


{{-- <div class="title_component" style="width:100%">
  <label class="form-label">タイトル</label>
    <ul class="fc-color-picker">
      <li>
        <label style="cursor:pointer">
          <input type="radio" class="title_color" name="title_color" style="display:none" value="#E60012" onchange ="colorChange(event)" checked>
          <i style="color:#E60012" class="fas fa-check-square"></i>
        </label>
      </li>
      @foreach (array("#F39800", "#FFF100", "#8FC31F", "#009944", "#009E96", "#00A0E9", "#0068B7", "#1D2088", "#920783", "#E4007F", "#000") as $color)
      <li>
        <label style="cursor:pointer">
          <input type="radio" class="title_color" name="title_color" style="display:none" value="{{strtoupper($color)}}" onchange ="colorChange(event)">
          <i style="color:{{$color}}" class="fas fa-square"></i>
        </label>
      </li>
      @endforeach
    </ul>
  <input type="text" class="title_form form-control" name="title" maxlength="50" aria-describedby="titleHelp">
  <div class="invalid-feedback title_feedback"></div> 
  <small id="titleHelp" class="form-text text-muted">タイトルはLINEで通知されません（上限文字数：50）</small>
</div>

<script>
  function colorChange(event){
    let comp = event.target.closest('.title_component')
    let ragioColors = comp.getElementsByClassName("title_color");
    for(let i = 0; i < ragioColors.length; i++){
      if(ragioColors[i].checked) {
        ragioColors[i].nextElementSibling.className = 'fas fa-check-square'
      }
      else
      {
        ragioColors[i].nextElementSibling.className = 'fas fa-square'
      }
    }
  }
</script> --}}
