<form method="post" id="commit_delete" action="admin.php?mod=extra-config&amp;plugin=multi_main&amp;action=dell">
	<input type="hidden" name="cat" value="{cat}"/>
	<input type="hidden" id="commit" name="commit" value="no"/>
	<div align="center">{commit}</div>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" value="{l_multi_main:button_cancel}" class="button"/>&#160;
				<input type="submit" onclick="document.forms['commit_delete'].elements['commit'].value='yes'; return true;" value="{l_multi_main:button_dell}" class="button"/>
			</td>
		</tr>
	</table>
</form>