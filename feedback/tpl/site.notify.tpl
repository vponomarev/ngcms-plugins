{% extends localPath(0) ~ "site.body.tpl" %}
{% block content %}
{{ entries }}
{% if (usermail.count > 0) %}<br/>На ваш адрес ({% for mail in usermail.list %}{{ mail }} {% endfor %}) отправлено email сообщение!{% endif %}
{% endblock %}
