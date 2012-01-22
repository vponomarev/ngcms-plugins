<table class="content" border="0" cellpadding="1" cellspacing="1">
<tbody>
<tr>
<td colspan="2" class="contentHead" width="100%"><img src="{{ skins_url }}/images/nav.gif" hspace="8"><a href="?mod=extras" title="Управление плагинами">Управление плагинами</a> &#8594; <a href="?mod=extra-config&plugin=xfields&section={{ sectionID }}">{{ lang.xfconfig['config_text'] }} xfields</a> &#8594; {% if (not flags.editMode) %}{{ lang.xfconfig['title_add'] }}{% else %}{{ lang.xfconfig['title_edit'] }} ({{ id }}){% endif %} </td>
</tr>
</tbody>
</table>

<script language="javascript">
function clx(mode) {
 document.getElementById('type_text').style.display		= (mode == 'text')?		'block':'none';
 document.getElementById('type_textarea').style.display = (mode == 'textarea')?	'block':'none';
 document.getElementById('type_select').style.display	= (mode == 'select')?	'block':'none';
 document.getElementById('type_images').style.display	= (mode == 'images')?	'block':'none';
}
function storageMode(mode) {
// alert(document.getElementById('storageRow'));
 if (mode == 0) {
  document.getElementById('storageRow').className = 'contRow4';
  document.getElementById('db.type').disabled = true;
  document.getElementById('db.len').disabled = true;
 } else {
  document.getElementById('storageRow').className = 'contRow1';
  document.getElementById('db.type').disabled = false;
  document.getElementById('db.len').disabled = false;
 }

}

</script>

<div id="edit_yakor"></div>

<form action="?mod=extra-config&plugin=xfields&action=doedit&section={{ sectionID }}" method="post" name="xfieldsform">
<input type="hidden" name="mod" value="extra-config">
<input type="hidden" name="edit" value="{% if (flags.editMode) %}1{% else %}0{% endif %}">
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr>
<td colspan="2" class="contentHead" width="100%"><img src="{{ skins_url }}/images/nav.gif" hspace="8">{% if (flags.editMode) %}{{ lang.xfconfig['title_edit'] }}{% else %}{{ lang.xfconfig['title_add'] }}{% endif %}</td>
</tr>
<tr class="contRow1"><td width="50%">{{ lang.xfconfig['disabled'] }}</td><td width="47%"><input type="checkbox" name="disabled" value="1"{% if (flags.disabled) %}checked="checked"{% endif %}></td></tr>
<tr class="contRow1"><td width="50%">{{ lang.xfconfig['id'] }}</td><td width="47%"><input type="text" name="id" value="{{ id }}" size="40" {% if (flags.editMode) %}readonly{% endif %}>{% if (flags.editMode) %} &nbsp; &nbsp; {{ lang.xfconfig['noeditid'] }}{% endif %}</td></tr>
<tr class="contRow1"><td width="50%">{{ lang.xfconfig['title'] }}</td><td><input type="text" name="title" value="{{ title }}" size="40" /></td></tr>
<tr class="contRow1"><td width="50%">{{ lang.xfconfig['type'] }}</td><td><select name="type" size="4" id="xfSelectType" onclick="clx(this.value);" onchange="clx(this.value);" />{{ type_opts }}</select></td></tr>
</table>

<!-- FIELD TYPE: TEXT -->
<div id="type_text">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_texts'] }}</td>
  <td width="45%">{{ lang.xfconfig['bb_support'] }}</td>
  <td><input type="checkbox" name="text_bb_support" value="1" {{ bb_support }}></td>
 </tr>
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_texts'] }}</td>
  <td width="45%">{{ lang.xfconfig['default'] }}</td>
  <td><input type="text" name="text_default" value="{{ defaults.text }}" size=40></td>
 </tr>
</table>
</div>

<!-- FIELD TYPE: TEXTAREA -->
<div id="type_textarea">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_texts'] }}</td>
  <td width="45%">{{ lang.xfconfig['bb_support'] }}</td>
  <td><input type="checkbox" name="textarea_bb_support" value="1" {{ bb_support }}></td>
 </tr>
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_textareas'] }}</td>
  <td width="45%">{{ lang.xfconfig['default'] }}</td>
  <td>
   <textarea name="textarea_default" cols=70 rows=4>{{ defaults.textarea }}</textarea></td>
 </tr>
</table>
</div>

<!-- FIELD TYPE: SELECT -->
<div id="type_select">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_selects'] }}</td>
  <td width="45%">{{ lang.xfconfig['tselect_storekeys'] }}</td>
  <td><select name="select_storekeys">{{ storekeys_opts }}</select></td>
  </tr>
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_selects'] }}</td>
  <td valign="top">{{ lang.xfconfig['tselect_options'] }}</td>
  <td>
   <table id="xfSelectTable" width="100%" cellspacing="0" cellpadding="0" border="0" class="content" style="padding: 0px;">
   <thead>
    <tr class="contRow1"><td>Код</td><td>Значение</td><td>&nbsp;</td></tr>
   </thead>
   <tbody id="xfSelectRows">
{{ sOpts }}
   </tbody>
   <tfoot>
    <tr><td colspan="3"><input type="button" id="xfBtnAdd" style="width: 300px;" value=" + Добавить строку"/></td></tr>
   </tfoot>
   </table>
  </td>
 </tr>
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">{{ lang.xfconfig['type_selects'] }}</td>
  <td>{{ lang.xfconfig['tselect_default'] }}</td><td><input type="text" name="select_default" value="{{ defaults.select }}" size=40></td>
 </tr>
</table>
</div>

<!-- FIELD TYPE: IMAGES -->
<div id="type_images">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="45%" colspan="2">Максимальное кол-во изображений для загрузки:</td><td colspan=2"><input type="text" size="3" name="images_maxCount" value="{{ images.maxCount }}"/></td></tr>
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="45%" colspan="2">Добавлять штамп:</td><td colspan=2"><input type="checkbox" name="images_imgStamp" value="1" {{ images.imgStamp }} /></td></tr>
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="45%" colspan="2">Добавлять тень:</td><td colspan=2"><input type="checkbox"/ name="images_imgShadow" value="1" {{ images.imgShadow }} /></td></tr>
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="45%" colspan="2">Уменьшенная копия:</td><td colspan=2"><input type="checkbox" name="images_imgThumb" value="1" {{ images.imgThumb }} /></td></tr>
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="5%">&nbsp;</td><td width="40%">Не более:</td><td>&nbsp;</td><td><input type="text" size="4" name="images_thumbWidth" value="{{ images.thumbWidth }}" /> x <input type="text" size="4" name="images_thumbHeight" value="{{ images.thumbHeight }}" /> пикселов</td></tr>
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="5%">&nbsp;</td><td width="40%">Добавлять штамп:</td><td>&nbsp;</td><td><input type="checkbox" name="images_thumbStamp" value="1" {{ images.thumbStamp }}/></td></tr>
 <tr class="contRow1"><td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">изображения</td><td width="5%">&nbsp;</td><td width="40%">Добавлять тень:</td><td>&nbsp;</td><td><input type="checkbox" name="images_thumbShadow" value="1" {{ images.thumbShadow }}/></td></tr>
</table>
</div>
<!-- FIELD TYPE: /CLOSED/ -->

<table border="0" cellspacing="1" cellpadding="1" style="width:100%;" class="contRow3">
<tr class="contRow1"><td width="50%">Режим сохранения данных:</td><td><select name="storage" id="storage" value="{{ storage }}" onclick="storageMode(this.value);" onchange="storageMode(this.value);" /><option value="0">Единое хранилище</option><option value="1">Персональное поле в БД</option></select></td></tr>
<tr class="contRow4" id="storageRow">
<td width="50%">Тип поля в БД:</td>
<td>
 <select name="db_type" value="{{ db_type }}" id="db.type" />
  <option value="int">int - только цифры</option>
  <option value="decimal">decimal - число с фиксированной точкой</option>
  <option value="char">char - текст с ограничением длины</option>
  <option value="datetime">datetime - дата-время</option>
 </select>
 <input maxlength="3" size="3" type="text" name="db_len" value="{{ db_len }}" id="db.len" />
</td>
</tr>
</table>
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr class="contRow1"><td width="50%">{{ lang.xfconfig['required'] }}</td><td width="47%"><select name="required">{{ required_opts }}</select></td></tr>
{% if (sectionID != 'tdata') %}<tr class="contRow1"><td width="50%">Блок:<br/><small>Этот параметр позволяет указать в каком именно месте интерфейса добавления/редактирования новости появится данная переменная.<br/><b>По умолчанию</b> - блок `дополнительно`<br/><b>1</b> - блок `основное содержание`<br/><b>другие (цифровые) значения</b> - для блоков, добавленных в ручном режиме в админ панель</small></td><td width="47%"><input type="text" name="area" value="{{ area }}"/></td></tr>{% endif %}
</table>

<table width="100%">
<tr>&nbsp;</tr>
<tr align="center">
<td class="contentEdit" valign="top" width="100%">
<input id="xfBtnSubmit" type="submit" class="button" value="{% if (flags.editMode) %}{{ lang.xfconfig['edit'] }}{% else %}{{lang.xfconfig['save'] }}{% endif %}">
</td>
</tr>
</table>
</form>

<script type="text/javascript">
clx('{{ type }}');
document.getElementById('storage').value = '{{ storage }}';
document.getElementById('db.type').value = '{{ db_type }}';
storageMode(document.getElementById('storage').value);

var soMaxNum = $('#xfSelectTable >tbody >tr').length+1;

$('#xfSelectTable a').click(function(){
	if ($('#xfSelectTable >tbody >tr').length > 1) {
		$(this).parent().parent().remove();
	} else {
		$(this).parent().parent().find("input").val('');
	}
});

$("#xfBtnSubmit").click(function() {
	// Check if type == 'select'
	if ($("#xfBtnType").val() == 'select') {
		// Prepare list of data



	}

});

// jQuery - INIT `select` configuration
$("#xfBtnAdd").click(function() {
	var xl = $('#xfSelectTable tbody>tr:last').clone();
	xl.find("input").val('');
	xl.find("input").eq(0).attr("name", "so_data["+soMaxNum+"][0]");
	xl.find("input").eq(1).attr("name", "so_data["+soMaxNum+"][1]");
	soMaxNum++;

	xl.insertAfter('#xfSelectTable tbody>tr:last');
	$('#xfSelectTable a').click(function(){
		if ($('#xfSelectTable >tbody >tr').length > 1) {
			$(this).parent().parent().remove();
		} else {
			$(this).parent().parent().find("input").val('');
		}
	});
});

</script>