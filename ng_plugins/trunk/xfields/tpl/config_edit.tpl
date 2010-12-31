<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />{l_config_text}: <a href="?mod=extra-config&plugin=xfields&section={sectionID}" class="bold">xfields</a> :: [add]{l_xfields_title_add}[/add][edit]{l_xfields_title_edit} ({id})[/edit]</td>
</tr>
</table>

<script language="javascript">
function clx(mode) {
 document.getElementById('type_text').style.display = (mode == 'text')?'block':'none';
 document.getElementById('type_textarea').style.display = (mode == 'textarea')?'block':'none';
 document.getElementById('type_select').style.display = (mode == 'select')?'block':'none';
}
function storageMode(mode) {
// alert(document.getElementById('storageRow'));
 if (mode == 0) {
  document.getElementById('storageRow').className = 'contRow3';
  document.getElementById('db.type').disabled = true;
  document.getElementById('db.len').disabled = true;
 } else {
  document.getElementById('storageRow').className = 'contRow1';
  document.getElementById('db.type').disabled = false;
  document.getElementById('db.len').disabled = false;
 }

}

</script>

<form action="?mod=extra-config&plugin=xfields&action=doedit&section={sectionID}" method="post" name="xfieldsform">
<input type="hidden" name="mod" value="extra-config">
<input type="hidden" name="edit" value="[add]0[/add][edit]1[/edit]">
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr class="contRow1"><td width="50%">{l_xfields_id}</td><td width="47%"><input type="text" name="id" value="{id}" size="40" [edit]readonly[/edit]>[edit] &nbsp; &nbsp; {l_xfields_noeditid}[/edit]</td></tr>
<tr class="contRow1"><td width="50%">{l_xfields_title}</td><td><input type="text" name="title" value="{title}" size="40" /></td></tr>
<tr class="contRow1"><td width="50%">Режим сохранения данных:</td><td><select name="storage" id="storage" value="{storage}" onclick="storageMode(this.value);" onchange="storageMode(this.value);" /><option value="0">Единое хранилище</option><option value="1">Персональное поле в БД</option></select></td></tr>
<tr class="contRow3" id="storageRow"><td width="50%">Тип поля в БД:</td><td><select name="db_type" value="{db.type}" id="db.type" /><option value="int">int - только цифры</option><option value="char">char - текст с ограничением длины</option><option value="datetime">datetime - дата-время</option></select> <input maxlength="3" size="3" type="text" name="db_len" value="{db.len}" id="db.len" /></td></tr>
<tr class="contRow1"><td width="50%">{l_xfields_type}</td><td><select name="type" id="type" onclick="clx(this.value);" onchange="clx(this.value);" />{type_opts}</select></td></tr>
</table>

<div id="type_text">
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr class="contRow1"><td width="5%" style="background-color: #E0FFFF;">{l_xfields_type_texts}</td><td width="45%">{l_xfields_default}</td><td><input type="text" name="text_default" value="{text_default}" size=40></tr>
</table>
</div>
<div id="type_textarea">
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr class="contRow1"><td width="5%" style="background-color: #FFE0FF;">{l_xfields_type_textareas}</td><td width="45%">{l_xfields_default}</td><td><textarea name="textarea_default" cols=70 rows=4>{textarea_default}</textarea></tr>
</table>
</div>
<div id="type_select">
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr class="contRow1"><td width="5%" style="background-color: #FFFFE0;">{l_xfields_type_selects}</td><td width="45%">{l_xfields_tselect_storekeys}</td><td><select name="select_storekeys">{storekeys_opts}</select></td></tr>
<tr class="contRow1"><td width="5%" style="background-color: #FFFFE0;">{l_xfields_type_selects}</td><td>{l_xfields_tselect_options}</td><td><textarea cols=70 rows=8 name="select_options">{select_options}</textarea></tr>
<tr class="contRow1"><td width="5%" style="background-color: #FFFFE0;">{l_xfields_type_selects}</td><td>{l_xfields_tselect_default}</td><td><input type="text" name="select_default" value="{select_default}" size=40></tr>
</table>
</div>
<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr class="contRow1"><td width="50%">{l_xfields_required}</td><td width="47%"><select name="required">{required_opts}</select></td></tr>
<tr class="contRow1"><td colspan=2 align="center"><input type="submit" class="button" value="{l_xfields_save}"></td></tr>
</table>
</form>

<script type="text/javascript">
clx('{type}');
document.getElementById('storage').value = '{storage}';
document.getElementById('db.type').value = '{db.type}';
storageMode(document.getElementById('storage').value);
</script>