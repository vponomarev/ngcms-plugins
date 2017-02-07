<h1>{{ lang['guestbook']['mail_title'] }}</h1>
<div>
	<p>{{ lang['guestbook']['mail_date'] }}: {{ time|date("d.m.Y H:i") }}</p>
	<p>{{ lang['guestbook']['mail_ip'] }}: {{ ip }}</p>
	<p>{{ lang['guestbook']['mail_author'] }}: {{ author }}</p>
	<p>{{ lang['guestbook']['mail_message'] }}: {{ message }}</p>
	{% for field in fields %}
		<p>{{ field.name }}: {{ field.value }}</p>
	{% endfor %}
</div>
