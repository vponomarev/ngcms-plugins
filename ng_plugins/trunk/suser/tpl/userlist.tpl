<div class="blockform">
	<h2><span>Поиск пользователей</span></h2>
	<div class="box">
	<form id="userlist" method="get" action="">
		<div class="inform">
			<fieldset>
				<legend>Поиск и сортировка пользователей</legend>
				<div class="infldset">
					<label class="conl">Имя<br /><input type="text" name="username" value="{{ username }}" size="25" maxlength="25" /><br /></label>
					<label class="conl">Группа
					<br /><select name="show_group">
						<option value="-1" {% if (show_group_) %}selected{% endif %}>Все пользователи</option>
						<option value="1" {% if (show_group_1) %}selected{% endif %}>Администраторы</option>
						<option value="2" {% if (show_group_2) %}selected{% endif %}>Редактор</option>
						<option value="3" {% if (show_group_3) %}selected{% endif %}>Журналист</option>
						<option value="4" {% if (show_group_4) %}selected{% endif %}>Комментатор</option>
					</select>
					<br /></label>
					<label class="conl">Сортировать по
					<br /><select name="sort_by">
						<option value="username" {% if (sort_by_username) %}selected{% endif %}>Имя</option>
						<option value="registered" {% if (sort_by_registered) %}selected{% endif %}>Зарегистрирован</option>
						<option value="num_posts" {% if (sort_by_num_posts) %}selected{% endif %}>Кол-во новостей</option>
						<option value="num_comments" {% if (sort_by_num_comments) %}selected{% endif %}>Кол-во комментариев</option>
					</select>
					<br /></label>
					<label class="conl">Упорядочить по
					<br /><select name="sort_dir">
						<option value="ASC" {% if (sort_dir_ASC) %}selected{% endif %}>Возрастанию</option>
						<option value="DESC" {% if (sort_dir_DESC) %}selected{% endif %}>Убыванию</option>
					</select>
					<br /></label>
					<p class="clearb">Введите имя пользователя для поиска и/или укажите группу. Имя пользователя может быть пустым. Отсортируйте пользователей по имени, дате регистрации или количеству переданных сообщений и упорядочите по возрастанию/убыванию.</p>
				</div><br/>
		<p><input type="submit" name="submit" value="Отправить" /> <input type="submit" name="reset" value="Сброс" /></p>
			</fieldset>
		</div>
	</form>
	</div>
</div>
<br/><br/>
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
	</div> 
</div>
<div id="users1" class="blocktable"> 
	<h2><span>Пользователи</span></h2> 
	<div class="box"> 
		<div class="inbox"> 
			<table cellspacing="0"> 
				<thead> 
					<tr> 
						<th class="tcl" scope="col">Имя</th> 
						<th class="tc2" scope="col">Статус</th> 
						<th class="tc3" scope="col">Новостей</th>
						<th class="tc4" scope="col">Комментариев</th> 
						<th class="tc5" scope="col">Зарегистрирован</th> 						
						<th class="tcr" scope="col">Последний вход</th> 
					</tr> 
				</thead> 
				<tbody>
				{% for entry in entries %}
					<tr> 
						<td class="tcl"><a href='{{ entry.profile_link }}'>{{ entry.profile }}</a></td> 
						<td class="tc2">{{ entry.status }}</td> 
						<td class="tc3">{{ entry.news }}</td>
						<td class="tc4">{{ entry.com }}</td> 
						<td class="tc5">{{ entry.reg|date("d-m-Y h:i") }}</td>						
						<td class="tcr">{% if (entry.last != 0) %}{{ entry.last|date("d-m-Y h:i") }} {% else %} не был ни разу {% endif %}</td> 
					</tr>
				{% else %}
					<tr> 
						<td class="tcl">По вашему запросу ничего не найдено.</td> 
						<td class="tc2"></td> 
						<td class="tc3"></td> 
						<td class="tcr"></td> 
					</tr>
				{% endfor %}
				</tbody> 
			</table> 
		</div> 
	</div> 
</div>
<br/>
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<div class="clearer"></div> 
	</div> 
</div>