
    
        {% for entry in stats %}
            &#x2714;{{ entry.title }}: <b>{{ entry.count }}</b><br>
        {% endfor %}
    

    
    
        {% for entry in online %}
            &#x2714;{{ entry.title }}: <b>{{ entry.count }}</b> {{ entry.content }}<br>
        {% endfor %}
    

