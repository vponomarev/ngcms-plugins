<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td colspan=2>
			<fieldset class="admGroup">
				<legend class="title">Настройки</legend>
				<table width="100%" border="0" class="content">
					<tr>
						<td class="contentEntry1" valign=top>Чпу для главной страницы<br/></td>
						<td class="contentEntry2" valign=top>{% if (home_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=home&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=home&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для страницы регистрации<br/></td>
						<td class="contentEntry2" valign=top>{% if (register_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=register&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=register&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для входа на форум<br/></td>
						<td class="contentEntry2" valign=top>{% if (login_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=login&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=login&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для выхода из форума<br/></td>
						<td class="contentEntry2" valign=top>{% if (out_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=out&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=out&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для добавления сообщения<br/></td>
						<td class="contentEntry2" valign=top>{% if (addreply_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=addreply&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=addreply&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для создания новой темы<br/></td>
						<td class="contentEntry2" valign=top>{% if (newtopic_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=newtopic&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=newtopic&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для профиля пользователя и редактирования<br/></td>
						<td class="contentEntry2" valign=top>{% if (profile_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=profile&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=profile&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для страницы фрумов<br/></td>
						<td class="contentEntry2" valign=top>{% if (showforum_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=showforum&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=showforum&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Чпу для страницы темы<br/></td>
						<td class="contentEntry2" valign=top>{% if (showtopic_forum) %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=showtopic&s=0'" value="Выключить" />{% else %}
								<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=url&id=showtopic&s=1'" value="Включить" />{% endif %}
						</td>
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
</table>