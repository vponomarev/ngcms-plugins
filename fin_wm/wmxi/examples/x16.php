<?php
require_once("_header.php");
# Получение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X16(
		$_POST["wmid"],            # ВМ-идентификатор, которому будет принадлежать вновь созданный кошелек
		$_POST["pursetype"],       # Тип создаваемого кошелька в виде одного латинского символа в верхнем регистре B ,C ,D ,E ,G ,R ,U ,Y ,Z.
		trim($_POST["desc"])       # текстовое название кошелька, которое будет отображаться в интерфейсе Webmoney Keeper Classic или Light.
	);
	# Преобразовываем ответ сервера в структуру. Входные параметры:
	# - XML-ответ сервера
	# - кодировка, используемая на сайте. По умолчанию используется UTF-8
	$structure = $parser->Parse($response, DOC_ENCODING);
	# преобразуем индексы структуры к более удобным для доступа.
	# Не рекомендуется проводить такое преобразование с с результатом, если он содержит
	# множество однотипных строк (например, список транзакций)
	# если надобности в аттрибутах XML-тегов ответа нет, то второй параметр можно
	# установить в false - в таком случае структура выйдет более компактной
	$transformed = $parser->Reindex($structure, true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X16</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
Детальное описание параметров:
<a href="https://wiki.webmoney.ru/wiki/show/%d0%98%d0%bd%d1%82%d0%b5%d1%80%d1%84%d0%b5%d0%b9%d1%81+X16">https://wiki.webmoney.ru/wiki/show/Интерфейс+X16</a>
<br/>

<form action="" method="post">

	<label>WMID кошелька:</label>
	<input type="text" name="wmid" value=""/>
	<br/>

	<label>тип кошелька:</label>
	<input type="text" name="pursetype" value="Z"/>
	<br/>

	<label>название кошелька:</label>
	<input type="text" name="desc" value="wmz"/>
	<br/>

	<input type="submit" value="создать кошелёк"/>
	<br/>

</form>

<!--pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre-->
<pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre>

<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		Код ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		Описание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
