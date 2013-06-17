<script language="javascript">
				function forum_change(){
					if (document.getElementById('forum_captcha').className == "yellow"){
						document.getElementById('forum_captcha').className = "red";
						document.getElementById('forum_captcha_sess').value = 0;
					} else {
						document.getElementById('forum_captcha').className = "yellow";
						document.getElementById('forum_captcha_sess').value = 1;
					}
				}
</script>
<div class="blockform">
	{{ error }}
	<h2><span>Зайти</span></h2> 
	<div class="box"> 
		<div class="inbox"> 
			<p>{{ info }}</p> 
		</div> 
		<form method="post" action="{{ action }}"> 
			<div class="inform"> 
				<fieldset> 
					<legend>Введите ваше имя и пароль ниже</legend> 
						<div class="infldset"> 
							<label class="conl"><strong>Имя</strong><br /><input type="text" name="username" value="{{ username }}" size="25" maxlength="25" tabindex="1" /><br /></label> 
							<label class="conl"><strong>Пароль</strong><br /><input type="text" name="password" size="16" maxlength="16" tabindex="2" /><br /></label> 
							<p class="clearb">Поставь галочку если человек: <input type="checkbox" id="forum_captcha" onclick="forum_change();" value="1"></p>
							<input type="hidden" name="forum_captcha_sess" id="forum_captcha_sess" value="0">
						</div> 
				</fieldset> 
			</div> 
			<p><input type="submit" name="submit" value="Зайти" tabindex="3" /></p> 
		</form> 
	</div> 
</div>