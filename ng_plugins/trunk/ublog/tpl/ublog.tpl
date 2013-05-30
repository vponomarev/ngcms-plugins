{% for entry in news.entries %}
{{ entry }}
{% endfor %}

{% if pages.total > 1 %}
{{ pages.output }}
{% endif %}