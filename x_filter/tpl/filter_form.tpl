<h2>{{ lang['x_filter:title'] }}</h2>
<hr>

<form action="{{ plugin_link }}" method="post">
    <div class="mt-3 form-group">
        <div class="input-group">
            {{ catlist }}
            <div class="input-group-append">
                <button type="submit" class="xf_btn">&#10004;</button>
            </div>
        </div>
    </div>
</form>

<form action="{{ form_action }}" method="post">
    <div class="xf__items">
        
        {% if x_price %}
            <div class="xf__item {{ x_price.active ? 'active' : '' }}">
                <label class="xf__title">{{ x_price.title }}</label>
                <div class="xf__body">{{ x_price.input }}</div>
            </div>
        {% endif %}
        
        {% if x_gender %}
            <div class="xf__item {{ x_gender.active ? 'active' : '' }}">
                <label class="xf__title">{{ x_gender.title }}</label>
                <div class="xf__body">{{ x_gender.input }}</div>
            </div>
        {% endif %}
        
        {% if x_year %}
            <div class="xf__item {{ x_year.active ? 'active' : '' }}">
                <label class="xf__title">{{ x_year.title }}</label>
                <div class="xf__body">{{ x_year.input }}</div>
            </div>
        {% endif %}
        
        <div class="xf__item active">
            <label class="xf__title expanded">{{ lang['x_filter:label.order_by'] }}</label>
            <div class="xf__body">
                <label class="xf_label xf_label-block">
                    <input type="radio" name="order" value="id_desc" class="xf_radio" {{ 'id_desc' == order ? 'checked' : ''  }} />
                    {{ lang['x_filter:order.default'] }}
                </label>
                <label class="xf_label xf_label-block">
                    <input type="radio" name="order" value="title_asc" class="xf_radio" {{ 'title_asc' == order ? 'checked' : ''  }} />
                    {{ lang['x_filter:order.title'] }}
                </label>
                <label class="xf_label xf_label-block">
                    <input type="radio" name="order" value="postdate_asc" class="xf_radio" {{ 'postdate_asc' == order ? 'checked' : ''  }} />
                    {{ lang['x_filter:order.date'] }}
                </label>
            </div>
        </div>
    </div>
    
    <div class="xf__submit">
        <button type="submit" class="xf_btn xf_btn-block">{{ lang['x_filter:btn.apply_filter'] }}</button>
    </div>
</form>
