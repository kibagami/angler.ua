<?php

namespace Angler\StoreBundle\Interfaces;

interface KeywordsInterface {

	/**
	 * @param $keyword \Angler\StoreBundle\Entity\KeywordFocused
	 */
	public function addFocusedKeyword(\Angler\StoreBundle\Entity\KeywordFocused $keyword);

	/**
	 * @param $keyword \Angler\StoreBundle\Entity\KeywordAdditional
	 */
	public function addAdditionalKeyword(\Angler\StoreBundle\Entity\KeywordAdditional $keyword);

	/**
	 * @param $keyword null|\Angler\StoreBundle\Entity\KeywordFocused
	 */
	public function removeFocusedKeyword(\Angler\StoreBundle\Entity\KeywordFocused $keyword);

	/**
	 * @param $keyword null|\Angler\StoreBundle\Entity\KeywordAdditional
	 */
	public function removeAdditionalKeyword(\Angler\StoreBundle\Entity\KeywordAdditional $keyword);

	/**
	 * @param string $text
	 * @param string $locale
	 */
	public function getAdditionalKeywordByTextAndLocale($text, $locale);

	/**
	 * @param string $text
	 * @param string $locale
	 */
	public function removeAdditionalKeywordByTextAndLocale($text, $locale);

	/**
	 * @param string $locale
	 */
	public function removeFocusedKeywordByLocale($locale);



	/**
	 * @return \Angler\StoreBundle\Entity\KeywordFocused[]
	 */
	public function getFocusedKeywords();

	/**
	 * @return \Angler\StoreBundle\Entity\KeywordAdditional[]
	 */
	public function getAdditionalKeywords();

	/**
	 * @param string $locale
	 * @return null | \Angler\StoreBundle\Entity\KeywordAdditional[]
	 */
	public function getAdditionalKeywordsByLocale($locale);

	/**
	 * @param string $locale
	 * @return null | \Angler\StoreBundle\Entity\KeywordFocused
	 */
	public function getFocusedKeywordByLocale($locale);

}
