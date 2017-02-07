{% for entry in entries %}
	{% if (entry.cat_id) %}
		{{ entry.cat_link }}<br/>
	{% elseif (entry.news_id) %}
		<div style="padding-left:50px">
			<a href="{{ entry.news_link }}">{{ entry.news_title }}</a> {{ entry.news_date|date("d.m.Y H:i") }}</div>
	{% elseif (entry.static_id) %}
		<a href="{{ entry.static_link }}">{{ entry.static_title }}</a> {{ entry.static_date|date("d.m.Y H:i") }}<br/>
	{% endif %}
{% endfor %}

<div style="padding: 10px; text-align:center;">{{ pagination }}</div>
