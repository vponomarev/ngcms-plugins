<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class CategoryAccessNewsFilter extends NewsFilter {

	function CategoryAccessNewsFilter() {

		$this->flag = false;
		$this->flag2 = false;
		$this->templateName = '';
		$this->templatePath = '';
	}

	function GetParentCategory($cat, &$categorys) {

		global $catz, $catmap;
		$par_cat = $catz[$catmap[$cat]]['parent'];
		if ($par_cat && !in_array($par_cat, $categorys)) {
			$categorys[] = $par_cat;
			$this->GetParentCategory($par_cat, $categorys);
		}
	}

	public function showNews($newsID, $SQLnews, &$tvars, $mode = []) {

		global $userROW, $catmap, $catz;
		if ($this->flag) {
			$mode['overrideTemplateName'] = $this->templateName;
			$mode['overrideTemplatePath'] = $this->templatePath;
		}
		$acces_type = 0;
		if (!is_array($userROW)) $acces_type = pluginGetVariable('category_access', 'guest');
		else if ($userROW['status'] == 1) $acces_type = pluginGetVariable('category_access', 'admin');
		else if ($userROW['status'] == 2) $acces_type = pluginGetVariable('category_access', 'moder');
		else if ($userROW['status'] == 3) $acces_type = pluginGetVariable('category_access', 'journ');
		else if ($userROW['status'] == 4) $acces_type = pluginGetVariable('category_access', 'coment');
		$if_view = false;
		switch ($acces_type) {
			case 1:
				$cats = pluginGetVariable('category_access', 'categorys');
				$cur_cats = explode(',', $SQLnews['catid']);
				$count = count($cur_cats);
				for ($i = 0; $i < $count; $i++) {
					$this->GetParentCategory($cur_cats[$i], $cur_cats);
				}
				if (is_array($cats) && is_array($cur_cats) && count(array_intersect($cur_cats, $cats))) {
					$if_view = true;
					break;
				}
				$users = pluginGetVariable('category_access', 'users');
				$user = '';
				if (is_array($userROW)) $user = $userROW['name'];
				if (is_array($users) && array_key_exists($user, $users) && in_array($users[$user], $cur_cats)) $if_view = true;
				break;
			case 2:
				$if_view = true;
				break;
		}
		if (!$if_view) {
			if (!$this->flag) {
				$this->templateName = $mode['overrideTemplateName'];
				$this->templatePath = $mode['overrideTemplatePath'];
				$this->flag = true;
			}
			$mode['overrideTemplateName'] = '';
			$mode['overrideTemplatePath'] = extras_dir . '/category_access/tpl/';
		} else $this->flag2 = true;

		return 1;
	}

	function onAfterShow($mode) {

		global $template;
		if ($this->flag && !$this->flag2) $template['vars']['mainblock'] = pluginGetVariable('category_access', 'message');

		return 1;
	}

	function onAfterNewsShow($newsID, $SQLnews, $mode = array()) {

		global $template;
		if ($this->flag && !$this->flag2) $template['vars']['mainblock'] = pluginGetVariable('category_access', 'message');

		return 1;
	}
}

register_filter('news', 'category_access', new CategoryAccessNewsFilter);