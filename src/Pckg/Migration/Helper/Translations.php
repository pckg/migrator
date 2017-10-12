<?php

namespace Pckg\Migration\Helper;

use Pckg\Generic\Record\ListRecord;
use Pckg\Translator\Record\Translation;

/**
 * Class ListItem
 *
 * @package Pckg\Migration\Helper
 */
trait Translations
{
	/**
	 * @param       $slug
	 * @param array $translations
	 *
	 * @return ListRecord
	 * @internal param $id
	 */
	protected function translation($slug, $translations = [])
	{
		if (!is_array($translations)) {
			$translations['en'] = $translations;
		}

		$t = Translation::getOrCreate(['slug' => $slug]);
		foreach ($translations as $language => $translation) {
			$t->value       = $translation;
			$t->language_id = $language;
			$t->save();
		}
	}
}
