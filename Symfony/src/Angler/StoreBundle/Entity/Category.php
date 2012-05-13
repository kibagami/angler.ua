<?php

namespace Angler\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 * @Gedmo\Tree(type="nested")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Angler\StoreBundle\Repository\CategoryRepository")
 * @ORM\Table(name="_obb_category")
 */
class Category extends Base\Category {

	/**
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	public $iconFile;

	/**
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	public $imageFile;


	/**
	 * @ORM\Id
	 * @ORM\Column(name="category_id", type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;

	/**
	 * Get self id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	public function uploadImage() {
		// the file property can be empty if the field is not required
		if (null === $this->imageFile) {
			return;
		}

		// we use the original file name here but you should
		// sanitize it at least to avoid any security issues

		// move takes the target directory and then the target filename to move to
		$this->imageFile->move(DIR_FS_ADMIN_IMAGES, $this->imageFile->getClientOriginalName());

		// set the path property to the filename where you'ved saved the file
		$this->image = $this->imageFile->getClientOriginalName();

		// clean up the file property as you won't need it anymore
		$this->imageFile = null;
	}

	public function uploadIcon()
	{
		// the file property can be empty if the field is not required
		if (null === $this->iconFile) {
			return;
		}

		// we use the original file name here but you should
		// sanitize it at least to avoid any security issues

		// move takes the target directory and then the target filename to move to
		$this->iconFile->move(DIR_FS_ADMIN_IMAGES, $this->iconFile->getClientOriginalName());

		// set the path property to the filename where you'ved saved the file
		$this->icon = $this->iconFile->getClientOriginalName();

		// clean up the file property as you won't need it anymore
		$this->iconFile = null;
	}

	/**
	 * Remove parent to set this category as root node
	 * @return void
	 */
	private function removeParent() { //FIXME Move this logic to the Event Listener
		if ($this->getParent()) {
			// update ActiveProductsCount for old parents
			$cat = $this->getParent();
			while ($cat) {
				$cat->setEnabledProductsCount($cat->getEnabledProductsCount() - $this->getEnabledProductsCount());
				$cat->setPurchasableProductsCount($cat->getPurchasableProductsCount() - $this->getPurchasableProductsCount());
				$cat = $cat->getParent();
			}
			$this->getParent()->getChildren()->removeElement($this);
			$this->parent = null;
		}
	}

	/**
	 * @param $parent \Angler\StoreBundle\Entity\Category|null
	 */
	public function setParent(\Angler\StoreBundle\Entity\Category $parent = null) {
		$this->removeParent(); // for updating ActiveProductsCount for old parents

		if ($parent) {
			$parent->getChildren()->add($this);
			$this->parent = $parent;

			// update ActiveProductsCount for new parents
			$category = $this->getParent(); //FIXME Move this logic to the Event Listener
			while ($category) {
				$category->setEnabledProductsCount($category->getEnabledProductsCount() + $this->getEnabledProductsCount());
				$category->setPurchasableProductsCount($category->getPurchasableProductsCount() + $this->getPurchasableProductsCount());
				$category = $category->getParent();
			}
		}
	}

	/**
	 * @param boolean $isActive
	 */
	public function setIsActive($isActive) {
		$this->isActive = $isActive;
		if ( $this->getParent()) $this->isActive = $isActive && $this->getParent()->getIsActive();
		foreach($this->getChildren() as $child) {
			$child->updateIsActive();
		}
	}

	public function updateIsActive() {
		$old_value = $this->getIsActive();
		if ( $this->getParent()) {
			$this->setIsActive($this->getIsEnabled() && $this->getParent()->getIsActive() );
		}  else {
			$this->setIsActive($this->getIsEnabled() );
		}

		if ($old_value != $this->getIsActive()) {
			foreach($this->getProducts() as $product ) {
				$product->updateIsInActiveCategory();
			}
		}
	}

	/**
	 * Set all category products
	 *
	 * @param \Doctrine\Common\Collections\ArrayCollection $products
	 * @return void
	 */
	public function setProducts($products) {
		$this->products = $products;
		$this->updateProductsCountSelf();
	}

	/**
	 * Get all products of category
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection | \Angler\StoreBundle\Entity\Product[]
	 */
	public function getProducts() {
		return $this->products;
	}

	/**
	 * Add product to products collection
	 * not handling adding of category to product
	 *
	 * @param \Angler\StoreBundle\Entity\Base\Product $product
	 * @return void
	 * FIXME: cover it with tests
	 */
	public function addProduct(\Angler\StoreBundle\Entity\Base\Product $product) {
		if (!$this->getProducts()->exists(function($key, $p) use ($product) {
			return $p === $product;
		})
		) {
			$product->getCategories()->add($this);
			$this->getProducts()->add($product);
			$this->updateProductsCountSelf();
		}
	}

	/**
	 * Add product to products collection not handling adding of category to product
	 *
	 * @param Product $product
	 * @return void
	 */
	public function removeProduct(Product $product) {
		$this->getProducts()->removeElement($product);
		$this->updateProductsCountSelf();
	}

	public function updateProductsCountSelf() {
		$diffEnabled = $this->enabledProductsCountSelf;
		$diffPurchasable = $this->purchasableProductsCountSelf;

		$this->enabledProductsCountSelf = $this->purchasableProductsCountSelf = 0;
		foreach ($this->getProducts() as $product) {
			if ($product->getIsEnabled()) {
				$this->enabledProductsCountSelf ++;
			}
			if ($product->getIsPurchasable()) {
				$this->purchasableProductsCountSelf ++;
			}
		}

		$diffEnabled = $this->enabledProductsCountSelf - $diffEnabled;
		$diffPurchasable = $this->purchasableProductsCountSelf - $diffPurchasable;
		$cat = $this;
		while ($cat) {
			$cat->setEnabledProductsCount($cat->getEnabledProductsCount() + $diffEnabled);
			$cat->setPurchasableProductsCount($cat->getPurchasableProductsCount() + $diffPurchasable);
			$cat = $cat->getParent();
		}

		$this->totalProductsCountSelf = $this->getProducts()->count();
	}

	public function getChildren() {
		return $this->children;
	}

	public function getPageTitle(\Angler\StoreBundle\Entity\StrategyCategoryTitle $strategy = null) {
		$title = $this->getHeadingTitle();
		if ($strategy) {
			$strategy->setCategory($this);
			$title = $strategy->getProcessedValue($title);
		}
		return $title;
	}

	public function getMetaDescription(\Angler\StoreBundle\Entity\StrategyCategoryDescription $strategy = null) {
		$description = parent::getMetaDescription();
		if ($strategy) {
			$strategy->setCategory($this);
			$description = $strategy->getProcessedValue($description);
		}
		return $description;
	}

	public function getBreadcrumb($clue = " / ") {
		if ($this->getParent()) {
			return (string)$this->getParent()->getBreadcrumb($clue) . $clue . $this->getTitle();
		} else {
			return $this->getTitle();
		}
	}

	/*
	public function addAdditionalKeyword($text, $locale) {
		$keyword = new \Angler\StoreBundle\Entity\KeywordAdditional;
		$keyword->setLocale($locale);
		$keyword->setText($text);
		$keyword->setCategory($this);
		$this->keywordsAdditional->add($keyword);
	}

	public function addFocusedKeyword($text, $locale) {
		$keyword = new \Angler\StoreBundle\Entity\KeywordFocused;
		$keyword->setLocale($locale);
		$keyword->setText($text);
		$keyword->setCategory($this);
		$this->keywordsFocused->add($keyword);
	}
	*/

	/**
	 * @ORM\preFlush
	 */
	public function removeKeywordsDuplicates() {
		$keywordsByLocale = array();
		$keywordCollection = new ArrayCollection();
		foreach($this->getFocusedKeywords() as $keyword) {
			if (!in_array($keyword->getLocale(), $keywordsByLocale) && $keyword->getText()) {
				$keywordsByLocale[] = $keyword->getLocale();
				$keywordCollection->add($keyword);
			}
		}
		$this->setFocusedKeywords($keywordCollection);

		$keywordsByLocale = array();
		$keywordCollection = new ArrayCollection();
		foreach($this->getAdditionalKeywords() as $keyword) {
			$locale = $keyword->getLocale();
			$text = $keyword->getText();
			if ((!isset($keywordsByLocale[$locale]) || !in_array($text, $keywordsByLocale[$locale])) && $keyword->getText()) {
				$keywordsByLocale[$locale][] = $text;
				$keywordCollection->add($keyword);
			}
		}
		$this->setAdditionalKeywords($keywordCollection);
	}

	public function getFETranslations() {
		$em = \Context::getInstance()->getEm();
		$FELocales = \Context::getInstance()->getFELanguages();
		$listener = \Context::getInstance()->getTranslationListener();
		/** @var $repository \Gedmo\Translatable\Entity\Repository\TranslationRepository */
		$repository = $em->getRepository('\Gedmo\Translatable\Entity\Translation');

		$result = array();
		$translations = $repository->findTranslations($this);

		/** @var $locale Language */
		foreach ($FELocales as $locale) {
			if($locale->getCode() == $listener->getListenerLocale()) continue;
			$result[$locale->getCode()] = (isset($translations[$locale->getCode()])) ? $translations[$locale->getCode()] : '';
		}

		return $result;
	}

	/**
	 * @todo Rename to getScalarBreadcrumb?
	 * @return array
	 */
	public function getPathAsArray() { // FIXME cover with tests
		$path = array($this->getId());
		if ($this->getParent()) {
			$path = array_merge($this->getParent()->getPathAsArray(), $path);
		}
		return $path;
	}

	public function getRootParentId() { // FIXME cover with tests
		$id = array_shift($this->getPathAsArray() );
		return $id == 0 ? null : $id;
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getEnabledChildren() { // FIXME cover with tests
		return $this->getChildren()->filter(
			function($category) {
				/** @var $category \Angler\StoreBundle\Entity\Category */
				return $category->getIsEnabled();
			}
		);
	}

	/**
	 * @param $keyword null|\Angler\StoreBundle\Entity\KeywordFocused
	 */
	public function removeFocusedKeyword(\Angler\StoreBundle\Entity\KeywordFocused $keyword) {
		$this->getFocusedKeywords()->removeElement($keyword);
	}

	/**
	 * @param $keyword null|\Angler\StoreBundle\Entity\KeywordAdditional
	 */
	public function removeAdditionalKeyword(\Angler\StoreBundle\Entity\KeywordAdditional $keyword) {
		$this->getAdditionalKeywords()->removeElement($keyword);
	}

	/**
	 * @param $keyword \Angler\StoreBundle\Entity\KeywordFocused
	 */
	public function addFocusedKeyword(\Angler\StoreBundle\Entity\KeywordFocused $keyword) {
		foreach($this->getFocusedKeywords() as $word) {
			if ($keyword->getLocale() == $word->getLocale()) {
				return;
			}
		}
		$keyword->setCategory($this);
		$this->getFocusedKeywords()->add($keyword);
	}

	/**
	 * @param $keyword \Angler\StoreBundle\Entity\KeywordAdditional
	 */
	public function addAdditionalKeyword(\Angler\StoreBundle\Entity\KeywordAdditional $keyword) {
		foreach($this->getAdditionalKeywords() as $word) {
			if ($keyword->getLocale() == $word->getLocale()) {
				return;
			}
		}
		$keyword->setCategory($this);
		$this->getAdditionalKeywords()->add($keyword);
	}

	/**
	 * @param string $locale
	 * @return null|\Angler\StoreBundle\Entity\KeywordAdditional[]
	 */
	public function getAdditionalKeywordsByLocale($locale) {
		return $this->getAdditionalKeywords()->filter(function(\Angler\StoreBundle\Entity\KeywordAdditional $keyword) use ($locale) {
			return ($keyword->getLocale() == $locale);
		});
	}

	/**
	 * @param string $locale
	 * @return null|\Angler\StoreBundle\Entity\KeywordFocused[]
	 */
	public function getFocusedKeywordByLocale($locale) {
		return isset($this->keywordsFocused[$locale]) ? $this->keywordsFocused[$locale] : null;
	}

	/**
	 * @param string $text
	 * @param string $locale
	 * @return null|\Angler\StoreBundle\Entity\KeywordAdditional
	 */
	public function getAdditionalKeywordByTextAndLocale($text, $locale) {
		$result = null;
		foreach ($this->getAdditionalKeywordsByLocale($locale) as $keyword) {
			if ($keyword->getText() == $text) {
				$result = $keyword;
				break;
			}
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @param string $locale
	 * @return null|\Angler\StoreBundle\Entity\KeywordAdditional
	 */
	public function removeAdditionalKeywordByTextAndLocale($text, $locale) {
		$result = null;
		foreach ($this->getAdditionalKeywordsByLocale($locale) as $keyword) {
			if ($keyword->getText() == $text) {
				$result = $keyword;
				break;
			}
		}
		return $result;
	}

	/**
	 * @param string $locale
	 */
	public function removeFocusedKeywordByLocale($locale) {
		$keyword = $this->getFocusedKeywordByLocale($locale);
		$this->removeFocusedKeyword($keyword);
	}
}
