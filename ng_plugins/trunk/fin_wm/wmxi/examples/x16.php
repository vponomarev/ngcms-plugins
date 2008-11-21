<?php
	require_once("_header.php");

	# ��������� � ��������� ������ �����
	if (count($_POST) > 0) {

		$response = $wmxi->X16(
			$_POST["wmid"],            # ��-�������������, �������� ����� ������������ ����� ��������� �������
			$_POST["pursetype"],       # ��� ������������ �������� � ���� ������ ���������� ������� � ������� �������� B ,C ,D ,E ,G ,R ,U ,Y ,Z.
			trim($_POST["desc"])       # ��������� �������� ��������, ������� ����� ������������ � ���������� Webmoney Keeper Classic ��� Light.
		);

		# ��������������� ����� ������� � ���������. ������� ���������:
		# - XML-����� �������
		# - ���������, ������������ �� �����. �� ��������� ������������ UTF-8
		$structure = $parser->Parse($response, DOC_ENCODING);

		# ����������� ������� ��������� � ����� ������� ��� �������.
		# �� ������������� ��������� ����� �������������� � � �����������, ���� �� ��������
		# ��������� ���������� ����� (��������, ������ ����������)
		# ���� ���������� � ���������� XML-����� ������ ���, �� ������ �������� �����
		# ���������� � false - � ����� ������ ��������� ������ ����� ����������
		$transformed = $parser->Reindex($structure, true);

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X16</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	��������� �������� ����������:
	<a href="https://wiki.webmoney.ru/wiki/show/%d0%98%d0%bd%d1%82%d0%b5%d1%80%d1%84%d0%b5%d0%b9%d1%81+X16">https://wiki.webmoney.ru/wiki/show/���������+X16</a>
	<br />

	<form action="" method="post">

		<label>WMID ��������:</label>
		<input type="text" name="wmid" value="" />
		<br/>

		<label>��� ��������:</label>
		<input type="text" name="pursetype" value="Z" />
		<br/>

		<label>�������� ��������:</label>
		<input type="text" name="desc" value="wmz" />
		<br/>

		<input type="submit" value="������� ������" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre>

	<pre><!-- ������ � ���������� �������� ������������� ������� ����� ��������� ������ � ������� -->
		��� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		�������� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
