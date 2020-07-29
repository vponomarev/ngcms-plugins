<table class="table table-striped">
	<tbody>
		{% for image in images %}
		<tr>
			<td>{{ image.number }}</td>
			{% if image.flags.exist %}
				<td>
					<input type="text" name="xfields_{{ image.id }}_dscr[{{ image.image.id }}]" value="{{ image.description }}" placeholder="Введите описание..." class="form-control mb-2" />
					<figure class="figure mb-0">
						<a href="{{ image.image.url }}" target="_blank">
							{% if image.flags.preview %}
								<img src="{{ image.preview.url }}" width="{{ image.preview.width }}" height="{{ image.preview.height }}" class="figure-img img-fluid rounded" />
							{% else %}
								NO PREVIEW
							{% endif %}
						</a>
						<figcaption class="figure-caption">
							<label class="col-form-label d-block"><input type="checkbox" name="xfields_{{ image.id }}_del[{{ image.image.id }}]" value="1" /> удалить</label>
						</figcaption>
					</figure>
				</td>
			{% else %}
				<td>
					<input type="text" name="xfields_{{ image.id }}_adscr[]" value="{{ image.description }}" placeholder="Введите описание..." class="form-control mb-2" />
					<input type="file" name="xfields_{{ image.id }}[]" />
				</td>
			{% endif %}
		</tr>
		{% endfor %}
	</tbody>
</table>
