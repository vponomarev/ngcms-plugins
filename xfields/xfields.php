<?php

// #==========================================================#
// # Plugin name: xfields [ Additional fields managment ]     #
// # Author: Vitaly A Ponomarev, vp7@mail.ru                  #
// # Allowed to use only with: Next Generation CMS            #
// #==========================================================#

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load lang files
LoadPluginLang('xfields', 'config');

//
// XFields: Add/Modify attached files
function xf_modifyAttachedImages($dsID, $newsID, $xf, $attachList) {
	global $mysql, $config, $DSlist;
	//print "<pre>".var_export($_REQUEST, true)."</pre>";
	// Init file/image processing libraries
	$fmanager = new file_managment();
	$imanager = new image_managment();

	// Select xf group name
	$xfGroupName = '';
	foreach (array('news', 'users') as $k) {
		if ($DSlist[$k] == $dsID) {
			$xfGroupName = $k;
			break;
		}
	}

	if (!$xfGroupName) {
		return false;
	}

	// Scan if user want to change description
	foreach ($attachList as $iRec) {
		//print "[A:".$iRec['id']."]";
		if (isset($_REQUEST['xfields_'.$iRec['pidentity'].'_dscr']) && is_array($_REQUEST['xfields_'.$iRec['pidentity'].'_dscr']) && isset($_REQUEST['xfields_'.$iRec['pidentity'].'_dscr'][$iRec['id']])) {
			// We have this field in EDIT mode
			if ($_REQUEST['xfields_'.$iRec['pidentity'].'_dscr'][$iRec['id']] != $iRec['decsription']) {
				$mysql->query("update ".prefix."_images set description = ".db_squote($_REQUEST['xfields_'.$iRec['pidentity'].'_dscr'][$iRec['id']])." where id = ".intval($iRec['id']));
			}
		}
	}


	$xdata = array();
	foreach ($xf[$xfGroupName] as $id => $data) {
		// Attached images are processed in special way
		if ($data['type'] == 'images') {
			// Check if we should delete some images
			if (isset($_POST['xfields_'.$id.'_del']) && is_array($_POST['xfields_'.$id.'_del'])) {
				foreach ($_POST['xfields_'.$id.'_del'] as $key => $value) {
					// Allow to delete only images, that are attached to current news
					if ($value) {
						$xf = false;
						foreach ($attachList as $irow) {
							if ($irow['id'] == $key) {
								$xf = true; break;
							}
						}
						if (!$xf)
							continue;

						//print "NEED TO DEL [$key]<br/>\n";
						$fmanager->file_delete(array('type' => 'image', 'id' => $key));
					}
				}
			}
			// Check for new attached files
			if (isset($_FILES['xfields_'.$id]) && isset($_FILES['xfields_'.$id]['name']) && is_array($_FILES['xfields_'.$id]['name'])) {
				foreach ($_FILES['xfields_'.$id]['name'] as $iId => $iName) {
					if ($_FILES['xfields_'.$id]['error'][$iId] > 0) {
						//print $iId." >>ERROR: ".$_FILES['xfields_'.$id]['error'][$iId]."<br/>\n";
						continue;
					}
					if ($_FILES['xfields_'.$id]['size'][$iId] == 0) {
						//print $iId." >>EMPTY IMAGE<br/>\n";
						continue;
					}

					// Check if we try to overcome limits
					$currCount = $mysql->record("select count(*) as cnt from ".prefix."_images where (linked_ds = ".intval($dsID).") and (linked_id = ".intval($newsID).") and (plugin = 'xfields') and (pidentity=".db_squote($id).")");
					if ($currCount['cnt'] >= $data['maxCount'])
						continue;

					// Upload file
					$up = $fmanager->file_upload(
						array(
							'dsn' => true,
							'linked_ds'		=> $dsID,
							'linked_id'		=> $newsID,
							'type'			=> 'image',
							'http_var'		=> 'xfields_'.$id,
							'http_varnum'	=> $iId,
							'plugin'		=> 'xfields',
							'pidentity'		=> $id,
							'description'	=> (isset($_REQUEST['xfields_'.$id.'_adscr']) && is_array($_REQUEST['xfields_'.$id.'_adscr']) && isset($_REQUEST['xfields_'.$id.'_adscr'][$iId]))?($_REQUEST['xfields_'.$id.'_adscr'][$iId]):'',
						)
					);

					// Process upload error
					if (!is_array($up)) {
						continue;
					}
					//print "<pre>CREATED: ".var_export($up, true)."</pre>";
					// Check if we need to create preview
					$mkThumb  = $data['imgThumb'];
					$mkStamp  = $data['imgStamp'];
					$mkShadow = $data['imgShadow'];

					$stampFileName = '';
					if (file_exists(root.'trash/'.$config['wm_image'].'.gif')) {
						$stampFileName = root.'trash/'.$config['wm_image'].'.gif';
					} else if (file_exists(root.'trash/'.$config['wm_image'])) {
						$stampFileName = root.'trash/'.$config['wm_image'];
					}

					if ($mkThumb) {
						// Calculate sizes
						$tsx = $data['thumbWidth'];
						$tsy = $data['thumbHeight'];

						if ($tsx < 10) {	$tsx = 150;		}
						if ($tsy < 10) {	$tsy = 150;		}

						$thumb = $imanager->create_thumb($config['attach_dir'].$up[2], $up[1], $tsx,$tsy, $config['thumb_quality']);
						//print "<pre>THUMB: ".var_export($thumb, true)."</pre>";
						if ($thumb) {
							//print "THUMB_OK<br/>";
							// If we created thumb - check if we need to transform it
							$stampThumb  = ($data['thumbStamp']  && ($stampFileName != ''))?1:0;
							$shadowThumb = $data['thumbShadow'];
							if ($shadowThumb || $stampThumb) {
								$stamp = $imanager->image_transform(
									array(
										'image' => $config['attach_dir'].$up[2].'/thumb/'.$up[1],
										'stamp' => $stampThumb,
										'stamp_transparency' => $config['wm_image_transition'],
										'stamp_noerror' => true,
										'shadow' => $shadowThumb,
										'stampfile' => $stampFileName
									)
								);
								//print "THUMB [STAMP/SHADOW = (".$stamp.")]<br/>";
							}
						}
					}

					if ($mkStamp || $mkShadow) {
						$stamp = $imanager->image_transform(
						array(
							'image' => $config['attach_dir'].$up[2].'/'.$up[1],
							'stamp' => $mkStamp,
							'stamp_transparency' => $config['wm_image_transition'],
							'stamp_noerror' => true,
							'shadow' => $mkThumb,
							'stampfile' => $stampFileName
						)
						);
						//print "IMG [STAMP/SHADOW = (".var_export($stamp, true).")]<br/>";

					}

					// Now write info about image into DB
					if (is_array($sz = $imanager->get_size($config['attach_dir'].$up[2].'/'.$up[1]))) {
						$fmanager->get_limits($type);


						// Gather filesize for thumbinals
						$thumb_size_x = 0;
						$thumb_size_y = 0;
						if (is_array($thumb) && is_readable($config['attach_dir'].$up[2].'/thumb/'.$up[1]) && is_array($szt = $imanager->get_size($config['attach_dir'].$up[2].'/thumb/'.$up[1]))) {
							$thumb_size_x = $szt[1];
							$thumb_size_y = $szt[2];
						}
						$mysql->query("update ".prefix."_".$fmanager->tname." set width=".db_squote($sz[1]).", height=".db_squote($sz[2]).", preview=".db_squote(is_array($thumb)?1:0).", p_width=".db_squote($thumb_size_x).", p_height=".db_squote($thumb_size_y).", stamp=".db_squote(is_array($stamp)?1:0)." where id = ".db_squote($up[0]));
					}

				}
			}
		}
	}
}


// Perform replacements while showing news
class XFieldsNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars) {
		global $lang, $twig, $catz;

		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return false;

		$output = '';
		$xfEntries = array();

		if (is_array($xf['news']))
			foreach ($xf['news'] as $id => $data) {
				if ($data['disabled'])
					continue;

				$xfEntry = array(
					'title'		=>	$data['title'],
					'id'		=>	$id,
					'required'	=>	$lang['xfields_fld_'.($data['required']?'required':'optional')],
					'flags'		=>	array(
						'required'	=>	$data['required']?true:false,
					),
				);


				switch ($data['type']) {
					case 'text'  : 	$val = '<input type="text" id="form_xfields_'.$id.'" name="xfields['.$id.']" title="'.$data['title'].'" value="'.secure_html($data['default']).'"/>';
									$xfEntry['input'] = $val;
									$xfEntries[intval($data['area'])][] = $xfEntry;
									break;

					case 'select': 	$val = '<select name="xfields['.$id.']" id="form_xfields_'.$id.'" >';
									if (!$data['required']) $val .= '<option value=""></option>';
									if (is_array($data['options']))
										foreach ($data['options'] as $k => $v)
											$val .= '<option value="'.secure_html(($data['storekeys'])?$k:$v).'"'.((($data['storekeys'] && $data['default'] == $k)||(!$data['storekeys'] && $data['default'] == $v))?' selected':'').'>'.$v.'</option>';
									$val .= '</select>';
									$xfEntry['input'] = $val;
									$xfEntries[intval($data['area'])][] = $xfEntry;
									break;
					case 'textarea'  :	$val = '<textarea cols="30" rows="5" name="xfields['.$id.']" id="form_xfields_'.$id.'" >'.$data['default'].'</textarea>';
									$xfEntry['input'] = $val;
									$xfEntries[intval($data['area'])][] = $xfEntry;
									break;
					case 'images'	:
						$iCount = 0;
						$input = '';
						$tVars = array( 'images' => array());

						// Show entries for allowed number of attaches
						for ($i = $iCount+1; $i <= intval($data['maxCount']); $i++) {
							$tImage = array(
								'number'	=>	$i,
								'id'		=>	$id,
								'flags'		=> array(
									'exist'		=> false,
								),
							);
							$tVars['images'][] = $tImage;
						}

						// Make template
						$xt = $twig->loadTemplate('plugins/xfields/tpl/ed_entry.image.tpl');
						$val = $xt->render($tVars);
						$xfEntry['input'] = $val;
						$xfEntries[intval($data['area'])][] = $xfEntry;
						break;
				}
			}

		$xfCategories = array();
		foreach ($catz as $cId => $cData) {
			$xfCategories[$cData['id']] = $cData['xf_group'];
		}

		// Prepare table data [if needed]
		$flagTData = false;
		if (isset($xf['tdata']) && is_array($xf['tdata'])) {
			// Data are not provisioned
			$tlist = array();

			// Prepare config
			$tclist = array();
			$thlist = array();
			foreach ($xf['tdata'] as $fId => $fData) {
				if ($fData['disabled'])
					continue;

				$flagTData = true;

				$tclist[$fId] = array(
					'title'		=> $fData['title'],
					'required'	=> $fData['required'],
					'type'		=> $fData['type'],
					'default'	=> $fData['default'],
				);
				$thlist [] = array(
					'id'	=> $fId,
					'title'	=> $fData['title'],
				);
				if ($fData['type'] == 'select') {
					$tclist[$fId]['storekeys']	= $fData['storekeys'];
					$tclist[$fId]['options']	= $fData['options'];
				}

			}
		}


		$tVars = array(
		//	'entries'	=>	$xfEntries,
			'xfGC'		=>	json_encode(arrayCharsetConvert(0, $xf['grp.news'])),
			'xfCat'		=>	json_encode(arrayCharsetConvert(0, $xfCategories)),
			'xfList'	=>	json_encode(arrayCharsetConvert(0, array_keys($xf['news']))),
			'xtableConf'	=>	json_encode(arrayCharsetConvert(0, $tclist)),
			'xtableVal'		=>	isset($_POST['xftable'])?$_POST['xftable']:json_encode(arrayCharsetConvert(0, $tlist)),
			'xtableHdr'		=>	$thlist,
			'xtablecnt'		=>	count($thlist),
			'flags'			=> array(
				'tdata'			=> $flagTData,
			),
		);

		if (!isset($xfEntries[0])) {
			$xfEntries[0] = array();
		}

		foreach ($xfEntries as $k => $v) {
			// Check if we have template for specific area, elsewhere - use basic [0] template
			$templateName = 'plugins/xfields/tpl/news.add.'.(file_exists(root.'plugins/xfields/tpl/news.add.'.$k.'.tpl')?$k:'0').'.tpl';

			$xt = $twig->loadTemplate($templateName);
			$tVars['entries']		= $v;
			$tVars['entryCount']	= count($v);
			$tVars['area']			= $k;

			// Table data is available only for area 0
			$tVars['flags']['tdata']	= (!$k)?$flagTData:0;

			// Render block
			$tvars['plugin']['xfields'][$k] .= $xt->render($tVars);
		}

		unset($tVars['entries']);
		unset($tVars['area']);

		// Render general part [with JavaScript]
		$xt = $twig->loadTemplate('plugins/xfields/tpl/news.general.tpl');
		$tvars['plugin']['xfields']['general'] = $xt->render($tVars);

		return 1;
	}
	function addNews(&$tvars, &$SQL) {
		global $lang, $twig, $twigLoader;
		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return 1;

		$rcall = $_REQUEST['xfields'];
		if (!is_array($rcall)) $rcall = array();

		$xdata = array();
		foreach ($xf['news'] as $id => $data) {
			if ($data['disabled'])
				continue;

			if ($data['type'] == 'images') { continue; }
			// Fill xfields. Check that all required fields are filled
			if ($rcall[$id] != '') {
				$xdata[$id] = $rcall[$id];
			} else if ($data['required']) {
				msg(array("type" => "error", "text" => str_replace('{field}', $id, $lang['xfields_msge_emptyrequired'])));
				return 0;
			}
			// Check if we should save data into separate SQL field
			if ($data['storage'] && ($rcall[$id] != ''))
				$SQL['xfields_'.$id] = $rcall[$id];
		}

	    $SQL['xfields']   = xf_encode($xdata);
		return 1;
	}
	function addNewsNotify(&$tvars, $SQL, $newsID) {
		global $mysql;

		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return 1;

		xf_modifyAttachedImages(1, $newsID, $xf, array());

		// Scan fields and check if we have attached images for fields with type 'images'
		$haveImages = false;
		foreach ($xf['news'] as $fid => $fval) {
			if ($fval['type'] == 'images') {
				$haveImages = true;
				break;
			}
		}

		if ($haveImages) {
			// Get real ID's of attached images and print here
			$idlist = array();

			foreach ($mysql->select("select id, plugin, pidentity from ".prefix."_images where (linked_ds = 1) and (linked_id = ".db_squote($newsID).")") as $irec) {
				if ($irec['plugin'] == 'xfields') {
					$idlist[$irec['pidentity']] []= $irec['id'];
				}
			}

			// Decode xfields
			$xdata = xf_decode($SQL['xfields']);
			//print "<pre>IDLIST: ".var_export($idlist, tru)."</pre>";
			// Scan for fields that should be configured to have attached images
			foreach ($xf['news'] as $fid => $fval) {
				if (($fval['type'] == 'images')&&(isset($idlist[$fid]))) {
					$xdata[$fid] = join(",", $idlist[$fid]);
				}
			}
			$mysql->query("update ".prefix."_news set xfields = ".db_squote(xf_encode($xdata))." where id = ".db_squote($newsID));
		}

		// Prepare table data [if needed]
		if (isset($xf['tdata']) && is_array($xf['tdata']) && isset($_POST['xftable']) && is_array($xft = json_decode(iconv('Windows-1251', 'UTF-8', $_POST['xftable']), true))) {
			$xft = arrayCharsetConvert(1, $xft);
			//print "<pre>[".(is_array($xft)?'ARR':'NOARR')."]INCOMING ARRAY: ".var_export($xft, true)."</pre>";
			$recList = array();
			$queryList = array();
			// SCAN records
			foreach ($xft as $k => $v) {
				if (is_array($v) && isset($v['#id'])) {
					$editMode = 0;

					$tRec = array('xfields' => array());
					foreach ($xf['tdata'] as $fId => $fData) {
						if ($fData['storage']) {
							$tRec['xfields_'.$fId] = db_squote($v[$fId]);
						}
						$tRec['xfields'][$fId] = $v[$fId];
					}

					$tRec['xfields'] = db_squote(serialize($tRec['xfields']));

					// Now update record info
					$query = "insert into ".prefix."_xfields (".join(", ", array_keys($tRec)).", linked_ds, linked_id) values (".join(", ", array_values($tRec)).", 1, ".(intval($newsID)).")";
					//print "SQL: $query <br/>\n";
					$queryList []= $query;
					//$mysql->query($query);

					//print "GOT LINE:<pre>".var_export($tRec, true)."</pre>";
				}
			}

			// Execute queries
			foreach ($queryList as $query) {
				$mysql->query($query);
			}
		}

		return 1;
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {
		global $lang, $catz, $mysql, $config, $twig, $twigLoader;
		//print "<pre>".var_export($lang, true)."</pre>";
		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return false;

		// Fetch xfields data
		$xdata = xf_decode($SQLold['xfields']);
		if (!is_array($xdata))
			return false;

		$output = '';
		$xfEntries = array();

		foreach ($xf['news'] as $id => $data) {
			if ($data['disabled'])
				continue;

			$xfEntry = array(
				'title'		=>	$data['title'],
				'id'		=>	$id,
				'required'	=>	$lang['xfields_fld_'.($data['required']?'required':'optional')],
				'flags'		=>	array(
					'required'	=>	$data['required']?true:false,
				),
			);
			switch ($data['type']) {
				case 'text'  : 	$val = '<input type="text" name="xfields['.$id.']"  id="form_xfields_'.$id.'" title="'.$data['title'].'" value="'.secure_html($xdata[$id]).'" />';
								$xfEntry['input'] = $val;
								$xfEntries[intval($data['area'])][] = $xfEntry;
								break;
				case 'select': 	$val = '<select name="xfields['.$id.']" id="form_xfields_'.$id.'" >';
								if (!$data['required']) $val .= '<option value="">&nbsp;</option>';
								if (is_array($data['options']))
									foreach ($data['options'] as $k => $v) {
										$val .= '<option value="'.secure_html(($data['storekeys'])?$k:$v).'"'.((($data['storekeys'] && ($xdata[$id] == $k))||(!$data['storekeys'] && ($xdata[$id] == $v)))?' selected':'').'>'.$v.'</option>';
									}
								$val .= '</select>';
								$xfEntry['input'] = $val;
								$xfEntries[intval($data['area'])][] = $xfEntry;
								break;
				case 'textarea'	:
								$val = '<textarea cols="30" rows="4" name="xfields['.$id.']" id="form_xfields_'.$id.'">'.$xdata[$id].'</textarea>';
								$xfEntry['input'] = $val;
								$xfEntries[intval($data['area'])][] = $xfEntry;
								break;
				case 'images'	:
					// First - show already attached images
					$iCount = 0;
					$input = '';
					$tVars = array( 'images' => array());

					//$tpl -> template('ed_entry.image', extras_dir.'/xfields/tpl');
					if (is_array($SQLold['#images'])) {
						foreach ($SQLold['#images'] as $irow) {
							// Skip images, that are not related to current field
							if (($irow['plugin'] != 'xfields') || ($irow['pidentity'] != $id)) continue;

							// Show attached image
							$iCount++;

							$tImage = array(
								'number'	=>	$iCount,
								'id'		=>	$id,
								'preview'	=>	array(
									'width'		=>	$irow['p_width'],
									'height'	=>	$irow['p_height'],
									'url' 		=>	$config['attach_url'].'/'.$irow['folder'].'/thumb/'.$irow['name'],
								),
								'image'		=>	array(
									'id'		=> $irow['id'],
									'number'	=> $iCount,
									'url'		=> $config['attach_url'].'/'.$irow['folder'].'/'.$irow['name'],
									'width'		=> $irow['width'],
									'height'	=> $irow['height'],
								),
								'flags'		=> array(
									'preview'	=> $irow['preview']?true:false,
									'exist'		=> true,
								),
								'description'	=> secure_html($irow['description']),
							);
							$tVars['images'][] = $tImage;
						}
					}

					// Second - show entries for allowed number of attaches
					for ($i = $iCount+1; $i <= intval($data['maxCount']); $i++) {
						$tImage = array(
							'number'	=>	$i,
							'id'		=>	$id,
							'flags'		=> array(
								'exist'		=> false,
							),
						);
						$tVars['images'][] = $tImage;
					}

					// Make template
					$xt = $twig->loadTemplate('plugins/xfields/tpl/ed_entry.image.tpl');
					$val = $xt->render($tVars);
					$xfEntry['input'] = $val;
					$xfEntries[intval($data['area'])][] = $xfEntry;
					break;
			}
		}
		$xfCategories = array();
		foreach ($catz as $cId => $cData) {
			$xfCategories[$cData['id']] = $cData['xf_group'];
		}

		// Prepare table data [if needed]
		$flagTData = false;
		if (isset($xf['tdata']) && is_array($xf['tdata'])) {
			// Load table data for specific news
			$tlist = array();
			foreach ($mysql->select("select * from ".prefix."_xfields where (linked_ds = 1) and (linked_id = ".db_squote($newsID).")") as $trow) {
				$ts = unserialize($trow['xfields']);
				$tEntry = array('#id' => $trow['id']);
				// Scan every field for value
				foreach ($xf['tdata'] as $fId => $fData) {
					$fValue = '';
					if (is_array($ts) && isset($ts[$fId])) {
						$fValue = $ts[$fId];
					} elseif (isset($trow['xfields_'.$fId])) {
						$fValue = $trow['xfields_'.$fId];
					}
					$tEntry[$fId] = $fValue;
				}
				$tlist []= $tEntry;
			}

			// Prepare config
			$tclist = array();
			$thlist = array();
			foreach ($xf['tdata'] as $fId => $fData) {
				if ($fData['disabled'])
					continue;

				$flagTData = true;

				$tclist[$fId] = array(
					'title'		=> $fData['title'],
					'required'	=> $fData['required'],
					'type'		=> $fData['type'],
					'default'	=> $fData['default'],
				);
				$thlist [] = array(
					'id'	=> $fId,
					'title'	=> $fData['title'],
				);
				if ($fData['type'] == 'select') {
					$tclist[$fId]['storekeys']	= $fData['storekeys'];
					$tclist[$fId]['options']	= $fData['options'];
				}

			}
		}

		// Prepare personal [group] variables
		$tVars = array(
		//	'entries'		=>	$xfEntries[0],
			'xfGC'			=>	json_encode(arrayCharsetConvert(0, $xf['grp.news'])),
			'xfCat'			=>	json_encode(arrayCharsetConvert(0, $xfCategories)),
			'xfList'		=>	json_encode(arrayCharsetConvert(0, array_keys($xf['news']))),
			'xtableConf'	=>	json_encode(arrayCharsetConvert(0, $tclist)),
			'xtableVal'		=>	json_encode(arrayCharsetConvert(0, $tlist)),
			'xtableHdr'		=>	$thlist,
			'xtablecnt'		=>	count($thlist),
			'flags'			=> array(
				'tdata'		=> $flagTData,
			),
		);

		if (!isset($xfEntries[0])) {
			$xfEntries[0] = array();
		}

		foreach ($xfEntries as $k => $v) {
			// Check if we have template for specific area, elsewhere - use basic [0] template
			$templateName = 'plugins/xfields/tpl/news.edit.'.(file_exists(root.'plugins/xfields/tpl/news.edit.'.$k.'.tpl')?$k:'0').'.tpl';

			$xt = $twig->loadTemplate($templateName);
			$tVars['entries']		= $v;
			$tVars['entryCount']	= count($v);
			$tVars['area']			= $k;

			// Table data is available only for area 0
			$tVars['flags']['tdata']	= (!$k)?$flagTData:0;

			// Render block
			$tvars['plugin']['xfields'][$k] .= $xt->render($tVars);
		}

		unset($tVars['entries']);
		unset($tVars['area']);

		// Render general part [with JavaScript]
		$xt = $twig->loadTemplate('plugins/xfields/tpl/news.general.tpl');
		$tvars['plugin']['xfields']['general'] = $xt->render($tVars);

		return 1;
	}
	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		global $lang, $config, $mysql;

		//	print "<pre>POST VARS: ".var_export($_POST, true)."</pre>";

		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return 1;

		$rcall = $_POST['xfields'];
		if (!is_array($rcall)) $rcall = array();

		// Decode previusly stored data
		$oldFields = xf_decode($SQLold['xfields']);

		// Manage attached images
		xf_modifyAttachedImages(1, $newsID, $xf, $SQLold['#images']);

		$xdata = array();

		// Scan fields and check if we have attached images for fields with type 'images'
		$haveImages = false;
		foreach ($xf['news'] as $fid => $fval) {
			if ($fval['type'] == 'images') {
				$haveImages = true;
				break;
			}
		}

		if ($haveImages) {
			// Get real ID's of attached images and print here
			$idlist = array();
			foreach ($mysql->select("select id, plugin, pidentity from ".prefix."_images where (linked_ds = 1) and (linked_id = ".db_squote($newsID).")") as $irec) {
				if ($irec['plugin'] == 'xfields') {
					$idlist[$irec['pidentity']] []= $irec['id'];
				}
			}

			// Scan for fields that should be configured to have attached images
			foreach ($xf['news'] as $fid => $fval) {
				if (($fval['type'] == 'images')&&(is_array($idlist[$fid]))) {
					$xdata[$fid] = join(",", $idlist[$fid]);
				}
			}
		}


		foreach ($xf['news'] as $id => $data) {
			// Attached images are processed in special way
			if ($data['type'] == 'images') {
				continue;
			}

			// Skip disabled fields
			if ($data['disabled']) {
				$xdata[$id] = $oldFields[$id];
				continue;
			}

			if ($rcall[$id] != '') {
				$xdata[$id] = $rcall[$id];
			} else if ($data['required']) {
				msg(array("type" => "error", "text" => str_replace('{field}', $id, $lang['xfields_msge_emptyrequired'])));
				return 0;
			}
			// Check if we should save data into separate SQL field
			if ($data['storage'])
				$SQLnew['xfields_'.$id] = $rcall[$id];
		}

		// Prepare table data [if needed]
		$haveTable = false;

		if (isset($xf['tdata']) && is_array($xf['tdata']) && isset($_POST['xftable']) && is_array($xft = json_decode(iconv('Windows-1251', 'UTF-8', $_POST['xftable']), true))) {
			$xft = arrayCharsetConvert(1, $xft);
			//print "<pre>[".(is_array($xft)?'ARR':'NOARR')."]INCOMING ARRAY: ".var_export($xft, true)."</pre>";
			$recList = array();
			$queryList = array();
			// SCAN records
			foreach ($xft as $k => $v) {
				if (is_array($v) && isset($v['#id'])) {
					$editMode = 0;
					$tOldRec = array();
					$tOldRecX = array();
					if (intval($v['#id'])) {
						$recList []= intval($v['#id']);
						$editMode = 1;
						$tOldRec = $mysql->record("select * from ".prefix."_xfields where (id = ".intval($v['#id']).") and (linked_ds = 1) and (linked_id = ".intval($newsID).")");
						$tOldRecX = unserialize($tOldRec['xfields']);
					}

					$tRec = array('xfields' => array());
					foreach ($xf['tdata'] as $fId => $fData) {
						// Manage disabled fields
						if ($fData['disabled']) {
							$tRec['xfields'][$fId] = $tOldRecX[$fId];
							continue;
						}

						if ($fData['storage']) {
							$tRec['xfields_'.$fId] = db_squote($v[$fId]);
						}
						$tRec['xfields'][$fId]= $v[$fId];
					}

					$tRec['xfields'] = db_squote(serialize($tRec['xfields']));

					// Now update record info
					$haveTable = true;
					if ($editMode) {
						$vt = array();
						foreach ($tRec as $kx => $vx) { $vt []= $kx." = ".$vx;	}

						$query = "update ".prefix."_xfields set ".join(", ", $vt)." where (id = ".intval($v['#id']).") and (linked_ds = 1) and (linked_id = ".intval($newsID).")";
						//print "SQL: $query <br/>\n";
						$queryList []= $query;
						//$mysql->query($query);
					} else {

						$query = "insert into ".prefix."_xfields (".join(", ", array_keys($tRec)).", linked_ds, linked_id) values (".join(", ", array_values($tRec)).", 1, ".(intval($newsID)).")";
						//print "SQL: $query <br/>\n";
						$queryList []= $query;
						//$mysql->query($query);
					}

					//print "GOT LINE:<pre>".var_export($tRec, true)."</pre>";
				}
			}
			// Now delete old lines
			if (count($recList)) {
				$query = "delete from ".prefix."_xfields where (linked_ds = 1) and (linked_id = ".intval($newsID).") and id not in (".join(", ", $recList).")";
			} else {
				$query = "delete from ".prefix."_xfields where (linked_ds = 1) and (linked_id = ".intval($newsID).")";
			}
			$mysql->query($query);

			// Execute queries
			foreach ($queryList as $query) {
				$mysql->query($query);
			}

		}
		// Save info about table data
		if ($haveTable)
			$xdata['#table'] = 1;

	    $SQLnew['xfields']   = xf_encode($xdata);
		return 1;
	}

	// Delete news notifier [ after news is deleted ]
	function deleteNewsNotify($newsID, $SQLnews) {
		global $mysql;

		$query = "delete from ".prefix."_xfields where (linked_ds = 1) and (linked_id = ".intval($newsID).")";
		$mysql->query($query);

		return 1;
	}

	// Show news call :: processor (call after all processing is finished and before show)
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $mysql, $config, $twigLoader, $twig, $PFILTERS, $twig, $twigLoader;
		// Try to load config. Stop processing if config was not loaded
		if (($xf = xf_configLoad()) === false) return;

		$fields = xf_decode($SQLnews['xfields']);
		$content = $SQLnews['content'];

		// Check if we have at least one `image` field and load TWIG template if any
		if (is_array($xf['news']))
			foreach ($xf['news'] as $k => $v) {
				if ($v['type'] == 'images') {

					// Yes, we have it!
					$conversionParams = array();
					$imagesTemplateFileName = 'plugins/xfields/tpl/news.show.images.tpl';
					$twigLoader->setConversion($imagesTemplateFileName, $conversionConfig);
					$xtImages = $twig->loadTemplate($imagesTemplateFileName);
					break;
				}
			}

		// Show extra fields if we have it
		if (is_array($xf['news']))
			foreach ($xf['news'] as $k => $v) {
				$kp = preg_quote($k, "#");
				$xfk = isset($fields[$k])?$fields[$k]:'';

				// Our behaviour depends on field type
				if ($v['type'] == 'images') {
					// Check if there're attached images
					if ($xfk && count($ilist = explode(",", $xfk)) && count($imglist = $mysql->select("select * from ".prefix."_images where id in (".$xfk.")"))) {
						// Yes, show field block
						$tvars['regx']["#\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]#is"] = '$1';
						$tvars['regx']["#\[nxfield_".$kp."\](.*?)\[/nxfield_".$kp."\]#is"] = '';

						// Scan for images and prepare data for template show
						$tiVars = array(
							'fieldName'		=> $k,
							'fieldTitle'	=> secure_html($v['title']),
							'fieldType'		=> $v['type'],
							'entriesCount'	=> count($imglist),
							'entries'		=> array(),
							'execStyle'		=> $mode['style'],
							'execPlugin'	=> $mode['plugin'],
						);
						foreach ($imglist as $imgInfo) {
							$tiEntry = array(
								'url'			=> ($imgInfo['storage']?$config['attach_url']:$config['files_url']).'/'.$imgInfo['folder'].'/'.$imgInfo['name'],
								'width'			=> $imgInfo['width'],
								'height'		=> $imgInfo['height'],
								'pwidth'		=> $imgInfo['p_width'],
								'pheight'		=> $imgInfo['p_height'],
								'name'			=> $imgInfo['name'],
								'origName'		=> secure_html($imgInfo['orig_name']),
								'description'	=> secure_html($imgInfo['description']),

								'flags'		=> array(
									'hasPreview'	=> $imgInfo['preview'],
								),
							);

							if ($imgInfo['preview']) {
								$tiEntry['purl'] = ($imgInfo['storage']?$config['attach_url']:$config['files_url']).'/'.$imgInfo['folder'].'/thumb/'.$imgInfo['name'];
							}

							$tiVars['entries'] []= $tiEntry;
						}

						// Render field value
						$tvars['vars']['[xvalue_'.$k.']'] = $xtImages->render($tiVars);
					} else {
						$tvars['regx']["#\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]#is"] = '';
						$tvars['regx']["#\[nxfield_".$kp."\](.*?)\[/nxfield_".$kp."\]#is"] = '$1';
						$tvars['vars']['[xvalue_'.$k.']'] = '';
					}
				} else {
					$tvars['regx']["#\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]#is"] = ($xfk == "")?"":"$1";
					$tvars['regx']["#\[nxfield_".$kp."\](.*?)\[/nxfield_".$kp."\]#is"] = ($xfk == "")?"$1":"";
					$tvars['vars']['[xvalue_'.$k.']'] = ($v['type'] == 'textarea')?'<br/>'.(str_replace("\n","<br/>\n",$xfk).(strlen($xfk)?'<br/>':'')):$xfk;
				}
			}

		// Show table if we have it
		if (isset($xf['tdata']) && is_array($xf['tdata']) && isset($fields['#table']) && ($fields['#table'] == 1)) {
			// Yes, we have table. Display it!

			// Prepare conversion table
			$conversionConfig = array(
					'[entries]' => '{% for entry in entries %}',
					'[/entries]' => '{% endfor %}',
			);

			$xrecs = array();
			$npp = 1;
			foreach ($mysql->select("select * from ".prefix."_xfields where (linked_ds = 1) and (linked_id = ".db_squote($newsID).") order by id", 1) as $trec) {
				$xrec = array(
					'num'	=> ($npp++),
					'id'	=> $trec['id'],
					'flags'	=> array(),
				);

				foreach ($xf['tdata'] as $tid => $tval) {
					// Skip disabled
					if ($tval['disabled'])
						continue;

					//  Populate field data
					$drec = unserialize($trec['xfields']);
					$xrec['field_'.$tid] = $drec[$tid];
					$xrec['flags']['field_'.$tid] = ($drec[$tid] != '')?1:0;

					$conversionConfig['{entry_field_'.$tid.'}'] = '{{ entry.field_'.$tid.' }}';
				}

				// Process filters (if any)
				if (isset($PFILTERS['xfields']) && is_array($PFILTERS['xfields']))
					foreach ($PFILTERS['xfields'] as $k => $v) { $v->showTableEntry($newsID, $SQLnews, $trec, $xrec); }

				$xrecs []= $xrec;
			}

			// Show table
			$templateName = 'plugins/xfields/news.table.tpl';
			$twigLoader->setConversion($templateName, $conversionConfig);

			$xt = $twig->loadTemplate($templateName);
			$tvars['vars']['plugin_xfields_table'] = $xt->render(array('entries' => $xrecs));

		} else {
			$tvars['vars']['plugin_xfields_table'] = '';
		}

		$SQLnews['content'] = $content;
	}
}

// Manage uprofile modifications
if (getPluginStatusActive('uprofile')) {
	loadPluginLibrary('uprofile', 'lib');

	class XFieldsUPrifileFilter extends p_uprofileFilter {
		function editProfileForm($userID, $SQLrow, &$tvars) {
			global $lang, $catz, $mysql, $config, $twig, $twigLoader;

			//print "<pre>".var_export($lang, true)."</pre>";
			// Load config
			$xf = xf_configLoad();
			if (!is_array($xf))
				return false;

			// Fetch xfields data
			$xdata = xf_decode($SQLrow['xfields']);
			if (!is_array($xdata))
				return false;

			$output = '';
			$xfEntries = array();

			foreach ($xf['users'] as $id => $data) {
				if ($data['disabled'])
					continue;

				//print "FLD: [$id]<br>\n";
				$xfEntry = array(
					'title'		=>	$data['title'],
					'id'		=>	$id,
					'required'	=>	$lang['xfields_fld_'.($data['required']?'required':'optional')],
					'flags'		=>	array(
						'required'	=>	$data['required']?true:false,
					),
				);
				switch ($data['type']) {
					case 'text'  : 	$val = '<input type="text" name="xfields['.$id.']"  id="form_xfields_'.$id.'" title="'.$data['title'].'" value="'.secure_html($xdata[$id]).'" />';
						$xfEntry['input'] = $val;
						$xfEntries[intval($data['area'])][] = $xfEntry;
						break;
					case 'select': 	$val = '<select name="xfields['.$id.']" id="form_xfields_'.$id.'" >';
						if (!$data['required']) $val .= '<option value="">&nbsp;</option>';
						if (is_array($data['options']))
							foreach ($data['options'] as $k => $v) {
								$val .= '<option value="'.secure_html(($data['storekeys'])?$k:$v).'"'.((($data['storekeys'] && ($xdata[$id] == $k))||(!$data['storekeys'] && ($xdata[$id] == $v)))?' selected':'').'>'.$v.'</option>';
							}
						$val .= '</select>';
						$xfEntry['input'] = $val;
						$xfEntries[intval($data['area'])][] = $xfEntry;
						break;
					case 'textarea'	:
						$val = '<textarea cols="30" rows="4" name="xfields['.$id.']" id="form_xfields_'.$id.'">'.$xdata[$id].'</textarea>';
						$xfEntry['input'] = $val;
						$xfEntries[intval($data['area'])][] = $xfEntry;
						break;
					case 'images'	:
						// First - show already attached images
						$iCount = 0;
						$input = '';
						$tVars = array( 'images' => array());

						if (is_array($SQLrow['#images'])) {
							foreach ($SQLrow['#images'] as $irow) {
								// Skip images, that are not related to current field
								if (($irow['plugin'] != 'xfields') || ($irow['pidentity'] != $id)) continue;

								// Show attached image
								$iCount++;

								$tImage = array(
									'number'	=>	$iCount,
									'id'		=>	$id,
									'preview'	=>	array(
										'width'		=>	$irow['p_width'],
										'height'	=>	$irow['p_height'],
										'url' 		=>	$config['attach_url'].'/'.$irow['folder'].'/thumb/'.$irow['name'],
									),
									'image'		=>	array(
										'id'		=> $irow['id'],
										'number'	=> $iCount,
										'url'		=> $config['attach_url'].'/'.$irow['folder'].'/'.$irow['name'],
										'width'		=> $irow['width'],
										'height'	=> $irow['height'],
									),
									'flags'		=> array(
										'preview'	=> $irow['preview']?true:false,
										'exist'		=> true,
									),
								);
								$tVars['images'][] = $tImage;
							}
						}

						// Second - show entries for allowed number of attaches
						for ($i = $iCount+1; $i <= intval($data['maxCount']); $i++) {
							$tImage = array(
								'number'	=>	$i,
								'id'		=>	$id,
								'flags'		=> array(
									'exist'		=> false,
								),
							);
							$tVars['images'][] = $tImage;
						}

						// Make template
						$xt = $twig->loadTemplate('plugins/xfields/tpl/ed_entry.image.tpl');
						$val = $xt->render($tVars);
						$xfEntry['input'] = $val;
						$xfEntries[intval($data['area'])][] = $xfEntry;
						break;

				}
			}

			// Prepare configuration array
			$tVars = array();

			// Area 0 should always be configured
			if (!isset($xfEntries[0])) {
				$xfEntries[0] = array();
			}

			// For compatibility with old template engine, init values for blocks 0 and 1
			$tvars['vars']['plugin_xfields_0'] = '';
			$tvars['vars']['plugin_xfields_1'] = '';

			foreach ($xfEntries as $k => $v) {
				// Check if we have template for specific area, elsewhere - use basic [0] template
				$templateName = 'plugins/xfields/tpl/uprofile.edit.'.(file_exists(root.'plugins/xfields/tpl/uprofile.edit.'.$k.'.tpl')?$k:'0').'.tpl';

				$xt = $twig->loadTemplate($templateName);
				$tVars['entries']		= $v;
				$tVars['entryCount']	= count($v);
				$tVars['area']			= $k;

				// Render block
				$tvars['vars']['plugin_xfields_'.$k] .= $xt->render($tVars);
			}
/*
			unset($tVars['entries']);
			unset($tVars['area']);

			// Render general part [with JavaScript]
			$xt = $twig->loadTemplate('plugins/xfields/tpl/news.general.tpl');
			$tvars['plugin']['xfields']['general'] = $xt->render($tVars);


			$xt = $twig->loadTemplate('plugins/xfields/tpl/ed_uprofile.tpl');
			$tvars['vars']['plugin_xfields'] .= $xt->render($tVars);
*/
			return 1;

		}

		function editProfile($userID, $SQLrow, &$SQLnew) {
			global $lang, $config, $mysql, $DSlist;

			//print "<pre>editProfile() POST VARS: ".var_export($_POST, true)."</pre>";

			// Load config
			$xf = xf_configLoad();
			if (!is_array($xf))
				return 1;

			$rcall = $_POST['xfields'];
			if (!is_array($rcall)) $rcall = array();

			// Decode previusly stored data
			$oldFields = xf_decode($SQLrow['xfields']);

			// Manage attached images
			xf_modifyAttachedImages($DSlist['users'], $userID, $xf, $SQLrow['#images']);

			$xdata = array();
			//print "XF[users]: <pre>".var_export($xf['users'], true)."</pre>";
			// Scan fields and check if we have attached images for fields with type 'images'
			$haveImages = false;
			foreach ($xf['users'] as $fid => $fval) {
				if ($fval['type'] == 'images') {
					$haveImages = true;
					break;
				}
			}

			if ($haveImages) {
				// Get real ID's of attached images and print here
				$idlist = array();
				foreach ($mysql->select("select id, plugin, pidentity from ".prefix."_images where (linked_ds = ".$DSlist['users'].") and (linked_id = ".db_squote($userID).")") as $irec) {
					if ($irec['plugin'] == 'xfields') {
						$idlist[$irec['pidentity']] []= $irec['id'];
					}
				}

				// Scan for fields that should be configured to have attached images
				foreach ($xf['users'] as $fid => $fval) {
					if (($fval['type'] == 'images')&&(is_array($idlist[$fid]))) {
						$xdata[$fid] = join(",", $idlist[$fid]);
					}
				}
			}


			foreach ($xf['users'] as $id => $data) {
				// Attached images are processed in special way
				if ($data['type'] == 'images') {
					continue;
				}

				// Skip disabled fields
				if ($data['disabled']) {
					$xdata[$id] = $SQLrow[$id];
					continue;
				}

				if ($rcall[$id] != '') {
					$xdata[$id] = $rcall[$id];
				} else if ($data['required']) {
					msg(array("type" => "error", "text" => str_replace('{field}', $id, $lang['xfields_msge_emptyrequired'])));
					return 0;
				}
				// Check if we should save data into separate SQL field
				if ($data['storage'])
					$SQLnew['xfields_'.$id] = $rcall[$id];
			}

			$SQLnew['xfields']   = xf_encode($xdata);

			return 1;
		}

		function showProfile($userID, $SQLrow, &$tvars) {
		global $mysql, $config;
			// Try to load config. Stop processing if config was not loaded
			if (($xf = xf_configLoad()) === false) return;

			$fields = xf_decode($SQLrow['xfields']);

			// Show extra fields if we have it
			if (is_array($xf['users']))
				foreach ($xf['users'] as $k => $v) {
					$kp = preg_quote($k, "#");
					$xfk = isset($fields[$k])?$fields[$k]:'';

					// Our behaviour depends on field type
					if ($v['type'] == 'images') {
						// Check if there're attached images
						if ($xfk && count($ilist = explode(",", $xfk)) && count($imglist = $mysql->select("select * from ".prefix."_images where id in (".$xfk.")"))) {
							//print "-xGotIMG[$k]";
							// Yes, get list of images
							$imgInfo = $imglist[0];
							$tvars['regx']["#\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]#is"] = '$1';
							$tvars['regx']["#\[nxfield_".$kp."\](.*?)\[/nxfield_".$kp."\]#is"] = '';

							$iname = ($imgInfo['storage']?$config['attach_url']:$config['files_url']).'/'.$imgInfo['folder'].'/'.$imgInfo['name'];
							$tvars['vars']['[xvalue_'.$k.']'] = $iname;

						} else {
							$tvars['regx']["#\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]#is"] = '';
							$tvars['regx']["#\[nxfield_".$kp."\](.*?)\[/nxfield_".$kp."\]#is"] = '$1';

						}
					} else {
						$tvars['regx']["#\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]#is"] = ($xfk == "")?"":"$1";
						$tvars['regx']["#\[nxfield_".$kp."\](.*?)\[/nxfield_".$kp."\]#is"] = ($xfk == "")?"$1":"";
						$tvars['vars']['[xvalue_'.$k.']'] = ($v['type'] == 'textarea')?'<br/>'.(str_replace("\n","<br/>\n",$xfk).(strlen($xfk)?'<br/>':'')):$xfk;
					}
				}
		}
	}
	register_filter('plugin.uprofile','xfields', new XFieldsUPrifileFilter);
}


class XFieldsFilterAdminCategories extends FilterAdminCategories{
	function addCategory(&$tvars, &$SQL) {
		$SQL['xf_group'] = $_REQUEST['xf_group'];
		return 1;
	}

	function addCategoryForm(&$tvars) {
		global $lang;
		loadPluginLang('xfields', 'config', '', '', ':');

		// Get config
		$xf = xf_configLoad();

		// Prepare select
		$ms = '<select name="xf_group"><option value="">** все поля **</option>';
		if (isset($xf['grp.news'])) {
			foreach ($xf['grp.news'] as $k => $v) {
				$ms .= '<option value="'.$k.'">'.$k.' ('.$v['title'].')</option>';
			}
		}

		$tvars['vars']['extend'] .= '<tr><td width="70%" class="contentEntry1">'.$lang['xfields:categories.group'].'<br/><small>'.$lang['xfields:categories.group#desc'].'</small></td><td width="30%" class="contentEntry2">'.$ms.'</td></tr>';
		return 1;
	}


	function editCategoryForm($categoryID, $SQL, &$tvars) {
		global $lang;
		loadPluginLang('xfields', 'config', '', '', ':');

		// Get config
		$xf = xf_configLoad();

		// Prepare select
		$ms = '<select name="xf_group"><option value="">** все поля **</option>';
		foreach ($xf['grp.news'] as $k => $v) {
			$ms .= '<option value="'.$k.'"'.(($SQL['xf_group'] == $k)?' selected="selected"':'').'>'.$k.' ('.$v['title'].')</option>';
		}

		$tvars['vars']['extend'] .= '<tr><td width="70%" class="contentEntry1">'.$lang['xfields:categories.group'].'<br/><small>'.$lang['xfields:categories.group#desc'].'</small></td><td width="30%" class="contentEntry2">'.$ms.'</td></tr>';
		return 1;
	}

	function editCategory($categoryID, $SQL, &$SQLnew, &$tvars) {
		$SQLnew['xf_group'] = $_REQUEST['xf_group'];
		return 1;
	}
}


register_filter('news','xfields', new XFieldsNewsFilter);
register_admin_filter('categories', 'xfields', new XFieldsFilterAdminCategories);


// Global XF variables
$XF = array();		// $XF - array with configuration
$XF_loaded = 0;		// $XF_loaded - flag if config is loaded


// Load fields definition
function xf_configLoad() {
	global $lang, $XF, $XF_loaded;

	if ($XF_loaded) return $XF;
	if (!($confdir = get_plugcfg_dir('xfields'))) return false;

	if (!file_exists($confdir.'/config.php')) {
		$XF_loaded = 1;
		return array( 'news' => array());
	}
	include $confdir.'/config.php';
	$XF_loaded = 1;

	$XF = is_array($xarray)?$xarray:array();

	// Init required blocks if they are not initialized yet
	foreach (array('news', 'grp.news', 'users', 'tdata') as $k) {
		if (!is_array($XF[$k])) {
			$XF[$k] = array();
		}
	}


	return $XF;
}

// Save fields definition
function xf_configSave($xf = null) {
	global $lang, $XF, $XF_loaded;

	if (!$XF_loaded) return false;
	if (!($confdir = get_plugcfg_dir('xfields'))) return false;

	// Open config
	if (!($fn = fopen($confdir.'/config.php', 'w'))) return false;

	// Write config
	fwrite($fn, "<?php\n\$xarray = ".var_export(is_array($xf)?$xf:$XF, true).";\n");
	fclose($fn);
	return true;
}

// Decode fields from text
function xf_decode($text){

	if ($text == '') return array();

	// MODERN METHOD
	if (substr($text,0,4) == "SER|") return unserialize(substr($text,4));

	// OLD METHOD. OBSOLETE but supported for reading
	$xfieldsdata = explode("||", $text);

	foreach ($xfieldsdata as $xfielddata) {
		list($xfielddataname, $xfielddatavalue) = explode("|", $xfielddata);
		$xfielddataname = str_replace("&#124;", "|", $xfielddataname);
		$xfielddataname = str_replace("__NEWL__", "\r\n", $xfielddataname);
		$xfielddatavalue = str_replace("&#124;", "|", $xfielddatavalue);
		$xfielddatavalue = str_replace("__NEWL__", "\r\n", $xfielddatavalue);
		$data[$xfielddataname] = $xfielddatavalue;
	}
	return $data;
}

// Encode fields into text
function xf_encode($fields){
	if (!is_array($fields)) return '';
	return 'SER|'.serialize($fields);
}


function xf_getTableBySectionID($sectionID) {
	switch ($sectionID) {
		case 'news':	return prefix.'_news';
		case 'users':	return prefix.'_users';
		case 'tdata':	return prefix.'_xfields';
	}
	return false;
}
