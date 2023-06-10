<label>タイトルカラー</label>
<div class="btn-group" style="width: 100%; margin-bottom: 10px;">
  <ul class="fc-color-picker" id="select_title_color">
    <li><a style="color: #E60012;" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #F39800" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #FFF100" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #8FC31F" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #009944" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #009E96" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #00A0E9" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #0068B7" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #1D2088" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #920783" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #E4007F" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
    <li><a style="color: #000" tabindex="-1"><i onclick="colorClick(event)" class="fas fa-square"></i></a></li>
  </ul>
  <input id="title_color" type="hidden" name="title_color">
</div>
<script>
  'use strict'
  function colorClick(event){
    console.log(event.target.parentNode.style.color)
      document.getElementById( "title_color" ).value = rgbToHex(event.target.parentNode.style.color)
  }

  function rgbToHex(col)
{
    if(col.charAt(0)=='r')
    {
        col=col.replace('rgb(','').replace(')','').split(',');
        var r=parseInt(col[0], 10).toString(16);
        var g=parseInt(col[1], 10).toString(16);
        var b=parseInt(col[2], 10).toString(16);
        r=r.length==1?'0'+r:r; g=g.length==1?'0'+g:g; b=b.length==1?'0'+b:b;
        var colHex='#'+r+g+b;
        return colHex;
    }
}
</script>