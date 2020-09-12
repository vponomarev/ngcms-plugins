<div class="block block-site_stats">
    <h3 class="block-title">{{ lang['site_stats:block-stats'] }}</h3>
    <ul>
        {% for entry in stats %}
            <li>{{ entry.title }}: <b>{{ entry.count }}</b></li>
        {% endfor %}
    </ul>

    <h3 class="block-title">{{ lang['site_stats:block-online'] }}</h3>
    <ul>
        {% for entry in online %}
            <li>{{ entry.title }} <b>{{ entry.count }}</b> {{ entry.content }}</li>
        {% endfor %}
    </ul>
</div>
