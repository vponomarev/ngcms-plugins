{% if (submit) %}
	<div id="searchform" class="blockform">
		<h2><span>Поиск</span></h2>
		<div class="box">
			<form id="search" method="post" action="">
				<div class="inform">
					<fieldset>
						<legend>Укажите критерий для поиска</legend>
						<div class="infldset">
							<label class="conl">Ключевые
								слова<br/><input type="text" name="keywords" size="40" maxlength="100"/><br/></label>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend>Выберите где искать</legend>
						<div class="infldset">
							<label class="conl">Форум<br/>
								<select id="forum" name="forum_id">
									<option value='0'>Все имеющиеся</option>
									{% for entry in entries %}
										<option value='{{ entry.forum_id }}'>{{ entry.forum_name }}</option>
									{% endfor %}
								</select>
								<br/></label>
							<label class="conl">Поиск в<br/>
								<select name="search_in">
									<option value='all' selected>Текстах сообщений и заголовках тем</option>
									<option value='post'>Только в текстах сообщений</option>
									<option value='topic'>Только в заголовках тем</option>
								</select>
								<br/></label>
							<p class="clearb">Выберите в каком форуме вы желаете искать и место поиска.</p>
						</div>
					</fieldset>
				</div>
				<p><input type="submit" name="submit" value="Отправить" accesskey="s"/></p>
			</form>
		</div>
	</div>
{% else %}
	<div class="linkst">
		<div class="inbox">
			<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
			<div class="clearer"></div>
		</div>
	</div>
	<div id="vf" class="blocktable">
		<h2><span>Результаты поиска</span></h2>
		<div class="box">
			<div class="inbox">
				<table cellspacing="0">
					<thead>
					<tr>
						<th class="tcl" scope="col">Тема</th>
						<th class="tcl" scope="col">Найденое в сообщениях</th>
					</tr>
					</thead>
					<tbody>
					{% for entry in entries %}
						<tr>
							<td class="tcl">
								<div class="intd">
									<div class="icon">
										<div class="nosize"><!-- --></div>
									</div>
									<div class="tclcon">
										<a href='{{ entry.topic_link }}'>{{ entry.subject }}</a> <span class='byuser'>оставил&nbsp;{{ entry.user }}</span>
									</div>
								</div>
							</td>
							<td class="tc2">{{ entry.message }}</td>
						</tr>
					{% else %}
						<tr>
							<td class="tcl">
								<div class="intd">
									<div class="icon">
										<div class="nosize"><!-- --></div>
									</div>
									<div class="tclcon">
										По вашему запросу <b>'.$get_url.'</b> ничего не найдено
									</div>
								</div>
							</td>
							<td class="tc2"></td>
						</tr>
					{% endfor %}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="linkst">
		<div class="inbox">
			<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
			<div class="clearer"></div>
		</div>
	</div>
{% endif %}