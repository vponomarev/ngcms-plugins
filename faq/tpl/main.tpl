<div style="text-align : left;">
	<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="100%" colspan="2" class="contentHead">
				<img src="{{ skins_url }}/images/nav.gif" hspace="8" alt=""/><a href="admin.php?mod=extras" title="Плагины">Плагины</a>
				&#8594; <a href="{{ admin_url }}/admin.php?mod=extra-config&plugin=faq">Вопросы и ответы</a></td>
		</tr>

	</table>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr align="center">
			<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=faq'" value="Список вопросов" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=faq&action=add_faq'" value="Добавить вопрос" class="navbutton"/>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>

	{{ entries }}

</div>