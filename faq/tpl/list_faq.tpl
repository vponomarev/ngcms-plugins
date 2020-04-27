<form action="{{ home }}/engine/admin.php?mod=extra-config&plugin=faq&action=modify" method="post" name="check_faq">
	<!-- List of news start here -->
	<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
		<tr class="contHead" align="left">
			<td width="5%">ID</td>
			<td width="35%">Вопрос</td>
			<td width="35%">Ответ</td>
			<td width="15%">Активна?</td>
			<td width="5%">
				<input class="check" type="checkbox" name="master_box" title="Выбрать все" onclick="javascript:check_uncheck_all(check_faq)"/>
			</td>
		</tr>
		{% for entry in entries %}
			<tr align="left">
				<td width="5%" class="contentEntry1">
					<a href="?mod=extra-config&plugin=faq&action=edit_faq&id={{ entry.id }}"/>{{ entry.id }}</a></td>
				<td width="40%" class="contentEntry1">{{ entry.question }}</td>
				<td width="15%" class="contentEntry1">{{ entry.answer }}</td>
				<td width="15%" class="contentEntry1">{% if (entry.active == "1") %}Да{% else %}Нет{% endif %}</td>
				<td width="5%" class="contentEntry1">
					<input name="selected_faq[]" value="{{ entry.id }}" class="check" type="checkbox"/></td>
			</tr>
		{% else %}
			<tr align="left">
				<td colspan="10" class="contentEntry1">Нет добавленных записей.</td>
			</tr>
		{% endfor %}
		<tr>
			<td width="100%" colspan="10">&nbsp;</td>
		</tr>

		<tr align="center">
			<td colspan="10" class="contentEdit" align="right" valign="top">
				<div style="text-align: left;">
					Действие: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
						<option value="">-- Действие --</option>
						<option value="mass_approve">Активировать</option>
						<option value="mass_forbidden">Деактивировать</option>
						<option value="" style="background-color: #E0E0E0;" disabled="disabled">===================
						</option>
						<option value="mass_delete">Удалить</option>
					</select>
					<input type="submit" value="Выполнить.." class="button"/>
					<br/>
				</div>
			</td>
		</tr>
</form>

<tr>
	<td width="100%" colspan="10">&nbsp;</td>
</tr>
<tr>
	<td align="center" colspan="10" class="contentHead">{{ pagesss }}</td>
</tr>
</table>