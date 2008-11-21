<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

register_plugin_page('gsmg','','plugin_gsmg_screen',0);

function plugin_gsmg_screen() {
	global $config, $mysql, $catz, $catmap, $SUPRESS_TEMPLATE_SHOW;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;

	@header('Content-type: text/xml; charset=utf-8');

	if (extra_get_param('gsmg','cache')) {
		$cacheData = cacheRetrieveFile('gsmg.txt', extra_get_param('gsmg','cacheExpire'), 'gsmg');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			print $cacheData;
			return;
		}
	}

	$output = '<?xml version="1.0" encoding="UTF-8"?>';
	$output.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';	

	// Настройки постранички
	if ($config['number']<1)
		$config['number'] = 5;

	// Надо ли выводить данные с головной страницы
	if (extra_get_param('gsmg','main')) {
		$output.= "<url>";
		$output.= "<loc><![CDATA[".home."]]></loc>";
		$output.= "<priority>".floatval(extra_get_param('gsmg', 'main_pr'))."</priority>";

		$lm = $mysql->record("select date(from_unixtime(max(postdate))) as pd from ".prefix."_news");
		$output.= "<lastmod>".$lm['pd']."</lastmod>";
		$output.= "<changefreq>daily</changefreq>";

		$output.= "</url>";

		if (extra_get_param('gsmg', 'mainp')) {
			$cnt = $mysql->record("select count(*) as cnt from ".prefix."_news");
			$pages = ceil($cnt['cnt'] / $config['number']);
			for ($i = 2; $i <= $pages; $i++) {
				$link = getLink('page', array('page' => $i));
				$output.= "<url>";
				$output.= "<loc><![CDATA[".$link."]]></loc>";
				$output.= "<priority>".floatval(extra_get_param('gsmg', 'mainp_pr'))."</priority>";
				$output.= "<lastmod>".$lm['pd']."</lastmod>";
				$output.= "<changefreq>daily</changefreq>";
				$output.= "</url>";
			}
		}
	}

	// Надо ли выводить данные по категориям
	if (extra_get_param('gsmg','cat')) {
		foreach  ($catmap as $id => $altname) {
				$link = getLink('category', array('id' => $id, 'alt' => $altname));
				$output.= "<url>";
				$output.= "<loc><![CDATA[".$link."]]></loc>";
				$output.= "<priority>".floatval(extra_get_param('gsmg', 'cat_pr'))."</priority>";
				$output.= "<lastmod>".$lm['pd']."</lastmod>";
				$output.= "<changefreq>daily</changefreq>";
				$output.= "</url>";

			if (extra_get_param('gsmg', 'catp')) {
				$pages = ceil($catz[$altname]['posts'] / $config['number']);
				for ($i = 2; $i <= $pages; $i++) {
					$link = getLink('category_page', array('page' => $i, 'alt' => $altname));
					$output.= "<url>";
					$output.= "<loc><![CDATA[".$link."]]></loc>";
					$output.= "<priority>".floatval(extra_get_param('gsmg', 'catp_pr'))."</priority>";
					$output.= "<lastmod>".$lm['pd']."</lastmod>";
					$output.= "<changefreq>daily</changefreq>";
					$output.= "</url>";
				}
			}
		}
	}

	// Надо ли выводить данные по новостям
	if (extra_get_param('gsmg','news')) {
		$query = "select id, alt_name, catid, date(from_unixtime(postdate)) as postdate, year(from_unixtime(postdate)) as year, month(from_unixtime(postdate)) as month, day(from_unixtime(postdate)) as day from ".prefix."_news where approve = 1";

		foreach ($mysql->select($query,1) as $rec) {
			$link = getLink('full', array('catid' => $rec['catid'], 'alt_name' => $rec['alt_name'], 'id' => $rec['id'], 'year' => $rec['year'], 'month' => $rec['month'], 'day' => $rec['day']));
			$output.= "<url>";
			$output.= "<loc><![CDATA[".$link."]]></loc>";
			$output.= "<priority>".floatval(extra_get_param('gsmg', 'news_pr'))."</priority>";
			$output.= "<lastmod>".$rec['postdate']."</lastmod>";
			$output.= "<changefreq>daily</changefreq>";
			$output.= "</url>";
		}
	}

	// Надо ли выводить данные по статическим страницам
	if (extra_get_param('gsmg','static')) {
		$query = "select id, alt_name from ".prefix."_static where approve = 1";

		foreach ($mysql->select($query,1) as $rec) {
			$link = getLink('static', array('alt_name' => $rec['alt_name'], 'id' => $rec['id'], 'year' => $rec['year'], 'month' => $rec['month'], 'day' => $rec['day']));
			$output.= "<url>";
			$output.= "<loc><![CDATA[".$link."]]></loc>";
			$output.= "<priority>".floatval(extra_get_param('gsmg', 'static_pr'))."</priority>";
			$output.= "<lastmod>".$lm['pd']."</lastmod>";
			$output.= "<changefreq>weekly</changefreq>";
			$output.= "</url>";
		}
	}

    $output.= "</urlset>";
    print $output;
	cacheStoreFile('gsmg.txt', $output, 'gsmg');
}
