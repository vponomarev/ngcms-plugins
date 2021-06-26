<div class="frame-crumbs">
    <div class="crumbs" >
        <div class="container">
            <ul class="items items-crumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
                {% for loc in location %}
                <li class="btn-crumb" itemprop="itemListElement" itemscope  itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="{{ loc.url }}" ><span class="text-el" itemprop="name">{{ loc.title }}</span></a>
		    <meta itemprop="position" content="1" />
                    <span class="divider">/</span>
                </li>
                {% endfor %}
                {% if (location_last) %}
                <li class="btn-crumb" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="{{ news.url.full }}">
                    <span class="text-el" itemprop="name">{{ location_last }}</span></a>
		     <meta itemprop="position" content="2" />
                </li>
                {% endif %}
            </ul>
        </div>
    </div>
</div>
