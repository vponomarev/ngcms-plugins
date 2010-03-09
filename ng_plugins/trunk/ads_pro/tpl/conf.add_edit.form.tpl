<form method="post" action="admin.php?mod=extra-config&amp;plugin=ads_pro&amp;action=[add]add_submit[/add][edit]edit_submit[/edit]">
<input type="hidden" name="id" value="[add]0[/add][edit]{id}[/edit]" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:name}<br /><small>{l_ads_pro:name_d}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="name"[edit] value="{name}"[/edit] /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:description}<br /><small>{l_ads_pro:description_d}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="description"[edit] value="{description}"[/edit] /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:type}<br /><small>{l_ads_pro:type_d}</small></td>
<td width="50%" class="contentEntry2">{type_list}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:location}<br /><small>{l_ads_pro:location_d}</small></td>
<td width="50%" class="contentEntry2"><input type="button" class="button" value='{l_ads_pro:location_dell}' onClick="RemoveBlok();return false;" />&nbsp;
<input type="button" class="button" value='{l_ads_pro:location_add}' onClick="AddBlok();return false;" /><br />
<table id="blokup" align="left">[edit]{location_list}[/edit]</table>
</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:state}<br /><small>{l_ads_pro:state_d}</small></td>
<td width="50%" class="contentEntry2">{state_list}</td>
</tr>
</table><br />
<fieldset>
<legend><b>{l_ads_pro:sched_legend}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:start_view}<br /><small>{l_ads_pro:start_view_d}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="start_view"[edit] value="{start_view}"[/edit] /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_ads_pro:end_view}<br /><small>{l_ads_pro:end_view_d}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="end_view"[edit] value="{end_view}"[/edit] /></td>
</tr>
</table>
</fieldset><br />
<fieldset>
<legend><b>{l_ads_pro:ads_blok_legend}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="100%" class="contentEntry1" align="center"><TEXTAREA NAME="ads_blok" COLS="150" ROWS="30">[edit]{ads_blok}[/edit]</TEXTAREA></td>
</tr>
</table>
</fieldset>


<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="[add]{l_ads_pro:add_submit}[/add][edit]{l_ads_pro:edit_submit}[/edit]" class="button" />
</td>
</tr>
</table>
</form>

<script language="javascript" type="text/javascript">
function AddBlok() {
	var tbl = document.getElementById('blokup');
	var lastRow = tbl.rows.length;
	var iteration = lastRow+1;
	var row = tbl.insertRow(lastRow);
	var cellRight = row.insertCell(0);
	cellRight.innerHTML = iteration+': ';
	cellRight = row.insertCell(1);
	cellRight.setAttribute('align', 'left');

	var el = '<select name="location[' + iteration + '][mode]" onchange="AddSubBlok(this, ' + iteration + ');"><option value=0>{l_ads_pro:around}</option><option value=1>{l_ads_pro:main}</option><option value=2>{l_ads_pro:not_main}</option><option value=3>{l_ads_pro:category}</option><option value=4>{l_ads_pro:static}</option></select>';

	cellRight.innerHTML += el;
	
	el = '<select name="location[' + iteration + '][view]"><option value=0>{l_ads_pro:view}</option><option value=1>{l_ads_pro:not_view}</option></select>';
	
	cellRight.innerHTML += el;
}
function AddSubBlok(el, iteration){
	var subel = null;
	var subsubel = null;
	switch (el.value){
		case '3':
			subel = createNamedElement('select', 'location[' + iteration + '][id]');
			{category_list}
			break;
		case '4':
			subel = createNamedElement('select', 'location[' + iteration + '][id]');
			{static_list}
			break;
	}
	if (el.nextSibling.name == 'location[' + iteration + '][id]')
		el.parentNode.removeChild(el.nextSibling);
	if (subel)
		el.parentNode.insertBefore(subel, el.nextSibling);
}
function RemoveBlok() {
	var tbl = document.getElementById('blokup');
	var lastRow = tbl.rows.length;
	if (lastRow > 0){
		tbl.deleteRow(lastRow - 1);
	}
}
function createNamedElement(type, name) {
    var element = null;
    try {
        element = document.createElement('<'+type+' name="'+name+'">');
    } catch (e) {
    }
    if (!element || element.nodeName != type.toUpperCase()) {
        element = document.createElement(type);
        element.setAttribute("name", name);
    }
    return element;
}
</script>
