<form method="post" action="">
	<tr>
		<td colspan=2>
			<fieldset class="admGroup">
				<legend class="title">Настройки</legend>
				<table width="100%" border="0" class="content">
					<tr>
						<td class="contentEntry1" valign=top>Включить отложенную рассылку?<br/></td>
						<td class="contentEntry2" valign=top><select name="delayed_send">{delayed_send}</select></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="admGroup">
				<legend class="title">Настройки админки</legend>
				<table width="100%" border="0" class="content">
					<tr>
						<td class="contentEntry1" valign=top>Количество объектов на странице<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="admin_count" type="text" title="Количество объектов на странице" size="4" value="{admin_count}"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input name="submit" type="submit" value="Сохранить" class="button"/>
			</td>
		</tr>
	</table>
</form>