<form method="post" action="admin.php?mod=extra-config&amp;plugin=gmanager&amp;action=general_submit">
	<fieldset>
		<legend><b>{l_gmanager:legend_general}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_locate_tpl}<br/>
					<small>{l_gmanager:desc_locate_tpl}</small>
				</td>
				<td width="50%" class="contentEntry2">{locate_tpl_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_if_auto_cash}<br/>
					<small>{l_gmanager:desc_if_auto_cash}</small>
				</td>
				<td width="50%" class="contentEntry2">{if_auto_cash_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_if_description}<br/>
					<small>{l_gmanager:desc_if_description}</small>
				</td>
				<td width="50%" class="contentEntry2">{if_description_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_if_keywords}<br/>
					<small>{l_gmanager:desc_if_keywords}</small>
				</td>
				<td width="50%" class="contentEntry2">{if_keywords_list}</td>
			</tr>
		</table>
	</fieldset>
	<br/>
	<fieldset>
		<legend><b>{l_gmanager:legend_gallery_main}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_cell}<br/>
					<small>{l_gmanager:desc_cell}</small>
				</td>
				<td width="50%" class="contentEntry2">
					<input type="text" size="10" name="main_cell" value="{main_cell}"/></td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_row}<br/>
					<small>{l_gmanager:desc_row}</small>
				</td>
				<td width="50%" class="contentEntry2"><input type="text" size="10" name="main_row" value="{main_row}"/>
				</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_page}<br/>
					<small>{l_gmanager:desc_page}</small>
				</td>
				<td width="50%" class="contentEntry2">{main_page_list}</td>
			</tr>
		</table>
	</fieldset>
	<br/>
	<fieldset>
		<legend><b>{l_gmanager:legend_gallery_one}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_cell}<br/>
					<small>{l_gmanager:desc_cell}</small>
				</td>
				<td width="50%" class="contentEntry2"><input type="text" size="10" name="one_cell" value="{one_cell}"/>
				</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_row}<br/>
					<small>{l_gmanager:desc_row}</small>
				</td>
				<td width="50%" class="contentEntry2"><input type="text" size="10" name="one_row" value="{one_row}"/>
				</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_gmanager:label_page}<br/>
					<small>{l_gmanager:desc_page}</small>
				</td>
				<td width="50%" class="contentEntry2">{one_page_list}</td>
			</tr>
		</table>
	</fieldset>

	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=gmanager&action=clear_cash'" value="{l_gmanager:button_clear_cash}" class="button"/>&nbsp
				<input type="submit" value="{l_gmanager:button_save}" class="button"/>
			</td>
		</tr>
	</table>
</form>