<form method="post" action="admin.php?mod=extra-config&amp;plugin=gmanager&amp;action=edit_submit">
	<input type="hidden" name="id" value="{id}"/>
	<fieldset>
		<legend><b>{l_gmanager:legend_general}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_name}<br/>
					<small>{l_gmanager:desc_name}</small>
				</td>
				<td width="50%" class="contentEntry2">{name}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_title}<br/>
					<small>{l_gmanager:desc_title}</small>
				</td>
				<td width="50%" class="contentEntry2"><input type="text" size="80" name="title" value="{title}"/></td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_if_active}<br/>
					<small>{l_gmanager:desc_if_active}</small>
				</td>
				<td width="50%" class="contentEntry2">{if_active_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_id_icon}<br/>
					<small>{l_gmanager:desc_id_icon}</small>
				</td>
				<td width="50%" class="contentEntry2">{id_icon_list}</td>
			</tr>
		</table>
	</fieldset>
	<br/>

	<fieldset>
		<legend><b>{l_gmanager:legend_description}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_description}<br/>
					<small>{l_gmanager:desc_description}</small>
				</td>
				<td width="50%" class="contentEntry2">
					<TEXTAREA NAME="description" COLS="80" ROWS="5">{description}</TEXTAREA></td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_keywords}<br/>
					<small>{l_gmanager:desc_keywords}</small>
				</td>
				<td width="50%" class="contentEntry2"><TEXTAREA NAME="keywords" COLS="80" ROWS="5">{keywords}</TEXTAREA>
				</td>
			</tr>
		</table>
	</fieldset>

	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" value="{l_gmanager:button_edit}" class="button"/>
			</td>
		</tr>
	</table>
</form>