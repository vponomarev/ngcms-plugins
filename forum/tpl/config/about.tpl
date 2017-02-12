<script type="text/javascript">
	function ChangeOption(selectedOption) {
		document.getElementById('about').style.display = (selectedOption == 'about') ? "block" : "none";
		document.getElementById('author').style.display = (selectedOption == 'author') ? "block" : "none";
		document.getElementById('acknowledgments').style.display = (selectedOption == 'acknowledgments') ? "block" : "none";
		document.getElementById('support').style.display = (selectedOption == 'support') ? "block" : "none";
	}
</script>
<input type="button" onmousedown="javascript:ChangeOption('about')" value="О плагине" class="button"/>
<input type="button" onmousedown="javascript:ChangeOption('author')" value="Авторы" class="button"/>
<input type="button" onmousedown="javascript:ChangeOption('acknowledgments')" value="Благодарности" class="button"/>
<input type="button" onmousedown="javascript:ChangeOption('support')" value="Поддержка" class="button"/>

<fieldset id="author" style="display: none;" class="admGroup">
	<legend class="title">Авторы</legend>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<dl>
			<dt>
			<center><strong>Nail' Davydov</strong></center>
			</dt>
			<dt>
			<center><strong><a href="http://rozard.net" target="_blank">http://rozard.net</a></strong></center>
			</dt>
			<dt>
			<center><strong><a href="http://rozard.ngdemo.ru/" target="_blank">http://rozard.ngdemo.ru/</a></strong>
			</center>
			</dt><br/>
			<dt>
			<center>© 2009-2014 Nail' Davydov</center>
			</dt>
		</dl>
	</table>
</fieldset>


<fieldset id="about" class="admGroup">
	<legend class="title">О плагине</legend>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<dl>
			<dt>
			<center><strong>Forum Next Generation CMS</strong></center>
			</dt><br/>
			<dt>
			<center></center>
			</dt><br/>
			<dt>
			<center>© 2009-2014 Nail' Davydov</center>
			</dt>
		</dl>
	</table>
</fieldset>

<fieldset id="acknowledgments" style="display: none;" class="admGroup">
	<legend class="title">Благодарности</legend>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<dl>
			<dt><strong>Закрытое тестирование и аудит:</strong></dt>
			<dd>Sergey Rostunov -(<a href="http://ngcms.ru/forum/profile.php?id=62">infinity237</a>)</dd>
			<dt>
			<center>© 2009-2014 Nail' Davydov</center>
			</dt>
		</dl>
	</table>
</fieldset>
<fieldset id="support" style="display: none;" class="admGroup">
	<legend class="title">Поддержка</legend>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<dl>
			<dt><strong>По всем проблема и предложениям обращаться на:
					<a href="http://ngcms.ru/forum/viewtopic.php?id=592" target="_blank"><b>Тестирование и обсуждения
							плагина форум</b></a></strong></dt>
			<br/>
			<dt>
			<center>© 2009-2014 Nail' Davydov</center>
			</dt>
		</dl>
	</table>
</fieldset>