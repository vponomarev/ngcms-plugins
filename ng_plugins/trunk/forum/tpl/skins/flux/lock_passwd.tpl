<div class="blockform">
	<h2><span>Доступ на форум под паролем</span></h2> 
	<div class="box"> 
		<form method="post" action=""> 
			<div class="inform"> 
				<fieldset> 
					<legend>Введите пароль</legend> 
						<div class="infldset"> 
							{% if (error_text['empty_passwd']) %}Вы не ввели пароль<br />{% endif %}
							{% if (error_text['error_passwd']) %}Неверный пароль{% endif %}
							<label class="conl"><strong>Пароль</strong><br /><input type="password" name="lock_passwd" size="30" maxlength="30" tabindex="2" value="{{ lock_passwd }}" /><br /></label> 
						</div> 
				</fieldset> 
			</div> 
			<p><input type="submit" name="submit" value="Зайти" tabindex="3" /></p> 
		</form> 
	</div> 
</div>