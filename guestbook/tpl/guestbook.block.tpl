{% for entry in entries %}
	<div class="review col-xs-12 col-sm-6">
		<div class="review-inner">
			<div class="review-header">
				{% if entry.social %}
					{% if entry.social.Vkontakte.photo %}
						{% set avatar = entry.social.Vkontakte.photo %}
					{% elseif entry.social.Facebook.photo %}
						{% set avatar = entry.social.Facebook.photo %}
					{% elseif entry.social.Instagram.photo %}
						{% set avatar = entry.social.Instagram.photo %}
					{% elseif entry.social.Google.photo %}
						{% set avatar = entry.social.Google.photo %}
					{% endif %}
				{% else %}
					{% set avatar = '/uploads/avatars/noavatar.gif' %}
				{% endif %}
				<div class="person-photo"><img src="{{ avatar }}" width="60" height="60"></div>
				<div class="person-name">{% if entry.author == 'guest' %} {{ entry.fields.firstname.value }} {{ entry.fields.lastname.value }}{% else %}{{ entry.author }}{% endif %}</div>
				<div class="review-date">{{ entry.date }}</div>
				<div class="review-subject">Ремонтировали - {{ entry.fields.item.value }}</div>
			</div>
			<div class="review-caption"><p>{{ entry.message }}</p></div>
			<div class="review-social">
				<ul class="social-links social-links-default list-inline">
					{% if entry.social.Vkontakte %}
						<li class="active"><a href="{{ entry.social.Vkontakte.link }}">
								<svg class="icon icon-vk">
									<use xlink:href="#icon-vk"></use>
								</svg>
							</a></li>
					{% else %}
						<li>
							<svg class="icon icon-vk">
								<use xlink:href="#icon-vk"></use>
							</svg>
						</li>
					{% endif %}
					{% if entry.social.Google %}
						<li class="active"><a href="{{ entry.social.Google.link }}">
								<svg class="icon icon-google">
									<use xlink:href="#icon-google"></use>
								</svg>
							</a></li>
					{% else %}
						<li>
							<svg class="icon icon-google">
								<use xlink:href="#icon-google"></use>
							</svg>
						</li>
					{% endif %}
					{% if entry.social.Facebook %}
						<li class="active"><a href="{{ entry.social.Facebook.link }}">
								<svg class="icon icon-facebook">
									<use xlink:href="#icon-facebook"></use>
								</svg>
							</a></li>
					{% else %}
						<li>
							<svg class="icon icon-facebook">
								<use xlink:href="#icon-facebook"></use>
							</svg>
						</li>
					{% endif %}
					{% if entry.social.Instagram %}
						<li class="active"><a href="{{ entry.social.Instagram.link }}">
								<svg class="icon icon-instagram">
									<use xlink:href="#icon-instagram"></use>
								</svg>
							</a></li>
					{% else %}
						<li>
							<svg class="icon icon-instagram">
								<use xlink:href="#icon-instagram"></use>
							</svg>
						</li>
					{% endif %}
				</ul>
			</div>
		</div>
	</div>
{% endfor %}
