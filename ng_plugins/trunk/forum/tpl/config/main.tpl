<div style="text-align : left;">
	<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="100%" colspan="2" class="contentHead"><img src="{{ skins_url }}/images/nav.gif" hspace="8" alt="" />Настройка компонета: форума => {{ global }} </td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr align="center">
			<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum'" value="Общие" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=permission'" value="Права пользователя" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=ads'" value="Объявления" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=rules'" value="Правила" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=list_forum'" value="Список форумов" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=list_news'" value="Список новостей" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=list_complaints'" value="Список жалоб" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url'" value="ЧПУ" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=title'" value="Управление заголовками форума" class="navbutton" />
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=about'" value="О плагине" class="navbutton" />
			</td>
		</tr>
	</table><br />
	{% if (info) %}
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="contentNav">
		<tr align="center">
			<td>
			<font color="red">
			{{ info }}
			</font>
			</td>
		</tr>
	</table><br />
	{% endif %}
	{{ entries }}
</div>