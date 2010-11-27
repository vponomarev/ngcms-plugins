<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('maintenance', 'cron_run');

//
// ���������� ����������
//
$CRONFILE_LOADED = 0;
$CRONFILE = '';
$CRONDATA = array();

//
// ��������� CRON �����
//
function cron_load() {
 global $CRONFILE_LOADED, $CRONFILE, $CRONDATA;

 if ($CRONFILE_LOADED) { return $CRONDATA; }

 $cronFile = get_plugcfg_dir('cron').'/crontab';

 $cronList = array();

 // ���� ����-������ �� ���������� - �������� ��� �������
 if (!file_exists($cronFile)) {
  $fl = @fopen($cronFile,'w+');
  @fclose($fl);
 }

 // �������� ����� �� ������� ���� �����
 if (!is_readable($cronFile)) { return array(); }

 $data = explode("\n",file_get_contents($cronFile));
 $CRONFILE = $data;
 foreach ($data as $line) {
 	$line = trim($line);
 	//print "CRON: $line\n";
 	if (preg_match("/^[ \t]*#/",$line)) { continue; }
 	if (preg_match("/^(.+?)[ \t]+(.+?)[ \t]+(.+?)[ \t]+(.+?)[ \t]+(.+?)[ \t]+(.+?)[ \t]+(.+?)[ \t]*$/",$line,$res)) {
 	 // ���������� ������, ��������� ������������
 	 for ($i = 1; $i <= 5; $i++) {
 	 	if (!preg_match("/^(\*|\d+)$/",$res[$i])) {
 	 		print "Wrong value ($i): ".$res[$i]."\n";
 	 		return 0;
 	 	}
 	 }	
 	 array_push($cronList, $res);
 	}
 }	
 $CRONFILE_LOADED = 1;
 $CRONDATA = $cronList;
 return $cronList;
}

//
// ��������� CRON ����
//
function cron_save() {
 global $CRONFILE_LOADED, $CRONFILE, $CRONDATA;

 // ������ ������ ���� ���� ��� ��������
 if (!$CRONFILE_LOADED) { return; }
 $cronDir = get_plugcfg_dir('cron');
 if (!is_dir($cronDir) && !mkdir($cronDir)) {
 	print "Can't create config directory for plugin 'cron'<br />\n";
 	return;
 }	

 if (!($fp = fopen($cronDir.'/crontab', 'w'))) {
 	print "Can't open crontab file for writing<br />\n";
 	return;
 }

 foreach ($CRONDATA as $k => $v) {
 	fwrite($fp, $v[0]."\n");
 }
 fclose($fp);	
}


//
// ���������������� ����� ������������� ������
//
function cron_register_task($plugin, $pluginCMD, $min, $hour, $day, $month, $DOW) {
 global $CRONFILE_LOADED, $CRONFILE, $CRONDATA;
 // �������� � ������ ������������� ������
 if ((!$CRONFILE_LOADED)&&(!is_array(cron_load()))) { return 0; }

 // ��������� ���������
 if ((!preg_match('/^(\*|\d+)$/',$min)) || (!preg_match('/^(\*|\d+)$/',$hour)) ||
     (!preg_match('/^(\*|\d+)$/',$day)) || (!preg_match('/^(\*|\d+)$/',$month)) ||
     (!preg_match('/^(\*|\d+)$/',$DOW)) || (!$plugin)) {
        // �������� �������� ����������
	return 0;
 }	

 // ��������� ���������
 array_push($CRONDATA, array ("$min $hour $day $month $DOW $plugin $pluginCMD",$min,$hour,$day,$month,$DOW,$plugin,$pluginCMD)); 
 cron_save();
}


//
// ������� ����� ���������� ������
//
function cron_unregister_task($plugin, $pluginCMD='', $min='', $hour='', $day='', $month='', $DOW='') {
 global $CRONFILE_LOADED, $CRONFILE, $CRONDATA;

 // �������� � ������ ������������� ������
 if ((!$CRONFILE_LOADED)&&(!is_array(cron_load()))) { return 0; }

 $ok = 0;
 foreach ($CRONDATA as $k => $v) {
  if (((!$min) && ($v[6] == $plugin) && ((!$pluginCMD) || ($v[7] == $pluginCMD))) ||
      (($v[1] == $min) && ($v[2] == $hour) && ($v[3] == $day) && ($v[4] == $month) &&
       ($v[5] == $DOW) && ($v[6] == $plugin) && ($v[7] == $pluginCMD))) {
      array_splice($CRONDATA, $k, 1);
      $ok=1;
  }
 }
 if ($ok) {
      cron_save();
      return 1;
 }
 return 0;
}


//
// ������ ������������ CRON � ������ �������������
//
function cron_run() {

 $cacheDir  = get_plugcache_dir('cron');

 $timeout   = 120;  // 120 ������ (2 ������) �� �������
 $period    = 300;  // 5 ����� ����� ���������

 if (!is_dir($cacheDir) && !mkdir($cacheDir)) {
 	print "Can't create temp directory for plugin 'cron'<br />\n";
 	return;
 }	

 // ���������� ����� ���������� ��������� ���������� �������
 $fn_ok 	= 0;
 $fn_progress	= 0;

 if (!($dir = opendir($cacheDir))) { return -1; }
 while (false !== ($file = readdir($dir))) {
  if (false !== ($fsize = filesize($cacheDir.'/'.$file))) {
   if ($fsize && (intval($file) > $fn_ok )) { $fn_ok = intval($file); }
    else if (intval($file) > $fn_progress) { $fn_progress = intval($file); }
  }
 }
 closedir($dir);

 // ������� ���� �� ������ period ������� ��� ���� ���������� (�� �� ��������) ������.
 if (!(($fn_ok+$period < time()) && ($fn_progress+$timeout < time()))) {
  return 0;
 }

 // ������ ��������� ���� ��� �����
 if (false === ($temp = tempnam($cacheDir,'tmp_'))) {
  // �� ������ ������� ���� (???)
  return -1;
 }

 // ������ ����
 $myFlagFile = time();

 // �������. ������� rename
 if (!rename($temp,$cacheDir.'/'.$myFlagFile)) {
  // �� ������ ���������������, ���-�� ����� �� ���. ������� ��������� ���� � �������.
  unlink($temp);
  return 0;
 }

 // ���������, �� ����� �� ��� ����� ��� ������� ���� ����?
 $fn_max = 0;
 if (!($dir = opendir($cacheDir))) { return -1; }
 while (false !== ($file = readdir($dir))) {
  if (intval($file)>$fn_max) { $fn_max = $file; }
 }
 closedir($dir);

 if ($fn_max > $myFlagFile) {
  // ��� ��-���� ������. ��������� ������
  unlink($cacheDir.'/'.$myFlagFile);
  return 0;
 }


 //===========================================================================================
 // ����, �� ������� ���� ����! ������ ��� ��� ����-����� �� ������ � ������ timeout �������
 //===========================================================================================

 // ������� - ��������� CRON ����

 if (!is_array($cronList = cron_load())) {
 	if ($cronList == -1) { 
 		// ��� CRON �����
 		//print "Can't open CRON file<br />\n"; 
 	} else {
 		// ������ � CRON �����
 		print "Wrong data in CRON file\n"; 
 	}
 	return -1;
 }	
 
 if (sizeof($cronList) == 0) { return 0; }

 // ������ ��������� ������ ����� ������� ���������� ��������� � ������� ���������� ��������� �������
 $runList = array();
 
 //print ">> Last run: ".date("Y-m-d H:i:s", $fn_ok)."<br />\n";
 foreach ($cronList as $cronLine) {
  // ��� ������ ������ ����� ������� ����� ������ ��������� ��������� �����.
  // ���� ������ �� �������� ������� - ���������
  $at = localtime($fn_ok, 1);
  list ($xxx, $min, $hour, $day, $month, $dow, $plugin, $plug_cmd) = $cronLine;

  // ��������� ������ ��� ��������� ����������� ������� �������� � ������� ��� "*"
  $at['tm_min']++;
  // ������
  if ($min != '*')   { if ($min<=$at['tm_min']  )    { $at['tm_hour']++; } $at['tm_min'] = $min;     }
  // ����           
  if ($hour != '*')  { if ($hour<$at['tm_hour'])     { $at['tm_mday']++; } $at['tm_hour'] = $hour;   }
  // ����
  if ($day != '*')   { if ($day<$at['tm_mday'])      { $at['tm_mon']++;  } $at['tm_mday'] = $hour;   }
  // �����
  if ($month != '*') { if ($month<($at['tm_mon']+1)) { $at['tm_year']++; } $at['tm_mon'] = $month-1; }

  //var_dump($at);
  $newtime = mktime($at['tm_hour'], $at['tm_min'], 0, $at['tm_mon']+1, $at['tm_mday'], $at['tm_year']);
  //print " [$plugin][$plug_cmd] future run: ".date("Y-m-d H:i:s", $newtime)."<br />\n";

  if ($newtime < $myFlagFile) {
  	// ������ �������� � ������������� ��������
  	$runList[$plugin.'_'.$plug_cmd] = array($plugin, $plug_cmd);
        //array_push($runList, array($plugin, $plug_cmd));
  }	
 }

 // ��������� ���� �� ��� ��� �������
 if (sizeof($runList)) {
  // ���������� �����������
  load_extras('cron');

  // ��������� �����������
  $trace = '';
  foreach ($runList as $num => $run) {
  	//print "Run [".$run[0]."] // ".$run[1]."\n";
  	$trace .= "Exec [".$run[0]."] // ".$run[1]."\n";
  	exec_acts('cron_'.$run[0], $run[1]);
  }	
 }
 
 // ====================================
 // ��� ������ ���������
 // ====================================
 
 // �������� ���� ��� ��������
 if (false !== ($f = fopen($cacheDir.'/'.$myFlagFile,'w'))) {
  fwrite($f, $trace);
  fwrite($f,'OK');
  fclose($f);
 } else {
  return -1;
 }
 
 // ====================================
 // ��������� CleanUP ������ ������
 // ====================================

 if (!($dir = opendir($cacheDir))) { return -1; }
 while (false !== ($file = readdir($dir))) {
  if ((substr($file,0,1)!='.')&&(intval($file) == $file) && (intval($file)<$myFlagFile)) { unlink($cacheDir.'/'.$file); }
 }
 closedir($dir);


 // �������� ����������
 return 1;
}

