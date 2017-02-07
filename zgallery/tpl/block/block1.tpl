{% for entry in entries %}

	<div class="uk-width-medium-1-2" style="min-height: 347px;">

		<article class="uk-article" data-permalink="">
			<div>
				<p>
					{% if entry.preview == 1 %}
						<img alt="" height="900" src="{{ entry.thumburl }}" width="900">
					{% else %}
						<img alt="" height="900" src="{{ entry.fileurl }}" width="900">
					{% endif %}
				</p>
			</div>
		</article>

	</div>

{% endfor %}
