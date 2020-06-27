<?xml version="1.0" encoding="{{ lang['encoding'] }}"?>
<rss xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0">
    <channel>
        <link>{{ link }}</link>
        <title>{{ title }}</title>
        <description>{{ description }}</description>
        {# <turbo:analytics id="88888888" type="Yandex"></turbo:analytics> #}
        <language>{{ language | slice(0,2) }}</language>

        {% for entry in entries %}
			{{ entry }}
        {% else %}
            {{ lang.theme.msgi_no_news }}
        {% endfor %}

    </channel>
</rss>
