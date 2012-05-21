<?php

namespace Angler\StoreBundle\Model;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

abstract class AnglerTranslatable implements Translatable {

	/** @var \Doctrine\Common\Collections\ArrayCollection|AbstractPersonalTranslation[] */
	protected $translations;

	/**
	 * @Gedmo\Locale
	 * Used locale to override Translation listener`s locale
	 * this is not a mapped field of entity metadata, just a simple property
	 */
	protected $locale;

	public function getLocale() {
		return $this->locale;
	}

	public function setLocale($locale) {
		$this->locale = $locale;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation[]
	 */
	public function getTranslations() {
		return $this->translations;
	}

	public function setTranslations($translations = null) {
		$this->translations = $translations;
	}

	/**
	 * @return array
	 */
	public function getTranslationsAsArray() {
		$translations = array();
		foreach($this->getTranslations() as $translation) {
			$translations[$translation->getLocale()][$translation->getField()] = $translation->getContent();
		}
		return $translations;
	}

	/**
	 * @param $field
	 * @return array
	 */
	public function getTranslationsByLocale($field) {
		$translations = array();
		foreach($this->getTranslations() as $translation) {
			if ($translation->getField() == $field) {
				$translations[$translation->getLocale()] = $translation->getContent();
			}
		}
		return $translations;
	}

	/**
	 * @param \Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation $t
	 * @return mixed
	 */
	public function addTranslation(AbstractPersonalTranslation $t) {
		foreach($this->getTranslations() as $k => $translation) {
			if ($translation->getLocale() == $t->getLocale() &&
				$translation->getField() == $t->getField()
			) {
				$this->translations[$k]->setContent($t->getContent());
				return;
			}
		}
		$this->translations[] = $t;
		$t->setObject($this);
	}

	/**
	 * use it function when clone object
	 * @param \Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation $t
	 */
	protected function addCloneTranslation(AbstractPersonalTranslation $t) {
		$this->translations[] = $t;
		$t->setObject($this);
	}

}
