<tbody>
	<tr class="thead-dark">
		<th scope="col">
			Теги новости
		</th>
	</tr>
	<tr>
		<td>Список тегов:<br />
			<small>указывается через запятую</small>
		</td>
		<td><input id="pTags" name="tags" value="{tags}" autocomplete="off" />
			<span id="suggestLoader" style="width: 20px; visibility: hidden;"><img
					src="{skins_url}/images/loading.gif" /></span>
		</td>
	</tr>
</tbody>
<script language="javascript" type="text/javascript">
	// INIT NEW SUGGEST LIBRARY [ call only after full document load ]
	var aSuggest = new ngSuggest('pTags',
		{
			'localPrefix': '{localPrefix}',
			'reqMethodName': 'plugin.tags.suggest',
			'lId': 'suggestLoader',
			'hlr': 'true',
			'iMinLen': 1,
			'stCols': 2,
			'stColsClass': ['cleft', 'cright'],
			'stColsHLR': [true, false],
			'listDelimiter': ',',
		}
	);

</script>
