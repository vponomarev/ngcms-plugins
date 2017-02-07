<div style="text-align : left;">
	<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt=""/>Настройка
				плагина: Подписка на комментарии => {global}
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr align="center">
			<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
				<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=subscribe_comments'" value="Общие настройки" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=subscribe_comments&action=list_subscribe'" value="Список подписчиков" class="navbutton"/>
				[hide_delayed]<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=subscribe_comments&action=list_subscribe_post'" value="Сформированные письма" class="navbutton"/>[/hide_delayed]
			</td>
		</tr>
	</table>
	<br/>

	{entries_cron}

	{entries}

</div>