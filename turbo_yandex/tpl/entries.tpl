<item turbo="true">
    <link>{{ home ~ news.url.full }}</link>
    <title>{{ news.title }}</title>
    <pubDate>{{ news.dateStamp | date('D, d M y H:i:s O') }}</pubDate>
    <turbo:content>
        <![CDATA[
            <header>
                <h1>{{ news.title }}</h1>
                {#
                    <h2>номер телефона</h2>
                    <menu>
                        <a href="{{ home }}">Главная</a>
                        <a href="{{ home }}/catalog">Каталог</a>
                        <a href="{{ home }}/static/contacts.html">Контакты</a>
                    </menu>
                #}
            </header>

            {# Отображение короткой и полной новости с предварительно вырезанными недопустимыми тегами. #}
            <p>{{ news.short | striptags }}</p>

            {{ news.full | striptags('<p><figure><img><iframe><br><ul><ol><li><b><strong><i><em><sup><sub><ins><del><small><big><pre></pre><abbr><u><a>') | replace({
                'src="/': 'src="' ~ home ~ '/',
                'src="../': 'src="' ~ home ~ '/',
            }) }}

            {#
                Примеры самостоятельного вырезания тегов и укорачивания содержимого.

                <p>{{ news.full | striptags }}</p>
                <p>{{ news.full | striptags('<figure><img><p><br><ul><ol><li><b><i><u><pre></pre><a>') }}</p>
                <p>{{ news.full | striptags | truncateHTML(350, '...') }}</p>
            #}

            {#
                Пример замены относительных ссылок на абсолютные.

                {{ news.full | replace({
                    'src="/': 'src="' ~ home ~ '/',
                    'src="../': 'src="' ~ home ~ '/',
                }) }}
            #}

            {#
                Пример простого вывода всех изображений.

                {% for image in news.embed.images %}
                    <figure>
                        <img src="{{ image.url }}" alt="" />
                    </figure>
                {% endfor %}
            #}

            {#
                Примеры использования доп. полей.

                1) Если поле имеет текстовый тип.
                {{ p.xfields.specification.value ? p.xfields.specification.value : 'Характеристики не указаны' }}

                2) Если поле имеет числовой тип.
                {% if p.xfields.price.value >= 0 %}
                    стоимость от {{ p.xfields.price.value }} рублей
                {% endif %}

                3) Если поле представляет собой группу изображений.
                {% for image in p.xfields.poster.entries %}
                    <figure>
                        <img src="{{ image.url }}" alt="" />
                    </figure>
                {% endfor %}
            #}

            {#
                Пример отображения главной категории и дерева категорий новости.

                {{ news.categories.masterText }}

                {% for category in news.categories.list %}
                    <a href="{{ category.url }}">{{ category.name }}</a>
                {% endfor %}
            #}
        ]]>
    </turbo:content>
</item>
