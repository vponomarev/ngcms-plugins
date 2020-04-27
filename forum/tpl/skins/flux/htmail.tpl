<html>
<body>
<div style="font: normal 11px verdana, sans-serif;">
	<h3>Уважаемый {{ global.user.name }}!</h3>
	Только что было оставлено новое сообщение в теме на которую вы подписаны.<br/>
	<br/>
	{{ from_user }} написал ответ в теме {{ url }}
	<table width="100%" cellspacing="1" cellpadding="1">
		{{ message }}
	</table>
	<br/>

	<br/>
	---<br/>
	С уважением,<br/>
	почтовый робот (работает на базе <b><font color="#90b500">N</font><font color="#5a5047">ext</font>
		<font color="#90b500">G</font><font color="#5a5047">eneration</font> Forum</b> -
	http://rozard.ngdemo.ru/mod:forum/)
</div>
</body>
</html>

