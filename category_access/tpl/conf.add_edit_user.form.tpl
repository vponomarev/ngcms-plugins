<form method="post" action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=add_user[edit]&amp;user={user}[/edit]">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%" class="contentEntry1">{l_category_access:label_user_name}<br/>
				<small>{l_category_access:desc_user}</small>
			</td>
			<td width="50%" class="contentEntry2">{user_list}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">{l_category_access:label_category}<br/>
				<small>{l_category_access:desc_category}</small>
			</td>
			<td width="50%" class="contentEntry2">{category_list}</td>
		</tr>
	</table>
	<br/>


	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" value="[add]{l_category_access:button_add_user}[/add][edit]{l_category_access:button_edit_user}[/edit]" class="button"/>
			</td>
		</tr>
	</table>
</form>
