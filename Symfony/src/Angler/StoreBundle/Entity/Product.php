<?php

namespace Angler\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use \Openbizbox\CoreBundle as Core;

/**
 * Product
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="Angler\StoreBundle\Repository\ProductRepository")
 */
class Product extends Base\Product {

	/**
	 * @var boolean
	 */
	protected $isDisplayNonPurchasable = false;

	protected $taxRate = 0;

	/**
	 * @Assert\All({ * @Assert\File(maxSize="6000000")* })
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile[]
	 */
	protected $filesToSave = null;

	/**
	 * @Assert\All({ * @Assert\File(maxSize="6000000")* })
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile[]
	 */
	protected $imagesToSave = null;


	public function __construct() {
		parent::__construct();
		$this->setIsDisplayNonPurchasable(\Context::getInstance()->getSettings()->offsetGet('DISPLAY_NON_PURCHASABLE'));
	}

	public function __toString() {
		return $this->getTitle();
	}

	/**
	 * Add article to product
	 *
	 * @param \Openbizbox\CoreBundle\Entity\ProductArticle $article
	 * @return void
	 */
	public function addArticle(\Openbizbox\CoreBundle\Entity\ProductArticle $article) {
		$article->setProduct($this); // synchronously updating inverse side
		foreach ($article->getProductArticleAttributeValues() as $attributeValue) {
			$attribute = $attributeValue->getAttribute();
			if (!$this->getAttributes()->contains($attribute)) {
				$this->getAttributes()->add($attribute);
			}
		}
		$this->getArticles()->add($article);
	}

	public function removeArticle(\Openbizbox\CoreBundle\Entity\ProductArticle $article) { // FIXME: add attributes recalculation
		$this->getArticles()->removeElement($article);
	}

	public function setTaxRates($taxes = array()) {
		if ($taxes && $this->getTaxClass()) {
			foreach($taxes as $taxClassId => $taxRate) {
				if ($taxClassId == $this->getTaxClass()->getId()) {
					$this->setTaxRate($taxRate);
					break;
				}
			}
		}
	}

	private function setTaxRate($taxRate) {
		$this->taxRate = $taxRate;
	}

	public function getTaxRate() {
		return $this->taxRate;
	}

	public function getTaxMultiplier() {
		return $this->taxRate + 1;
	}

	public function getBasePrice($qty = 1) {
		$prices = array((float)$this->getRawPrice());
		if ($qty > 1 && $tierPrices = $this->getTierPrices()) {
			$reverse = array_reverse($tierPrices->getValues());
			foreach ($reverse as $tierPrice) {
				/** @var $tierPrice \Openbizbox\CoreBundle\Entity\ProductTierPrice */
				if ($tierPrice->getQty() <= $qty)
					$prices[] = (float)$tierPrice->getPrice();
			}
		}

		return min($prices);
	}

	/**
	 * Minimal price before any discounts - short alias for templates
	 * @return float
	 */
	public function getFormerPrice() {
		return $this->getCachedFormerPrice();
	}

	/**
	 * Minimal price - short alias for templates
	 * @param null|\Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup
	 * @return float
	 */
	public function getPrice(\Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup = null) {
		$price = $this->getCachedMinPrice();
		if ($customerGroup) {
			foreach($this->getIndexedPrices() as $priceIndexed) {
				if ($priceIndexed->getGroup()->getId() == $customerGroup->getId()) {
					$price = $priceIndexed->getMinPrice();
					break;
				}
			}
		}
		return $price;
	}

	/**
	 * Maximum price - short alias for templates
	 * @param null|\Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup
	 * @return float
	 */
	public function getMaxPrice(\Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup = null) {
		$price = $this->getCachedMaxPrice();
		if ($customerGroup) {
			foreach($this->getIndexedPrices() as $priceIndexed) {
				if ($priceIndexed->getGroup()->getId() == $customerGroup->getId()) {
					$price = $priceIndexed->getMaxPrice();
					break;
				}
			}
		}
		return $price;
	}

	public function getMaxDiscount(\Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup = null) {
		$discount = $this->maxDiscount;
		if ($customerGroup) {
			foreach($this->getIndexedPrices() as $priceIndexed) {
				if ($priceIndexed->getGroup()->getId() == $customerGroup->getId()) {
					$discount = $priceIndexed->getMaxDiscount();
					break;
				}
			}
		}
		return $discount;
	}

	/**
	 * Calculate price of first article
	 * @param $qty
	 * @param \Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup
	 * @return float
	 */
	public function getMainArticlePrice($qty = 1, \Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup = null) {
		return $this->getMainArticle()->getPrice($qty, $customerGroup);
	}

	public function setPrice($value, $qty = 1) {
		if ($qty < 0) {
			throw new \OBBException("Quantity must be positive for setPrice, $qty given");
		}
		if ($qty == 1) {
			$this->setRawPrice($value);
		} else {
			$tierPrice = new \Openbizbox\CoreBundle\Entity\ProductTierPrice();
			$tierPrice->setQty($qty);
			$tierPrice->setPrice($value);
			$this->addTierPrice($tierPrice);
		}
	}

	private function addPriceIndex($minPrice, $maxPrice, $maxDiscount, \Openbizbox\CoreBundle\Entity\CustomerGroup $customerGroup) {
		$found = false;
		$priceIndexed = null;
		foreach ($this->getIndexedPrices() as $priceIndexed) {
			if ($priceIndexed->getGroup()->getId() == $customerGroup->getId()) {
				$priceIndexed->setMinPrice($minPrice);
				$priceIndexed->setMaxPrice($maxPrice);
				$priceIndexed->setMaxDiscount($maxDiscount);
				$found = true;
				break;
			}
		}
		if (!$found) {
			$priceIndexed = new ProductPriceIndex();
			$priceIndexed->setGroup($customerGroup);
			$priceIndexed->setMinPrice($minPrice);
			$priceIndexed->setMaxPrice($maxPrice);
			$priceIndexed->setMaxDiscount($maxDiscount);
			$priceIndexed->setProduct($this);
			$this->getIndexedPrices()->add($priceIndexed);
		}
		return $priceIndexed;
	}

	/**
	 * See http://www.doctrine-project.org/docs/orm/2.0/en/cookbook/implementing-wakeup-or-clone.html
	 */
	public function __clone() {
		// If the entity has an identity, proceed as normal.
		if ($this->id) {
			$this->setCreatedAt(null); // 'null' resets to now() in fact
			$this->setModifiedAt(null); //'null' resets to now() in fact

			$this->categories = new \Doctrine\Common\Collections\ArrayCollection();

			// find how many dupplicates there are already
			// we care (suffixing title for) only default language
			$em = \Context::getInstance()->getEm();
			$copyText = _("Copy");
			$originalTitle = preg_replace("~\s\($copyText [0-9]+\)$~", "", $this->getTitle());
			$regex = addslashes(preg_quote($originalTitle)) . ' \\\\(' . $copyText . ' [0-9]+\\\\)$';
			$rsm = new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addScalarResult("title", "title");
			$query = $em->createNativeQuery("SELECT title FROM _obb_product WHERE title REGEXP '" . $regex . "'", $rsm);
			$titles = $query->getArrayResult();
			// Find the maximum X number from existing (Copy X) in titles
			$maxNumber = 0;
			foreach ($titles as $item) {
				$title = $item["title"];
				if (preg_match("~(\d+)\)$~", $title, $matches)) {
					$maxNumber = max($maxNumber, (int)$matches[1]);
				}
			}
			// make a new suffix (Copy X+1) in title
			$this->setTitle(sprintf("%s ($copyText %d)", $originalTitle, $maxNumber + 1));

			// Duplicate all articles
			$articles = $this->getArticles();
			foreach ($articles as $article) {
				$this->addArticle(clone $article);
			}

			// Duplicate all translations
			$translations = $this->getTranslations();
			foreach ($translations as $translation) {
				$this->addCloneTranslation(clone $translation);
			}

			$this->id = null;
		}
		// otherwise do nothing, do NOT throw an exception!
	}

	/**
	 * @param null|Category $targetCategory
	 * @return \Openbizbox\CoreBundle\Entity\Product
	 */
	public function duplicate(\Openbizbox\CoreBundle\Entity\Category $targetCategory = null) {
		$clone = clone $this;
		if ($targetCategory) {
			$clone->addCategory($targetCategory);
		}
		return $clone;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Openbizbox\CoreBundle\Entity\PriceRule[]
	 */
	public function getPriceRulesAsPartOfThisCategory() {
		$rules = new \Doctrine\Common\Collections\ArrayCollection;
		foreach ($this->getCategories() as $category) {
			foreach ($category->getPriceRules() as $rule) {
				if (!$rules->contains($rule)) $rules->add($rule);
			}
		}
		return $rules;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Openbizbox\CoreBundle\Entity\PriceRule[]
	 */
	public function getPriceRulesAsPartOfParentCategories() {
		$rules = new \Doctrine\Common\Collections\ArrayCollection;
		foreach ($this->getCategories() as $category) {
			foreach ($category->getParentPriceRules() as $rule) {
				if (!$rules->contains($rule)) $rules->add($rule);
			}
		}
		return $rules;
	}


	public function getPageTitle(\Openbizbox\CoreBundle\Entity\StrategyProductTitle $strategy = null) {
		$title = parent::getPageTitle();
		if ($strategy) {
			$strategy->setProduct($this);
			$title = $strategy->getProcessedValue($title);
		}
		return $title;
	}

	public function getPageMetaDescription(\Openbizbox\CoreBundle\Entity\StrategyProductDescription $strategy = null) {
		$description = parent::getPageMetaDescription();
		if ($strategy) {
			$strategy->setProduct($this);
			$description = $strategy->getProcessedValue($description);
		}
		return $description;
	}

	public function updatePricesAndAggregates() {
		foreach($this->getArticles() as $article) {
			$article->updatePrices();
			$article->updateDependentBundles();
		}
		foreach($this->getBundles() as $bundle) {
			$bundle->updatePrices();
		}
		$this->aggregateArticles();
	}

	public function getWeight() {
		$weight = null;
		if (!$this->getHasVariants()) {
			/** @var $article \Openbizbox\CoreBundle\Entity\ProductArticle */
			if ($article = $this->getArticles()->current())
				$weight = $article->getWeight();
		}

		return $weight;
	}

	private function aggregateArticles() {
		$activeArticles =
		$totalQty =
		$orderedEver =
		$ordered = 0;
		$foundAtLeastOne = false;
		$minPrices = $maxPrices = $maxDiscount = array();
		$priceIndexesActual = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->getArticles() as $article) {
			if ($article->isActive()) {
				$this->setCachedMinPrice($foundAtLeastOne ? min($this->getCachedMinPrice(), $article->getCachedPrice()) : $article->getCachedPrice());
			 	$this->setCachedMaxPrice($foundAtLeastOne ? max($this->getCachedMaxPrice(), $article->getCachedPrice()) : $article->getCachedPrice());
				$this->setCachedFormerPrice($foundAtLeastOne ? min($this->getCachedFormerPrice(), $article->getCachedFormerPrice()) : $article->getCachedFormerPrice());

				$this->setMaxDiscount(max($this->getMaxDiscount(), $article->getCachedFormerPrice() - $article->getCachedPrice()));

				$this->setMinQuantity($foundAtLeastOne ? min($this->getMinQuantity(), $article->getQuantity()) : $article->getQuantity());
				$this->setMaxQuantity($foundAtLeastOne ? max($this->getMaxQuantity(), $article->getQuantity()) : $article->getQuantity());
				$totalQty += $article->getQuantity() > 0 ? $article->getQuantity() : 0;

				$this->setMinWeight($foundAtLeastOne ? min($this->getMinWeight(), $article->getWeight()) : $article->getWeight());
				$this->setMaxWeight($foundAtLeastOne ? max($this->getMaxWeight(), $article->getWeight()) : $article->getWeight());

				$foundAtLeastOne = true;
				$activeArticles++;

				foreach ($article->getIndexedPrices() as $priceIndex) {
					$index = $priceIndex->getGroup()->getId();
					$maxDiscount[$index] = isset($maxDiscount[$index]) ? $maxDiscount[$index] : 0;
					$maxDiscount[$index] = max($maxDiscount[$index], $article->getCachedFormerPrice() - $priceIndex->getPrice());
					if (!isset($minPrices[$index]) || $priceIndex->getPrice() < $minPrices[$index]->getPrice()) {
						$minPrices[$index] = $priceIndex;
					}
					if (!isset($maxPrices[$index]) || $priceIndex->getPrice() > $maxPrices[$index]->getPrice()) {
						$maxPrices[$index] = $priceIndex;
					}
				}
			}

			$ordered     += $article->getOrdered();
			$orderedEver += $article->getOrderedEver();
		}

		if ($minPrices) foreach($minPrices as $index => $priceIndex) {
			$priceIndexesActual->add($this->addPriceIndex($priceIndex->getPrice(), $maxPrices[$index]->getPrice(), $maxDiscount[$index], $priceIndex->getGroup()));
		}

		// cleanup non-used indexed prices
		foreach($this->getIndexedPrices() as $indexPrice) {
			if (!$priceIndexesActual->contains($indexPrice)) {
				$this->getIndexedPrices()->removeElement($indexPrice);
			}
		}

		// if there's at least one Attribute - that's a product with variants
		$this->setHasVariants((bool)count($this->getAttributes()));
		$this->setTotalArticles($this->getArticles()->count());
		$this->setActiveArticles($activeArticles);
		$this->setIsPurchasable($this->getActiveArticles() > 0);
		$this->setTotalQuantity($totalQty);
		$this->setTotalOrdered($ordered);
		$this->setTotalOrderedEver($orderedEver);
	}

	/**
	 * @return boolean
	 */
	public function getIsDisplayNonPurchasable() {
		return $this->isDisplayNonPurchasable;
	}

	/**
	 * @param boolean $isDisplayNonPurchasable
	 * @return void
	 */
	public function setIsDisplayNonPurchasable($isDisplayNonPurchasable) {
		$this->isDisplayNonPurchasable = $isDisplayNonPurchasable;
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->getIsEnabled() && ($this->getIsPurchasable() || $this->getIsDisplayNonPurchasable());
	}

	/**
	 * Clean articles from values of non-used attributes
	 * @ORM\preFlush
	 * @return void
	 */
	public function cleanAttributes() {
		$attributes = $this->getAttributes();

		// if there's no attributes assigned:
		// - remove all variants
		// - make main article has no attribute values
		if (!count($attributes)) {
			// remove relations to any attribute value for the first article
			if ($mainArticle = $this->getArticles()->first()) {
				/** @var $mainArticle Productarticle */
				$mainArticle->setProductArticleAttributeValues(array());
			}
			// Remove all other articles except the first one
			foreach ($this->getArticles() as $article) {
				if ($article !== $mainArticle) {
					$this->getArticles()->removeElement($article);
				}
			}
		}
		else {
			foreach ($this->getArticles() as $article) {
				$validRelations = new ArrayCollection();
				/** @var $relation \Openbizbox\CoreBundle\Entity\ProductArticleAttributeValue */
				foreach ($article->getProductArticleAttributeValues() as $key => $relation) {
					if ($attributes->contains($relation->getAttribute())) {
						$validRelations->add($relation);
					}
				}
				$article->setProductArticleAttributeValues($validRelations);
			}
		}
	}


	public function updateFlags() {
		$this->setIsPurchasable($this->getActiveArticles() > 0);
	}

	public function getBaseURL() {
		if ($frozen = $this->getFrozenUrl()) {
			$url = $frozen;
		} else {
			$url = $this->createCanonicalURL();
		}

		return basename((string)$url);
	}

	public function createCanonicalURL($langCode = false) {
		if (!$langCode) {
			$title = $this->getTitle();
		}
		else {
			$localizations = $this->getTranslations();
			$title = "";
			foreach ($localizations as $code => $data) {
				if ($code == $langCode) {
					$title = $data['title'];
					break;
				}
			}
			$title = $title ? $title : $this->getTitle();
		}
		require_once(DIR_FS_MODULES . "/url_rewrite.php");
		return \Openbizbox\CoreBundle\Helpers\URLHelper::encodeTitle($title);
	}



	/**
	 * @return ProductArticle
	 */
	public function getMainArticle() {
		$mainArticle = $this->getArticles()->first();
		if (!$mainArticle) {
			$mainArticle = new ProductArticle();
			$this->addArticle($mainArticle);
		}
		return $mainArticle;
	}

	public function setMainArticle($article) {
		$this->getArticles()->add($article);
	}

	/**
	 *
	 * @param \Openbizbox\CoreBundle\Entity\Product $alsoPurchasedProduct
	 * @deprecated FIXME we don't need this method (no need to run it inside checkuot, no need to cleanup old records
	 *             here, etc). To simplify - we need to recalculate that table each night in cron script.
	 *             That cron will also be aware of MIN_DISPLAY_ALSO_PURCHASED, ALSO_PURCHASED_ORDER_BY, and will NOT
	 *             apply "limit 100" as it was doing before. Should process only last 90 days orders.
	 */
	public function addAlsoPurchasedProduct(\Openbizbox\CoreBundle\Entity\Product $alsoPurchasedProduct) {
		$relationTo = null;
		$relations = $this->getProductToAlsoPurchasedProduct();

		// First add relation to current product
		/** @var $relation \Openbizbox\CoreBundle\Entity\ProductToAlsoPurchasedProduct */
		foreach ($relations as $relation) {
			if ($relation->getProduct()->getId() == $this->getId() && $relation->getAlsoPurchasedProduct()->getId() == $alsoPurchasedProduct->getId()) {
				$relationTo = $relation;
				break;
			}
		}
		if (!$relationTo) {
			$relationTo = new \Openbizbox\CoreBundle\Entity\ProductToAlsoPurchasedProduct();
			$relationTo->setProduct($this);
			$relationTo->setAlsoPurchasedProduct($alsoPurchasedProduct);
			$this->getProductToAlsoPurchasedProduct()->add($relationTo);
		}

		// Then add relation to a also purchased product
		$relationFrom = null;
		$relations = $alsoPurchasedProduct->getProductToAlsoPurchasedProduct();
		foreach ($relations as $relation) {
			if ($relation->getProduct()->getId() == $alsoPurchasedProduct->getId() && $relation->getAlsoPurchasedProduct()->getId() == $this->getId()) {
				$relationFrom = $relation;
				break;
			}
		}
		if (!$relationFrom) {
			$relationFrom = new \Openbizbox\CoreBundle\Entity\ProductToAlsoPurchasedProduct();
			$relationFrom->setProduct($alsoPurchasedProduct);
			$relationFrom->setAlsoPurchasedProduct($this);
			$alsoPurchasedProduct->getProductToAlsoPurchasedProduct()->add($relationFrom);
		}
		$relationTo->setOrdersCount($relationTo->getOrdersCount() + 1);
		$relationFrom->setOrdersCount($relationFrom->getOrdersCount() + 1);
	}

	/**
	 * Add file to product
	 *
	 * @param \Openbizbox\CoreBundle\Entity\ProductFile $file
	 * @return void
	 */
	public function addFile(\Openbizbox\CoreBundle\Entity\ProductFile $file) {
		$file->setProduct($this); // synchronously updating inverse side
		$this->getFiles()->add($file);
	}

	/**
	 * Remove file from the product
	 *
	 * @param \Openbizbox\CoreBundle\Entity\ProductFile $file
	 * @return void
	 */
	public function removeFile(\Openbizbox\CoreBundle\Entity\ProductFile $file) {
		$this->getFiles()->removeElement($file);
	}

	/**
	 * @param \Openbizbox\CoreBundle\Entity\Category $mainCategory
	 */
	public function setMainCategory($mainCategory) {
		$this->mainCategory = $mainCategory;
		$this->addCategory($mainCategory);
	}

	/**
	 * Add image to product
	 *
	 * @param \Openbizbox\CoreBundle\Entity\ProductImage $image
	 * @return void
	 */
	public function addImage(\Openbizbox\CoreBundle\Entity\ProductImage $image) {
		$image->setProduct($this); // synchronously updating inverse side
		$this->getImages()->add($image);
	}

	/**
	 * Remove image from the product
	 *
	 * @param \Openbizbox\CoreBundle\Entity\ProductImage $image
	 * @return void
	 */
	public function removeImage(\Openbizbox\CoreBundle\Entity\ProductImage $image) {
		$this->getImages()->removeElement($image);
	}

	/**
	 * Add Category to products categories collection
	 *
	 * @param \Openbizbox\CoreBundle\Entity\Category $category
	 * @return void
	 */
	public function addCategory(\Openbizbox\CoreBundle\Entity\Category $category) {
		if (!$this->getCategories()->exists(function($key, $cat) use ($category) {
			return $cat === $category;
		})
		) {
			$category->getProducts()->add($this);
			$category->updateProductsCountSelf();
			$this->getCategories()->add($category);
		}
	}

	public function removeCategory(\Openbizbox\CoreBundle\Entity\Category $category) {
		if ($this->getCategories()->exists(function($key, $cat) use ($category) {
			return $cat === $category;
		})
		) {
			$this->getCategories()->removeElement($category);
			$category->getProducts()->removeElement($this);
			$category->updateProductsCountSelf();
		}
	}

	public function addReview($customer, $rating, $locale, $text = '', $isEnabled = false) {
		$review = new \Openbizbox\CoreBundle\Entity\ProductReview();
		$review->setProduct($this);
		$review->setLocale($locale);
		$review->setRating($rating);
		$review->setCustomer($customer);
		$review->setText($text);
		$review->setIsEnabled($isEnabled);
		if (!$this->getReviews()->contains($review)) {
			$this->getReviews()->add($review);
		}
	}

	public function addCrossSell(\Openbizbox\CoreBundle\Entity\Product $product, $sorting = 0) {
		foreach ($this->getProductToCrossSells() as $productToCrossSell) {
			if ($productToCrossSell->getCrossSellProduct()->getId() == $product->getId()) {
				return;
			}
		}
		$productToCrossSell = new \Openbizbox\CoreBundle\Entity\ProductToCrossSell;
		$this->getProductToCrossSells()->add($productToCrossSell);
		$productToCrossSell->setProduct($this);
		$productToCrossSell->setCrossSellProduct($product);
		$productToCrossSell->setSorting($sorting);
	}

	public function addPlusSell(\Openbizbox\CoreBundle\Entity\Product $product, $sorting = 0) {
		foreach ($this->getProductToPlusSells() as $productToPlusSell) {
			if ($productToPlusSell->getPlusSell()->getId() == $product->getId()) {
				return;
			}
		}
		$productToPlusSell = new \Openbizbox\CoreBundle\Entity\ProductToPlusSell;
		$this->getProductToPlusSells()->add($productToPlusSell);
		$productToPlusSell->setProduct($this);
		$productToPlusSell->setPlusSell($product);
		$productToPlusSell->setSorting($sorting);
	}

	public function addBundle(array $articles, $price = 0) {
		$bundle = new \Openbizbox\CoreBundle\Entity\ProductArticleBundle;
		$bundle->setProduct($this);
		$bundle->setMainArticle($this->getArticles()->first());
		foreach ($articles as $article) {
			// TODO: check if product has variants
			$bundle->addArticle($article);
		}
		$bundle->setRawPrice($price);
		$this->getBundles()->add($bundle);
	}

	public function removeBundle(\Openbizbox\CoreBundle\Entity\ProductArticleBundle $bundle) {
		$this->getBundles()->removeElement($bundle);
	}

	/**
	 * @param \Openbizbox\CoreBundle\Entity\ProductTierPrice $tierPrice

	 * @internal param $ \Openbizbox\CoreBundle\Entity\ProductTierPrice
	 */
	public function addTierPrice(\Openbizbox\CoreBundle\Entity\ProductTierPrice $tierPrice) {
		$tierPrice->setProduct($this);
		$this->getTierPrices()->add($tierPrice);
	}

	public function setSpecialPrice($discount, \DateTime $dateStart = null, \DateTime $dateEnd = null) {
		if (!$special = $this->getSpecialRule()) {
			$special = new \Openbizbox\CoreBundle\Entity\ProductSpecial;
		}
		else {
			$special->setPreviousDateStart($special->getDateStart());
			$special->setPreviousDateEnd($special->getDateEnd());
		}
		$special->setProduct($this);
		$special->setDiscount($discount);
		$special->setDateStart($dateStart);
		$special->setDateEnd($dateEnd);
		$this->setSpecialRule($special);
	}

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

	public static function listVariantTemplates() {
		return array(
				'full_with_stock'=> _('Full with stock'),
				'radio'          => _('Radio'),
				'drop_down'      => _('Drop down'),
				'main'           => _('Main template'),
			);
	}

	/** @deprecated it's here to replace old $product->getImage()->getThumbnail() but must be elsewhere */
	public function getImageThumbnail($width, $height) {
		$file = DIR_WS_IMAGES . $this->getImage();
		if (file_exists($file) && is_file($file)) {
			return resized_image($file, $width, $height);
		}
		return "";
	}

	public function isValidRequest(\PageRequestParser $parser, $defaultLocale, $isShortUrl = true) {
		// Obvious validation against title
		if ($parser->getProductTitle($isShortUrl) != $this->getBaseURL())
			return false;
		// If language in request == default language, no go
		if ($parser->getLanguage() == $defaultLocale)
			return false;
		if ($parser->needsCategoryPath() || ($parser->getMode() == \PageRequestParser::MODE_UNKNOWN)) {
			// that last bit about MODE_UNKNOWN is to deem all products specified without categories
			// as invalid - if they don't have a category of course.
			$requestPath = $parser->getCategoryPathAsString();
			return $this->isValidCategoryPath($requestPath, $defaultLocale, $isShortUrl);
		}

		return true;
	}

	private function isValidCategoryPath($requestPath, $defaultLocale, $isShortUrl = true) {
		$cats = $this->getActiveCategories();
		if (count($cats) == 0)
			return true;
		foreach ($cats as $cat) {
			$category_path = linkto_category($cat, $defaultLocale, false, $isShortUrl);
			if (rtrim($category_path, '/') == rtrim($requestPath, '/'))
				return true;
		}
		return false;
	}

	public function setFilesToSave($filesToSave) {
		if ($filesToSave) {
			foreach($filesToSave as $file) {
				if(null === $file) continue;
				$entity = new ProductFile();
				$entity->setProduct($this);
				$entity->setFileToSave($file);
				$entity->upload();

				$this->getFiles()->add($entity);
			}
		}

		$this->filesToSave = null;
	}

	public function getFilesToSave() {
		return $this->filesToSave;
	}

	public function setImagesToSave($imagesToSave) {
		if ($imagesToSave) {
			foreach($imagesToSave as $image) {
				if(null === $image) continue;
				$entity = new ProductImage();
				$entity->setProduct($this);
				$entity->setImageToSave($image);
				$entity->upload();

				$this->getImages()->add($entity);
			}
		}
		$this->imagesToSave = null;
	}

	public function getImagesToSave() {
		return $this->imagesToSave;
	}

	/**
	 * @param $keyword \Openbizbox\CoreBundle\Entity\KeywordFocused
	 */
	public function addFocusedKeyword(\Openbizbox\CoreBundle\Entity\KeywordFocused $keyword) {
		foreach($this->getFocusedKeywords() as $word) {
			if ($keyword->getLocale() == $word->getLocale()) {
				return;
			}
		}
		$keyword->setProduct($this);
		$this->getFocusedKeywords()->add($keyword);
	}

	/**
	 * @param $keyword \Openbizbox\CoreBundle\Entity\KeywordAdditional
	 */
	public function addAdditionalKeyword(\Openbizbox\CoreBundle\Entity\KeywordAdditional $keyword) {
		foreach($this->getAdditionalKeywords() as $word) {
			if ($keyword->getLocale() == $word->getLocale()) {
				return;
			}
		}
		$keyword->setProduct($this);
		$this->getAdditionalKeywords()->add($keyword);
	}

	/**
	 * @param $keyword null | \Openbizbox\CoreBundle\Entity\KeywordFocused
	 */
	public function removeFocusedKeyword(\Openbizbox\CoreBundle\Entity\KeywordFocused $keyword) {
		if(null === $keyword) return;
		$this->getFocusedKeywords()->removeElement($keyword);
	}

	/**
	 * @param $keyword \Openbizbox\CoreBundle\Entity\KeywordAdditional
	 */
	public function removeAdditionalKeyword(\Openbizbox\CoreBundle\Entity\KeywordAdditional $keyword) {
		$this->getAdditionalKeywords()->removeElement($keyword);
	}

	/**
	 * @param string $locale
	 * @return null | \Openbizbox\CoreBundle\Entity\KeywordAdditional[]
	 */
	public function getAdditionalKeywordsByLocale($locale) {
		return $this->getAdditionalKeywords()->filter(function(\Openbizbox\CoreBundle\Entity\KeywordAdditional $keyword) use ($locale) {
			return ($keyword->getLocale() == $locale);
		});
	}

	/**
	 * @param string $locale
	 * @return null | \Openbizbox\CoreBundle\Entity\KeywordFocused
	 */
	public function getFocusedKeywordByLocale($locale) {
		$result = null;
		foreach ($this->getFocusedKeywords() as $keyword) {
			if ($keyword->getLocale() == $locale) {
				$result = $keyword;
				break;
			}
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @param string $locale
	 * @return null | \Openbizbox\CoreBundle\Entity\KeywordAdditional
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
	 */
	public function removeAdditionalKeywordByTextAndLocale($text, $locale) {
		$keyword = $this->getAdditionalKeywordByTextAndLocale($text, $locale);
		$this->removeAdditionalKeyword($keyword);
	}

	/**
	 * @param string $locale
	 */
	public function removeFocusedKeywordByLocale($locale) {
		$keyword = $this->getFocusedKeywordByLocale($locale);
		$this->removeFocusedKeyword($keyword);
	}
}
