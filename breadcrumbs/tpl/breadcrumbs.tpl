<div class="frame-crumbs">
    <div class="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
        <div class="container">
            <ul class="items items-crumbs">
                {% for loc in location %}
                <li class="btn-crumb">
                    <a href="{{ loc.url }}" typeof="v:Breadcrumb"><span class="text-el">{{ loc.title }}</span></a>
                    <span class="divider">/</span>
                </li>
                {% endfor %}
                {% if (location_last) %}
                <li class="btn-crumb">
                    <button typeof="v:Breadcrumb" disabled="disabled">
                    <span class="text-el">{{ location_last }}</span>
                    </button>
                </li>
                {% endif %}
            </ul>
        </div>
    </div>
</div>

