<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		{% if (print == 1) %}
		<tr>
			<td width="50%" class="contentEntry1">Отображение<br /><small></small></td>
			<td width="50%" class="contentEntry2">
				<select size=1 name="type">
							{% if (print_et) %}<option value="0">Основной раздел</option>{% endif %}
						{% for entry in list_forum %}
							<option value="{{ entry.id }}" {% if (entry.id_set == entry.id) %}selected="selected"{% endif %}>{{ entry.title }}</option>
						{% endfor %}
													<!-- <option value="0" >Форум</option>
													<option value="1" selected="selected">Категория</option> -->
				</select>
			</td>
			
		</tr>
		{% endif %}
		<tr>
			<td width="50%" class="contentEntry1">Название форума:<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ Sname }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Описание форума<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="desc" value="{{ Sdesc }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Ключевые слова<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="keyw" value="{{ Skeyw }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Модераторы<br /><small>Укажите логины пользователей через запятую</small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="moder" value="{{ Smoder }}" /></td>
		</tr>
	</table>
	<br />
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center"><input type="submit" name="submit" value="Сохранить форум" class="button" /></td>
		</tr>
	</table>
</form>