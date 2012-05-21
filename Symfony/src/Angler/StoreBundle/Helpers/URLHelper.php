<?php

namespace Angler\StoreBundle\Helpers;

class URLHelper extends \Symfony\Component\Templating\Helper\Helper
{
	protected $settings;

	const URL_REWRITE_SPACE_SYMBOL = '-';

	public function __construct() {
		//$this->settings = \Context::getInstance()->getSettings(); //FIXME Replace it with symfony container
	}
	/**
	 * Returns the canonical name of this helper.
	 *
	 * @return string The canonical name
	 *
	 * @api
	 */
	public function getName() {
		return 'url';
	}

	public function linkToProduct(\Angler\StoreBundle\Entity\Product $product, \Angler\StoreBundle\Entity\Category $category = null, $locale = false, $absolute = false) {

		$url  = $product->getBaseURL();
		$url .= strlen($url) ? '-' : '';
		$url .= $this->linkToProductEnding($product->getId());

		// if no category specified we take canonical
		if (empty($category) || !$category->getId()) {
			$category = $product->getMainCategory();
		}

		if ($category && $category->getId()) {
			$url = $this->linkToCategory($category, $locale, $absolute) . '/' . $url;
		} else {
			$url = $this->wrapLink($url, $locale, $absolute);
		}

		$url = preg_replace('|([^:])/+|s', '\1/', $url);

		return $url;
	}

	public function linkToCategory(\Angler\StoreBundle\Entity\Category $category, $locale = false, $absolute = false) {
		$shortURL = bool($this->settings->offsetGet('SEO_SHORT_URL_STRUCTURE'));

		$url = $category->getURL();
		if ($locale) {
			$tranlations = $category->getTranslationsByLocale('url');
			$url = isset($tranlations[$locale]) ? $tranlations[$locale] : $url;
		}

		// get only last part of entire path
		if ($shortURL) {
			$parts = explode("/", $url);
			$url = array_pop($parts);
		}
		else {
			$url .= '/';
		}

		return $this->wrapLink ($url, $locale, $absolute);
	}

	public function linkToProductEnding($id) {
		$shortURL = bool($this->settings->offsetGet('SEO_SHORT_URL_STRUCTURE'));
		return !$shortURL ? $id . '.html' : 'p' . $id;
	}

	public function wrapLink($url, $locale, $absolute = false) {
		$languageDefaultCode = $this->settings->offsetGet('DEFAULT_LANGUAGE');
		if ($locale && $locale != $languageDefaultCode)
			$url = $locale . '/' . $url;
		if ($absolute)
			$url = HTTP_SERVER . DIR_WS_CATALOG . $url;// FIXME Define constants
		return $url;
	}

	public static function encodeTitle($title, $strict = true) {

		$space = self::URL_REWRITE_SPACE_SYMBOL;

		$sub_array = array(
			'ü' => 'uu',
			'ß' => 'ss',
			'°' => 'od',
			'õ' => 'ot',
			'ö' => 'ou',
			'ä' => 'au',
			'ë' => 'eu',
			'æ' => 'ae',
			'œ' => 'oe',
			'ø' => 'oe',
			'å' => 'aa',
			'Ü' => 'UU',
			'Ö' => 'OU',
			'Ø' => 'OE',
			'Ä' => 'AU',
			'Å' => 'AA',
			'Ë' => 'EU',
			'Œ' => 'OE',
			'Æ' => 'AE',
			'Õ' => 'OT',
			'+' => $space,
			'-' => $space,
			' ' => $space,
			';' => $space,
			'/' => $space,
			'?' => $space,
			':' => $space,
			'@' => $space,
			'&' => $space,
			'=' => $space,
			'$' => $space,
			',' => $space,
			'Щ' => 'Sht',
			'Ш' => 'Sh',
			'Ч' => 'Ch',
			'Ц' => 'Ts',
			'Ю' => 'Yu',
			'Я' => 'Ya',
			'Ж' => 'Zh',
			'А' => 'A',
			'Б' => 'B',
			'В' => 'V',
			'Г' => 'G',
			'Д' => 'D',
			'Е' => 'E',
			'Ё' => 'Jo',
			'З' => 'Z',
			'И' => 'I',
			'Й' => 'Y',
			'К' => 'K',
			'Л' => 'L',
			'М' => 'M',
			'Н' => 'N',
			'О' => 'O',
			'П' => 'P',
			'Р' => 'R',
			'С' => 'S',
			'Т' => 'T',
			'У' => 'U',
			'Ф' => 'F',
			'Х' => 'H',
			'Ь' => 'Y',
			'Ы' => 'Y',
			'Ъ' => 'A',
			'Э' => 'E',
			'Є' => 'Je',
			'Ї' => 'Ji',
			'І' => 'I',
			'щ' => 'sht',
			'ш' => 'sh',
			'ч' => 'ch',
			'ц' => 'ts',
			'ю' => 'ju',
			'я' => 'ja',
			'ж' => 'zh',
			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'g',
			'д' => 'd',
			'е' => 'e',
			'ё' => 'jo',
			'з' => 'z',
			'и' => 'i',
			'й' => 'y',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'h',
			'ь' => 'y',
			'ы' => 'y',
			'ъ' => 'a',
			'э' => 'e',
			'є' => 'je',
			'ї' => 'ji',
			'і' => 'i',
		);

		$title = strtr($title, $sub_array);
		$title = strtolower($title);

		$title = preg_replace('/[^a-z0-9_' . $space . ']/i', "", $title); // strip the rest
		if ($strict) {
			// This cleanup is not required if user set dashes explicitly
			$title = preg_replace('/\\' . $space . '+/', $space, $title); // Convert multiple underscores to a single one
			$title = trim($title, $space);
		}
		return $title;
	}
}
