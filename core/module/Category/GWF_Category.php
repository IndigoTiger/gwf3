<?php
final class GWF_Category extends GWF_Tree
{
	const KEY_LENGTH = 64;
	public function getColumnPrefix() { return 'cat_'; }
	###########
	### GDO ###
	###########
	public function getClassName() { return __CLASS__; }
	public function getTableName() { return GWF_TABLE_PREFIX.'category'; }
	public function getColumnDefines()
	{
		return array_merge(parent::getColumnDefines(), 
		array(
			'cat_group' => array(GDO::INDEX|GDO::VARCHAR|GDO::ASCII|GDO::CASE_S, '', self::KEY_LENGTH),
			'translations' => array(GDO::GDO_ARRAY, GDO::NOT_NULL, array('GWF_CategoryTranslation', 'langid', 'catid', 'catid'), array('trans')),
			'trans' => array(GDO::JOIN, true, array('GWF_CategoryTranslation', 'cat_tree_id', 'cl_catid')),
		));
	}
//	public function getColumnDefines()
//	{
//		return array(
////			'cat_id' => array(GDO::AUTO_INCREMENT),
////			'cat_name' => array(GDO::VARCHAR|GDO::ASCII|GDO::CASE_S|GDO::INDEX, true, self::KEY_LENGTH),
////			'cat_pid' => array(GDO::UINT|GDO::INDEX, 0),
////			'cat_group' => array(GDO::INDEX|GDO::VARCHAR|GDO::ASCII|GDO::CASE_S, '', self::KEY_LENGTH),
////			'translations' => array(GDO::PR_ARRAY, 0 , array('GWF_CategoryTranslation', 'catid', 'catid'), 'langid'),
//		);
//	}
//	public function getID() { return $this->getVar('cat_id'); }
//	public function getKey() { return $this->getVar('cat_name'); }
//	public function getPID() { return $this->getVar('cat_pid'); }
	
	public function getGroup() { return $this->getVar('cat_group'); }
	
	public static function getAllCategoriesCached($orderby='cat_tree_key ASC', $group='')
	{
		static $cats = true;
		if ($cats === true)
		{
			$cats = self::getAllCategories($orderby);
		}
		return $cats;
	}
	
	public static function getAllCategories($orderby='cat_tree_key ASC', $group='')
	{
		$where = $group === '' ? '' : "cat_group='".self::escape($group)."'";
		return self::table(__CLASS__)->selectObjects('*', $where, $orderby);
	}
	
	/**
	 * @param $key string
	 * @return GWF_Category
	 */
	public static function getByKey($key)
	{
		return self::table(__CLASS__)->getBy('cat_tree_key', $key);
	}
	
	/**
	 * @param $id int
	 * @return GWF_Category
	 */
	public static function getByID($id)
	{
		return self::table(__CLASS__)->getRow($id);
	}
	
	public function getTranslations()
	{
		return $this->gdo_data['translations'];
	}
	
	public function getTranslation($langid)
	{
		return isset($this->gdo_data['translations'][$langid]['translation']) ? $this->gdo_data['translations'][$langid]['translation'] : false;
	}
	
	public static function keyExists($key)
	{
		return self::getByKey($key) !== false;
	}
	
	public function saveTranslation($langid, $text)
	{
		$catid = $this->getID();
		$langid = (int) $langid;
		$text = (string) $text;
		
//		echo sprintf('CHANGING Cat %s LangID %d to %s', $catid, $langid, $text);
		
		$trans = self::table('GWF_CategoryTranslation');
		if (false === ($t = $trans->getRow($catid, $langid))) {
			$t = new GWF_CategoryTranslation(array(
				'catid' => $catid,
				'langid' => $langid,
				'translation' => $text,
			));
		} else {
			$t->setVar('translation', $text);
		}
		
		if (false === $t->replace()) {
			return false;
		}
		
		$this->gdo_data['translations'][$langid] = array(
			'catid' => $catid,
			'langid' => $langid,
			'translation' => $text
		);
		return true;
	}

	public static function isValidKey($key)
	{
		$key = (string) $key;
		$len = self::KEY_LENGTH;
		return preg_match('/^[a-zA-Z0-9_]{1,'.$len.'}$/', $key) === 1;
	}
	
//	public function getGDOInput($key, $value)
//	{
//		$key = GWF_HTML::display($key);
//		if ($value === '' || $value <= 0) {
//			$value = false;
//		} else {
//			$value = (int) $value;
//		} 
//		
//		if (false === ($cats = self::getAllCategoriesCached('catid', 'DESC'))) {
//			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
//		}
//		
//		$back = sprintf('<select name="%s">', $key);
//		
//		$sel = $value === false ? ' selected="selected"' : '';
//		$back .= sprintf('<option value="0"%s>%s</option>', $sel, GWF_HTML::lang('sel_category'));
//		
//		
//		$langid = GWF_Language::getCurrentID();
//		
//		foreach ($cats as $cat)
//		{
//			$catid = $cat->getID();
//			$text = $cat->getTranslatedText($langid);
//			$sel = $value === $catid ? ' selected="selected"' : '';
//			$back .= sprintf('<option value="%s"%s>%s</option>', $catid, $sel, $text);
//		}
//		
//		$back .= '</select>';
//		return $back;
//	}
	
	public function getTranslatedText($langid=0)
	{
		$langid = (string) $langid;
		if ($langid === '0') {
			$langid = (string) GWF_Language::getCurrentID();
		}
		if (isset($this->gdo_data['translations'][$langid])) {
			return $this->gdo_data['translations'][$langid]['translation'];
		}
		else {
			return $this->getVar('key for '.$langid);
		}
	}
	
	public static function categoryExists($catid)
	{
		return self::getByID($catid) !== false;
	}
	
	public function getEditHREF()
	{
		return sprintf('%scategory/edit/%d-%s', GWF_WEB_ROOT, $this->getID(), $this->getKey());
	}
	
}

?>