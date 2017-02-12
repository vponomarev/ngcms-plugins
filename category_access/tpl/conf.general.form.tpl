<form method="post" action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=general_submit">
	<fieldset>
		<legend><b>{l_category_access:legend_general}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" class="contentEntry1">{l_category_access:label_guest}<br/>
					<small>{l_category_access:desc_guest}</small>
				</td>
				<td width="50%" class="contentEntry2">{guest_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_category_access:label_coment}<br/>
					<small>{l_category_access:desc_coment}</small>
				</td>
				<td width="50%" class="contentEntry2">{coment_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_category_access:label_journ}<br/>
					<small>{l_category_access:desc_journ}</small>
				</td>
				<td width="50%" class="contentEntry2">{journ_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_category_access:label_moder}<br/>
					<small>{l_category_access:desc_moder}</small>
				</td>
				<td width="50%" class="contentEntry2">{moder_list}</td>
			</tr>
			<tr>
				<td width="50%" class="contentEntry1">{l_category_access:label_admin}<br/>
					<small>{l_category_access:desc_admin}</small>
				</td>
				<td width="50%" class="contentEntry2">{admin_list}</td>
			</tr>
		</table>
	</fieldset>
	<br/>

	<fieldset>
		<legend><b>{l_category_access:legend_general_text}</b></legend>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" class="contentEntry1" align="center"><TEXTAREA NAME="message" COLS="150" ROWS="15">{message}</TEXTAREA>
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
				<input type="submit" value="{l_category_access:button_save}" class="button"/>
			</td>
		</tr>
	</table>
</form>