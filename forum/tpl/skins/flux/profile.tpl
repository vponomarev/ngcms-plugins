{% if (action.profile) %}
	<div id="viewprofile" class="block">
		<h2><span>Профиль</span></h2>
		<div class="box">
			<div class="fakeform">
				<div class="inform">
					<fieldset>
						<legend>Персональный</legend>
						<div class="infldset">
							<dl>
								<dt>Имя:</dt>
								<dd>{{ name }}</dd>
								<dt>Статус:</dt>
								<dd>{{ status }}</dd>
								{% if (site.true) %}
									<dt>Вебсайт:</dt>
									<dd>{{ site.print }}&nbsp;</dd>
								{% endif %}
								<dt>E-mail:</dt>
								<dd>{% if (auth_admin) %}{{ mail }}{% else %}Скрыт{% endif %}</dd>

							</dl>
							<div class="clearer"></div>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend>Индивидуальный</legend>
						<div class="infldset">
							<dl>
								<dt>Подпись:</dt>
								<dd>{{ signature }}</dd>
							</dl>
							<div class="clearer"></div>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend>Активность пользователя</legend>
						<div class="infldset">
							<dl>
								<dt>Сообщений:</dt>
								<dd>{{ totalposts }}</dd>
								<dt>Последний раз был:</dt>
								<dd>{% if (lastvisit) %}{{ lastvisit|date("d-m-Y - H:i") }}{% else %}не было{% endif %}</dd>
								<dt>Последнее сообщение:</dt>
								<dd>{{ lastpost|date("d-m-Y - H:i") }}</dd>
								<dt>Зарегистрирован:</dt>
								<dd>{{ regdate|date("d-m-Y - H:i") }}</dd>
							</dl>
							<div class="clearer"></div>
						</div>
					</fieldset>
				</div>
				{% if (auth_user) %}<p><a href='{{ edit }}'>Редактировать</a></p>{% endif %}
			</div>
		</div>
	</div>
{% elseif action.edit %}
	<form id="post" method="post" action="">
		<div id="viewprofile" class="block">
			<h2><span>Редактировать</span></h2>
			<div class="box">
				<div class="fakeform">
					<div class="inform">
						<fieldset>
							<legend>Персональный</legend>
							<div class="infldset">
								<dl>
									<dt>Имя:</dt>
									<dd>{{ name }}</dd>
									<dt>Статус:</dt>
									<dd>{{ status }}</dd>
									<dt>Вебсайт:</dt>
									<dd>
										<input type="text" name="site" value="{{ site.print }}" size="40" maxlength="50"/>{{ error.url.print }}
									</dd>
									<dt>E-mail:</dt>
									<dd>
										<input type="text" name="mail" value="{{ mail }}" size="40" maxlength="50"/>{{ error.url.mail }}
									</dd>
								</dl>
								<div class="clearer"></div>
							</div>
						</fieldset>
					</div>
					<div class="inform">
						<fieldset>
							<legend>Индивидуальный</legend>
							<div class="infldset">
								<dl>
									<dt>Подпись:</dt>
									<dd><textarea name="signature" rows="4" cols="65">{{ signature }}</textarea><br/>
									</dd>
								</dl>
								<div class="clearer"></div>
							</div>
						</fieldset>
					</div>
					<p>
						<input type="submit" name="submit" value="Отправить" tabindex="5" accesskey="s"/><a href="javascript:history.go(-1)">Вернуться
							назад</a></p>
				</div>
			</div>
		</div>
	</form>
{% endif %}