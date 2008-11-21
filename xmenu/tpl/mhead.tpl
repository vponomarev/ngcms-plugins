<style>
.xmenu {
font-size: 22px;
padding: 5px;
padding-bottom: 10px;
}

.xmenu span {
width: 60px;
border: 1px solid #616161;
padding-left: 20px;
padding-right: 20px;
cursor: pointer;
}

.active {
background-color: #EEEEEE;
}

.passive {
background-color: #FFFFFF;
width: 60px;
border: 1px solid #616161;
padding-left: 20px;
padding-right: 20px;
}
{activity}
</style>
<script language="javascript">
function xmenu_click(id) {
 var i;
 for (i=0; i<=9; i++) {
 	document.getElementById('go_'+i).className = (i==id)?'active':'passive';
 	document.getElementById('menu_'+i).style.display = (i==id)?'block':'none';
// 	alert(document.getElementById('menu_'+i).style.display);
 }	
}
</script>
<tr><td width="100%">
<div class="xmenu">
<span id="go_0" class="active" onclick="xmenu_click(0);">Категории</span>
<span id="go_1" onclick="xmenu_click(1);">1</span>
<span id="go_2" onclick="xmenu_click(2);">2</span>
<span id="go_3" onclick="xmenu_click(3);">3</span>
<span id="go_4" onclick="xmenu_click(4);">4</span>
<span id="go_5" onclick="xmenu_click(5);">5</span>
<span id="go_6" onclick="xmenu_click(6);">6</span>
<span id="go_7" onclick="xmenu_click(7);">7</span>
<span id="go_8" onclick="xmenu_click(8);">8</span>
<span id="go_9" onclick="xmenu_click(9);">9</span>
</div>
</td></tr>