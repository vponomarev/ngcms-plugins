<div class="blockform">
	<h2><span>Регистрация</span></h2>
	<div class="box">
		<form method="post" action="" enctype="multipart/form-data">
			<div class="inform">
				<div class="forminfo">
					<h3>Важная информация</h3>
					<p>Регистрация не обязательна, но она предоставит вам доступ к дополнительным возможностям.
						Например, появится возможность редактировать и удалять свои сообщения, добавлять собственную
						подпись ко всем сообщениям, подписаться на уведомления о новых сообщениях на форуме. Если у вас
						есть какие-нибудь вопросы относительно этого форума, вы можете обратиться к администратору.</p>
					<p>Ниже приведена форма, которую Вы должны заполнить для того, чтобы зарегистрироваться. После
						регистрации, Вы должны посетить свой профиль и просмотреть введенную информацию, которую Вы
						сможете отредактировать в случае необходимости. Поля, заполняемые ниже - это только небольшая
						часть всех возможностей, которые Вы сможете изменять в своем профиле.</p>
				</div>
				<fieldset>
					<legend>Введите имя пользователя длиной от 2 до 25 символов</legend>
					<div class="infldset">
						<input type="hidden" name="form_sent" value="1"/>
						<label><strong>Имя</strong><br/><input type="text" name="name" maxlength="30" value="{{ name }}"/><br/></label>
						{{ error.name.print }}
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend>Введите и подтвердите свой пароль</legend>
					<div class="infldset">
						<label class="conl"><strong>Пароль</strong><br/><input type="text" name="password" maxlength="30" value=""/><br/></label>
						<label class="conl"><strong>Подтвердите
								пароль</strong><br/><input type="text" name="confirm" maxlength="30" value=""/><br/></label>
						<p class="clearb">Пароль должен быть не менее 4 и не более 16 символов в длину. Пароль
							чувствителен к регистру символов.</p>
						{{ error.password.print }}
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend>Введите правильный e-mail адрес</legend>
					<div class="infldset">
						<label><strong>E-mail</strong><br/>
							<input type="text" name="mail" maxlength="60" value="{{ mail }}"/><br/></label>
						{{ error.mail.print }}
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend>Введите цифры с картинки</legend>
					<div class="infldset">
						<label><strong>Капча</strong><br/>
							<input type="text" name="captcha" maxlength="5" size="30"/><img src="{{ url_captcha }}"/><br/></label>
						{{ error.captcha.print }}
					</div>
				</fieldset>
			</div>
			<script language="javascript">
				function forum_change() {
					if (document.getElementById('forum_captcha').className == "yellow") {
						document.getElementById('forum_captcha').className = "red";
						document.getElementById('forum_captcha_sess').value = 0;
					} else {
						document.getElementById('forum_captcha').className = "yellow";
						document.getElementById('forum_captcha_sess').value = 1;
					}
				}
			</script>
			<div class="inform">
				<fieldset>
					<legend>Я не робот.</legend>
					<input type="checkbox" id="forum_captcha" onclick="forum_change();" value="Я - человек!">
					<input type="hidden" name="forum_captcha_sess" id="forum_captcha_sess" value="0">
					{{ error.bot.print }}
				</fieldset>
			</div>
			<p><input type="submit" name="submit" value="Регистрация"/></p>
		</form>
	</div>
</div> 