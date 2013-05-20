{% if (preview.true) %}
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<h2><span>����������</span></h2>
	<p>{% if (content) %}{{ preview.print }}{% else %}�� �� �������� ������{% endif %}</p>
</table>
{% endif %}
{{ error }}
<link rel="stylesheet" href="{{ forum_tpl }}/wbbtheme.css" type="text/css" />
<script src="{{ forum_tpl }}/jquery.wysibb.min.js"></script>
<form method="post" action="" name="form" enctype="multipart/form-data">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="50%" class="contentEntry1">�������� �������<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="40" name="title" value="{{ title }}"  /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� �������<br /><small></small></td>
			<td width="50%" class="contentEntry2"><textarea type="text" name="content" id="content" cols="100" rows="10">{{ content }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">��������� ������������� � �������?<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="checkbox" name="mail" value="1" {{ checked }} /></td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" name="submit" value="��������" class="button" /> <input type="submit" name="preview" value="������������" class="button" />
			</td>
		</tr>
	</table>
</form>
<script>
	var myOpt = {
		buttons: 'bold,italic,underline,strike|,-|,link,myimg,|,smilebox,|,bullist,numlist,quotes, |,codephp, ,|,quote_name', 
		allButtons: {
			myimg: {
				title: '�����������',
				buttonHTML: '<span class="ve-tlb-img"></span>',
				modal:{
					title: '�������� �����������',
					width: '600px',
					tabs: [{
								input: [
									{param: "SRC",title:"������� ����� �����������",validation: '^http(s)?://.*?\.(jpg|png|gif|jpeg)$'},
									{param: "TITLE",title:"������� ��������� �����������"}
								]
						}],
						onLoad: function(){},
						onSubmit: function(){}
				}, transform:{
					'<img src="{SRC}" title="{TITLE}" />':'[img title={TITLE}]{SRC}[/img]'
				}
			},
			codephp: {
				title: CURLANG.code,
				buttonText:"[code]",
				transform:{ 
					'<div class="codewrap"><div class="codetop">���: PHP</div><div class="codemain">{SELTEXT}</div></div>':"[code=PHP]{SELTEXT}[/code]"
				}
			},quotes:{
				title:CURLANG.quote,
				buttonHTML:'<span class="ve-tlb-quote"></span>',
				transform:{
					'<div class="quote">{SELTEXT}</div>':'[quote]{SELTEXT}[/quote]',
					'<div class="quote"><cite>{AUTHOR} �������:</cite>{SELTEXT}</div>':'[quote={AUTHOR}]{SELTEXT}[/quote]'
				}
			}
		},
		smileList: [
			{title:CURLANG.sm1, img: '<img src="{themePrefix}{themeName}/smiles/1.gif" class="sm">', bbcode:":)"},
			{title:CURLANG.sm8 ,img: '<img src="{themePrefix}{themeName}/smiles/2.gif" class="sm">', bbcode:":("},
			{title:CURLANG.sm1 ,img: '<img src="{themePrefix}{themeName}/smiles/3.gif" class="sm">', bbcode:":D"},
			{title:CURLANG.sm3 ,img: '<img src="{themePrefix}{themeName}/smiles/11.gif" class="sm">', bbcode:";)"},
			{title:CURLANG.sm4, img: '<img src="{themePrefix}{themeName}/smiles/4.gif" class="sm">', bbcode:":up:"},
			{title:CURLANG.sm5, img: '<img src="{themePrefix}{themeName}/smiles/9.gif" class="sm">', bbcode:":down:"},
			{title:CURLANG.sm6, img: '<img src="{themePrefix}{themeName}/smiles/6.gif" class="sm">', bbcode:":shock:"},
			{title:CURLANG.sm7, img: '<img src="{themePrefix}{themeName}/smiles/9.gif" class="sm">', bbcode:":angry:"},
			{title:CURLANG.sm9, img: '<img src="{themePrefix}{themeName}/smiles/14.gif" class="sm">', bbcode:":sick:"}
		]
	};
	$('#content').wysibb(myOpt);
</script>
