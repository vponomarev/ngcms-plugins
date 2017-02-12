{% if (preview.true) %}
	<div id="postpreview" class="blockpost">
	<h2><span>Предосмотр</span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postright">
				<div class="postmsg">
					<p>{% if (message.true) %}{{ preview.print }}{% else %}Вы не добавили текста{% endif %}</p>
				</div>
			</div>
		</div>
	</div>
	</div>{% endif %}
<div class="blockform">
	{% if (error.true) %}{{ error.print }}{% endif %}
	<h2><span>Начать новую тему</span></h2>
	<div class="box">
		<form name="sendForm" id="sendForm" method="post" action="" enctype="multipart/form-data">
			<div class="inform">
				<fieldset>
					<legend>Напишите ваше сообщение и нажмите отправить</legend>
					<div class="infldset txtarea">
						<label><strong>Заголовок</strong><br/><input class="longinput" type="text" name="subject" value="{{ subject }}" size="80" maxlength="70" tabindex="1"/><br/></label>
						<label>
							<strong>Сообщение</strong><br/>
							<textarea name="message" id="message" rows="20" cols="95" tabindex="2">{{ message.print }}</textarea><br/>
						</label>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend>Прикрепленные файлы</legend>
					<div class="infldset">
						<div class="rbox">
							Прикрепить новый файл, максимум: 3 файлов<br/>
							<input type="file" name="files" size="80"/><br/>
							Примечание: при использовании кнопки "Просмотр" все опции для файлов не сохраняются, вам
							придется снова указать какие файлы надо загрузить / какие удалить
						</div>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend>Свойства</legend>
					<div class="infldset">
						<div class="rbox">
							<label><input type="checkbox" name="subscribe" value="1" tabindex="4"/>Подписаться и следить
								за ответами в этой теме<br/></label>
						</div>
					</div>
				</fieldset>
			</div>
			<p>
				<input type="submit" name="submit" value="Отправить" tabindex="5" accesskey="s"/><input type="submit" name="preview" value="Предпросмотр" tabindex="4" accesskey="s"/>
				<a href="javascript:history.go(-1)">Вернуться назад</a></p>
		</form>
	</div>
	<script>
		var myOpt = {
			buttons: 'bold,italic,underline,strike|,-|,link,myimg,|,smilebox,|,bullist,numlist,|,codephp, |,quotes',
			allButtons: {
				myimg: {
					title: 'Изображение',
					buttonHTML: '<span class="ve-tlb-img"></span>',
					modal: {
						title: 'Вставить изображение',
						width: '600px',
						tabs: [
							{
								input: [
									{
										param: "SRC",
										title: "Введите адрес изображения",
										validation: '^http(s)?://.*?\.(jpg|png|gif|jpeg)$'
									},
									{param: "TITLE", title: "Введите заголовок изображения"}
								]
							}
						],
						onLoad: function () {
						},
						onSubmit: function () {
						}
					}, transform: {
						'<img src="{SRC}" title="{TITLE}" />': '[img title={TITLE}]{SRC}[/img]'
					}
				},
				codephp: {
					title: CURLANG.code,
					buttonText: "[code]",
					transform: {
						'<div class="codewrap"><div class="codetop">Код: PHP</div><div class="codemain">{SELTEXT}</div></div>': "[code=PHP]{SELTEXT}[/code]"
					}
				}, quotes: {
					title: CURLANG.quote,
					buttonHTML: '<span class="ve-tlb-quote"></span>',
					transform: {
						'<div class="quote">{SELTEXT}</div>': '[quote]{SELTEXT}[/quote]',
						'<div class="quote"><cite>{AUTHOR} написал:</cite>{SELTEXT}</div>': '[quote={AUTHOR}]{SELTEXT}[/quote]'
					}
				}
			},
			smileList: [
				{title: CURLANG.sm1, img: '<img src="{themePrefix}{themeName}/smiles/1.gif" class="sm">', bbcode: ":)"},
				{title: CURLANG.sm8, img: '<img src="{themePrefix}{themeName}/smiles/2.gif" class="sm">', bbcode: ":("},
				{title: CURLANG.sm1, img: '<img src="{themePrefix}{themeName}/smiles/3.gif" class="sm">', bbcode: ":D"},
				{
					title: CURLANG.sm3,
					img: '<img src="{themePrefix}{themeName}/smiles/11.gif" class="sm">',
					bbcode: ";)"
				},
				{
					title: CURLANG.sm4,
					img: '<img src="{themePrefix}{themeName}/smiles/4.gif" class="sm">',
					bbcode: ":up:"
				},
				{
					title: CURLANG.sm5,
					img: '<img src="{themePrefix}{themeName}/smiles/9.gif" class="sm">',
					bbcode: ":down:"
				},
				{
					title: CURLANG.sm6,
					img: '<img src="{themePrefix}{themeName}/smiles/6.gif" class="sm">',
					bbcode: ":shock:"
				},
				{
					title: CURLANG.sm7,
					img: '<img src="{themePrefix}{themeName}/smiles/9.gif" class="sm">',
					bbcode: ":angry:"
				},
				{
					title: CURLANG.sm9,
					img: '<img src="{themePrefix}{themeName}/smiles/14.gif" class="sm">',
					bbcode: ":sick:"
				}
			]
		};
		$('#message').wysibb(myOpt);
	</script>
</div> 