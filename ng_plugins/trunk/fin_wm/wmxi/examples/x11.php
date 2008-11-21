<?php
	require_once("_header.php");

	# ��������� � ��������� ������ �����
	if (count($_POST) > 0) {

		$response = $wmxi->X11(
			trim($_POST["passportwmid"]),     # 12 ����
			intval($_POST["dict"]),           # 0/1
			intval($_POST["info"]),           # 0/1
			intval($_POST["mode"])            # 0/1
		);

		# �������� ��������� ��� �������, ��� ��� � ���� ���������� ��������� ������������ � ��������
		# ��������! ��� ���������� ������ ��� ���������� X11
		$parser->parser_encoding = "windows-1251";

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
	<title>X11</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	��������� �������� ����������:
	<a href="http://passport.webmoney.ru/asp/infoXMLGetWMPassport.asp">http://passport.webmoney.ru/asp/infoXMLGetWMPassport.asp</a>
	<br />

	<form action="" method="post">

		<label>WM - ������������� ��������� :</label>
		<input type="text" name="passportwmid" value="" />
		<br/>

		<label>����������� "�������� �������":</label>
		<input type="text" name="dict" value="0" />
		<br/>

		<label>����������� ������������ ������(���������� ������ + ���������� ����������) ��������� ���������:</label>
		<input type="text" name="info" value="1" />
		<br/>

		<label>�������� �������������� WM ��������������, ������������ ������, ������ ���������� ��������������� ��� ������������ ���������:</label>
		<input type="text" name="mode" value="0" />
		<br/>

		<input type="submit" value="���������" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<? $item = @$structure["0"]["node"]["1"]["node"]; ?>
	<pre><!-- ������ � ���������� �������� ������������� ������� ����� ��������� ������ � ������� -->
		�����������: <b><?=htmlspecialchars(@$item["0"]["node"]["0"]["@"]["REGNICKNAME"], ENT_QUOTES);?></b>
		���: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["NICKNAME"], ENT_QUOTES);?></b>
		����: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["INFOOPEN"], ENT_QUOTES);?></b>
		�����: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["CITY"], ENT_QUOTES);?></b>
		������: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["COUNTRY"], ENT_QUOTES);?></b>

		��� ������: <b><?=htmlspecialchars(@$structure["0"]["@"]["RETVAL"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
