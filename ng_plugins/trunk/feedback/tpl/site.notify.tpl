{% extends localPath(0) ~ "site.body.tpl" %}
{% block content %}
{{ entries }}
{% if (usermail.count > 0) %}<br/>�� ��� ����� ({% for mail in usermail.list %}{{ mail }} {% endfor %}) ���������� email ���������!{% endif %}
{% endblock %}
