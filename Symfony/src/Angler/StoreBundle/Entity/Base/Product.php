<?php

namespace Angler\StoreBundle\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Angler\StoreBundle as Store;
use Angler\StoreBundle\Model\AnglerTranslatable;
use Angler\StoreBundle\Interfaces\SEOKeywordsInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mapping of product onto table, please keep clean from other logic, only getter/setters of Doctrine fields, nothing
 * else. This is just to keep OUR logic clean from this long and no need to test code.
 *
 * @ORM\MappedSuperclass
 * @Gedmo\TranslationEntity(class="Angler\StoreBundle\Entity\Translations\ProductTranslation")
 */
abstract class Product extends AnglerTranslatable implements SEOKeywordsInterface {

	/**
	 * @ORM\Id
	 * @ORM\Column(name="product_id", type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank
	 */
	protected $title = "";

	/**
	 * @ORM\Column(name="is_purchasable", type="boolean")
	 * @var boolean
	 */
	protected $isPurchasable = true;

	/**
	 * @ORM\Column(name="is_enabled", type="boolean")
	 * @var boolean
	 */
	protected $isEnabled = true;

	/**
	 * @ORM\Column(name="raw_price", type="decimal", scale=4)
	 * @var float
	 */
	protected $rawPrice = 0.0;

	/**
	 * Minimum discounted price (in case when there are variants)
	 * @ORM\Column(name="cached_min_price", type="decimal", scale=4, nullable=true)
	 * @var float
	 */
	protected $cachedMinPrice = null;

	/**
	 * Maximum discounted price (in case when there are variants)
	 * @ORM\Column(name="cached_max_price", type="decimal", scale=4, nullable=true)
	 * @var float
	 */
	protected $cachedMaxPrice = null;

	/**
	 * Minimum price without any discounts (in case when there are variants)
	 * @ORM\Column(name="cached_former_price", type="decimal", scale=4, nullable=true)
	 * @var float
	 */
	protected $cachedFormerPrice = null;

	/**
	 * Maximum discount
	 * @ORM\Column(name="max_discount", type="decimal", scale=2, nullable=false)
	 * @var float
	 */
	protected $maxDiscount = 0;

	/**
	 * @ORM\Column(name="is_hot", type="boolean")
	 * @var boolean
	 */
	protected $isHot = false;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", name="hot_expires_at", nullable=true)
	 */
	protected $hotExpiresAt = null;

	/**
	 * @ORM\Column(name="barcode", type="string", nullable=true)
	 * @var string
	 */
	protected $barcode = '';

	/**
	 * @ORM\Column(name="model", type="string", nullable=true)
	 * @var string
	 */
	protected $model = '';

	/**
	 * @ORM\Column(name="is_append_model", type="boolean")
	 * @var boolean
	 */
	protected $isAppendModel = false;

	/**
	 * @ORM\Column(name="image", type="string")
	 * @var string
	 */
	protected $image = '';

	/**
	 * @ORM\Column(name="brand_model", type="string", nullable=true)
	 * @var string
	 */
	protected $brandModel = '';

	/**
	 * @ORM\Column(name="total_quantity", type="integer")
	 * @var integer
	 */
	protected $totalQuantity = 0;

	/**
	 * @ORM\Column(name="raw_price_cost", type="decimal", scale=4)
	 * @var float
	 */
	protected $rawPriceCost = 0.0;

	/**
	 * @ORM\Column(name="rating", type="decimal", scale=4)
	 * @var float
	 */
	protected $rating = 0.0;

	/**
	 * @ORM\Column(name="is_call_for_price", type="boolean")
	 * @var boolean
	 */
	protected $isCallForPrice = false;

	/**
	 * @ORM\Column(name="date_available", type="datetime", nullable=true)
	 * @var \DateTime
	 */
	protected $dateAvailable;

	/**
	 * @ORM\Column(name="edbpriser_id", type="integer", nullable=true)
	 * @var string
	 */
	protected $edbpriserId;

	/**
	 * @ORM\Column(name="is_edbpriser_list", type="boolean")
	 * @var boolean
	 */
	protected $isEDBPriserList = false;

	/**
	 * @ORM\Column(name="is_kelkoo", type="boolean")
	 * @var boolean
	 */
	protected $isKelkoo = false;

	/**
	 * @ORM\Column(name="variants_template", type="string")
	 * @var string
	 */
	protected $variantsTemplate = 'main';

	/**
	 * @ORM\Column(name="bundles_template", type="string")
	 * @var string
	 */
	protected $bundlesTemplate = 'main';

	/**
	 * @ORM\Column(name="min_quantity", type="integer")
	 * @var integer
	 */
	protected $minQuantity = 0;

	/**
	 * @ORM\Column(name="max_quantity", type="integer")
	 * @var integer
	 */
	protected $maxQuantity = 0;

	/**
	 * @ORM\Column(name="min_weight", type="decimal", scale=3)
	 * @var float
	 */
	protected $minWeight = 0.0;

	/**
	 * @ORM\Column(name="max_weight", type="decimal", scale=3)
	 * @var float
	 */
	protected $maxWeight = 0.0;

	/**
	 * @ORM\Column(name="total_ordered", type="integer")
	 * @var integer
	 */
	protected $totalOrdered = 0;

	/**
	 * @ORM\Column(name="total_ordered_ever", type="integer")
	 * @var integer
	 */
	protected $totalOrderedEver = 0;

	/**
	 * Sum of viewsCount (product page hits) among all localizations
	 * @ORM\Column(name="total_views_count", type="bigint")
	 * @var bigint
	 */
	protected $totalViewsCount = 0;

	/**
	 * @ORM\Column(name="total_articles", type="integer")
	 * @var integer
	 */
	protected $totalArticles = 0; // FIXME rename into ...Count (to not expect array of Articles)

	/**
	 * @ORM\Column(name="active_articles", type="integer")
	 * @var integer
	 */
	protected $activeArticles = 0; // FIXME rename into ...Count (to not expect array of Articles)

	/**
	 * @ORM\Column(name="has_variants", type="boolean")
	 * @var bool
	 */
	protected $hasVariants = false;
	/**
	 * This field is not actually needed (=1 when sorting is >0)
	 * Added because I don't know how to make Doctrine Query to sort by "sorting>0 DESC, sorting ASC"
	 * @ORM\Column(name="has_sorting", type="integer", nullable=false)
	 * @var integer
	 */
	protected $hasSorting = 0;

	/**
	 * @ORM\Column(name="sorting", type="integer", nullable=true)
	 * @var integer
	 */
	protected $sorting;

	/**
	 * @ORM\Column(name="is_in_active_category", type="boolean")
	 * @var boolean
	 */
	protected $isInActiveCategory = true;

	/**
	 * @ORM\Column(name="is_low_on_stock", type="boolean")
	 * @var boolean
	 */
	protected $isLowOnStock = false;

	/**
	 * @ORM\Column(name="is_meta_noindex", type="boolean")
	 * @var boolean
	 */
	protected $isMetaNoIndex = false;

	/**
	 * @ORM\Column(name="shipping_time", type="string")
	 * @var string
	 */
	protected $shippingTime = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text")
	 */
	protected $summary = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text")
	 */
	protected $description = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(name="page_title", type="text")
	 */
	protected $pageTitle = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(name="page_meta_description", type="text")
	 */
	protected $pageMetaDescription = ""; //FIXME rename to metaDescription

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(name="page_meta_keywords", type="text")
	 */
	protected $pageMetaKeywords = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(name="frozen_url", type="string")
	 */
	protected $frozenUrl = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(name="video", type="string")
	 */
	protected $video = "";

	/**
	 * @var string
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text")
	 */
	protected $homepage = "";

	/**
	 * viewsCount (product page hits) per localization
	 * @var int
	 * @Gedmo\Translatable
	 * @ORM\Column(name="views_count", type="bigint")
	 */
	protected $viewsCount = "";

	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime", name="created_at", nullable=false)
	 */
	protected $createdAt;

	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(type="datetime", name="modified_at", nullable=false)
	 */
	protected $modifiedAt;

	/** ----------------------- Associations ----------------------- */

	/**
	 * @var \Angler\CoreBundle\Entity\ProductSpecial
	 * @ORM\OneToOne(targetEntity="\Angler\CoreBundle\Entity\ProductSpecial", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
	 */
	protected $specialRule;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="ProductArticle", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $articles;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductArticleBundle", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $bundles;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\CoreBundle\Entity\TaxClass")
	 * @ORM\JoinColumn(name="tax_class_id", referencedColumnName="tax_class_id", onDelete="SET NULL")
	 * @var \Angler\CoreBundle\Entity\TaxClass
	 */
	protected $taxClass;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\CoreBundle\Entity\Supplier")
	 * @ORM\JoinColumn(name="supplier_id", referencedColumnName="supplier_id", onDelete="SET NULL")
	 * @var Supplier
	 */
	protected $supplier;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\CoreBundle\Entity\Brand")
	 * @ORM\JoinColumn(name="brand_id", referencedColumnName="brand_id", onDelete="SET NULL")
	 * @var Brand
	 */
	protected $brand;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="ProductImage", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $images;

	/**
	 * @var \Angler\CoreBundle\Entity\KeywordFocused[] | \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\KeywordFocused", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true, indexBy="locale")
	 */
	protected $keywordsFocused;

	/**
	 * @var \Angler\CoreBundle\Entity\KeywordAdditional[] | \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\KeywordAdditional", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $keywordsAdditional;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection|\Angler\CoreBundle\Entity\Category[]
	 * @ORM\ManyToMany(targetEntity="\Angler\CoreBundle\Entity\Category", inversedBy="products", cascade={"persist"})
	 * @ORM\JoinTable(name="_obb_product_to_category", schema="product_to_category",
	 *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="category_id", onDelete="CASCADE")}
	 * )
	 */
	protected $categories;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\CoreBundle\Entity\Category")
	 * @ORM\JoinColumn(name="main_category_id", referencedColumnName="category_id", onDelete="SET NULL")
	 * @var Category
	 */
	protected $mainCategory;

	/**
	 * @ORM\ManyToMany(targetEntity="\Angler\CoreBundle\Entity\Attribute", inversedBy="products", cascade={"persist"})
	 * @ORM\JoinTable(
	 *     name="_obb_product_to_attribute",
	 *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="attribute_id", referencedColumnName="attribute_id", onDelete="CASCADE")}
	 * )
	 */
	protected $attributes;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductToCrossSell", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @ORM\OrderBy({"sorting" = "ASC"})
	 */
	protected $productToCrossSells;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductToPlusSell", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @ORM\OrderBy({"sorting" = "ASC"})
	 */
	protected $productToPlusSells;

	/**
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductFile", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @ORM\OrderBy({"sorting" = "ASC", "filename" = "DESC"})
	 */
	protected $files;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductTierPrice", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @ORM\OrderBy({"qty" = "ASC"})
	 */
	protected $tierPrices;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductReview", mappedBy="product", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 */
	protected $reviews;

	/**
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\ProductToAlsoPurchasedProduct", mappedBy="product", cascade={"persist"})
	 * FIXME: sort order could be either by orders_count or last purchased date (i.e. last_modified). Anyway seems this
	 * one doesn't work at all:
	 * @ORM\OrderBy({"ordersCount" = "DESC"})
	 */
	protected $productToAlsoPurchasedProduct;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="ProductPriceIndex", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $indexedPrices;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="ProductArticlePriceIndex", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $indexedArticlePrices;

	/**
	 * @var \Angler\CoreBundle\Entity\PriceRule[] | \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\ManyToMany(targetEntity="PriceRule", mappedBy="products", cascade={"persist"})
	 */
	protected $priceRules;

	/**
	 * @var \Angler\CoreBundle\Entity\Translations\ProductTranslation[] | \Doctrine\Common\Collections\ArrayCollection $translations
	 * @ORM\OneToMany(targetEntity="\Angler\CoreBundle\Entity\Translations\ProductTranslation", mappedBy="object", cascade={"persist", "remove"})
	*/
	protected $translations;

	public function __construct() {
		$this->articles                      = new \Doctrine\Common\Collections\ArrayCollection();
		$this->attributes                    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->files                         = new \Doctrine\Common\Collections\ArrayCollection();
		$this->images                        = new \Doctrine\Common\Collections\ArrayCollection();
		$this->categories                    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->keywordsFocused               = new \Doctrine\Common\Collections\ArrayCollection();
		$this->keywordsAdditional            = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tierPrices                    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->productToCrossSells           = new \Doctrine\Common\Collections\ArrayCollection();
		$this->productToPlusSells            = new \Doctrine\Common\Collections\ArrayCollection();
		$this->bundles                       = new \Doctrine\Common\Collections\ArrayCollection();
		$this->reviews                       = new \Doctrine\Common\Collections\ArrayCollection();
		$this->priceRules                    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->indexedPrices                 = new \Doctrine\Common\Collections\ArrayCollection();
		$this->productToAlsoPurchasedProduct = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translations 	  			 = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Get productId
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get product articles
	 *
	 * @return \Angler\CoreBundle\Entity\ProductArticle[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getArticles() {
		return $this->articles;
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $articles
	 */
	public function setArticles($articles) {
		$this->articles = $articles;
	}

	/**
	 * Set isPurchasable
	 *
	 * @param bool $isPurchasable
	 */
	public function setIsPurchasable($isPurchasable) {
		$this->isPurchasable = $isPurchasable;
	}

	/**
	 * Get isPurchasable
	 *
	 * @return bool
	 */
	public function getIsPurchasable() {
		return $this->isPurchasable;
	}

	/**
	 * Set isEnabled
	 *
	 * @param bool $isEnabled
	 */
	public function setIsEnabled($isEnabled) {
		$this->isEnabled = $isEnabled;
	}

	/**
	 * Get isEnabled
	 *
	 * @return bool
	 */
	public function getIsEnabled() {
		return $this->isEnabled;
	}

	/**
	 * Set barcode
	 *
	 * @param string $barcode
	 */
	public function setBarcode($barcode) {
		$this->barcode = $barcode;
	}

	/**
	 * Get barcode
	 *
	 * @return string
	 */
	public function getBarcode() {
		return $this->barcode;
	}

	/**
	 * Set model
	 *
	 * @param string $model
	 */
	public function setModel($model) {
		$this->model = $model;
	}

	/**
	 * Get model
	 *
	 * @return string
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * Set isAppendModel
	 *
	 * @param bool $isAppendModel
	 */
	public function setIsAppendModel($isAppendModel) {
		$this->isAppendModel = $isAppendModel;
	}

	/**
	 * Get appendModel
	 *
	 * @return bool
	 */
	public function getIsAppendModel() {
		return $this->isAppendModel;
	}

	/**
	 * Set image
	 *
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * Get default image
	 * @return string
	 */
	public function getImage() { //FIXME: cover with test, move away from here
		foreach ($this->getImages() as $image) {
			if ($image->getIsDefault()) {
				return $image->getFileName();
			}
		}
		return $this->getImages() ? $this->getImages()->first()->getFileName() : '';
	}

	/**
	 * Get default image
	 * @return null|\Angler\CoreBundle\Entity\ProductImage
	 */
	public function getDefaultImage() { //FIXME: cover with test
		/** @var $image \Angler\CoreBundle\Entity\ProductImage */
		foreach ($this->getImages() as $image) {
			if ($image->getIsDefault()) {
				return $image;
			}
		}

		return count($this->getImages()) ? $this->getImages()->first() : null;
	}

	public function setDefaultImage($defaultImage) { //FIXME: cover with test
		/** @var $image \Angler\CoreBundle\Entity\ProductImage */
		foreach ($this->getImages() as $image) {
			$image->setIsDefault($image === $defaultImage);
		}
	}

	/**
	 * Set brandModel
	 *
	 * @param string $brandModel
	 */
	public function setBrandModel($brandModel) {
		$this->brandModel = $brandModel;
	}

	/**
	 * Get brandModel
	 *
	 * @return string
	 */
	public function getBrandModel() {
		return $this->brandModel;
	}

	/**
	 * @param \Angler\CoreBundle\Entity\Brand $brand
	 */
	public function setBrand(\Angler\CoreBundle\Entity\Brand $brand) {
		$this->brand = $brand;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\Brand
	 */
	public function getBrand() {
		return $this->brand;
	}

	/**
	 * Set quantity
	 *
	 * @param integer $totalQuantity
	 */
	public function setTotalQuantity($totalQuantity) {
		$this->totalQuantity = $totalQuantity;
	}

	/**
	 * Get quantity
	 *
	 * @return integer
	 */
	public function getTotalQuantity() {
		return $this->totalQuantity;
	}

	public function getQuantity() {
		return $this->getTotalQuantity();
	}

	/**
	 * Set rawPrice
	 *
	 * @param float $rawPrice
	 */
	public function setRawPrice($rawPrice) {
		$this->rawPrice = $rawPrice;
	}

	/**
	 * Get rawPrice
	 *
	 * @return float
	 */
	public function getRawPrice() {
		return $this->rawPrice;
	}

	/**
	 * Set rawPriceCost
	 *
	 * @param float $rawPriceCost
	 */
	public function setRawPriceCost($rawPriceCost) {
		$this->rawPriceCost = $rawPriceCost;
	}

	/**
	 * Get rawPriceCost
	 *
	 * @return float
	 */
	public function getRawPriceCost() {
		return $this->rawPriceCost;
	}

	/**
	 * Set isCallForPrice
	 *
	 * @param bool $isCallForPrice
	 */
	public function setIsCallForPrice($isCallForPrice) {
		$this->isCallForPrice = $isCallForPrice;
	}

	/**
	 * Get isCallForPrice
	 *
	 * @return bool
	 */
	public function getIsCallForPrice() {
		return $this->isCallForPrice;
	}

	/**
	 * Set dateAvailable
	 *
	 * @param \DateTime $dateAvailable
	 */
	public function setDateAvailable($dateAvailable) {
		$this->dateAvailable = $dateAvailable;
	}

	/**
	 * Get dateAvailable
	 *
	 * @return \DateTime
	 */
	public function getDateAvailable() {
		return $this->dateAvailable;
	}

	/**
	 * Set edbpriserId
	 *
	 * @param string $edbpriserId
	 */
	public function setEdbpriserId($edbpriserId) {
		$this->edbpriserId = $edbpriserId;
	}

	/**
	 * Get edbpriserId
	 *
	 * @return string
	 */
	public function getEdbpriserId() {
		return $this->edbpriserId;
	}

	/**
	 * Set isEDBPriserList
	 *
	 * @param bool $isEDBPriserList
	 */
	public function setIsEDBPriserList($isEDBPriserList) {
		$this->isEDBPriserList = $isEDBPriserList;
	}

	/**
	 * Get isEDBPriserList
	 *
	 * @return bool
	 */
	public function getIsEDBPriserList() {
		return $this->isEDBPriserList;
	}

	/**
	 * Set isKelkoo
	 *
	 * @param bool $isKelkoo
	 */
	public function setIsKelkoo($isKelkoo) {
		$this->isKelkoo = $isKelkoo;
	}

	/**
	 * Get isKelkoo
	 *
	 * @return bool
	 */
	public function getIsKelkoo() {
		return $this->isKelkoo;
	}

	/**
	 * Set variantsTemplate
	 *
	 * @param string $variantsTemplate
	 */
	public function setVariantsTemplate($variantsTemplate) {
		if (!in_array($variantsTemplate, array('full_with_stock', 'radio', 'drop_down', 'constructor'))) $variantsTemplate = 'full_with_stock';
		$this->variantsTemplate = $variantsTemplate;
	}

	/**
	 * Get variantsTemplate
	 *
	 * @return string
	 */
	public function getVariantsTemplate() {
		return $this->variantsTemplate;
	}

	/**
	 * Set minQuantity
	 *
	 * @param integer $minQuantity
	 */
	public function setMinQuantity($minQuantity) {
		$this->minQuantity = $minQuantity;
	}

	/**
	 * Get minQuantity
	 *
	 * @return integer
	 */
	public function getMinQuantity() {
		return $this->minQuantity;
	}

	/**
	 * Set maxQuantity
	 *
	 * @param integer $maxQuantity
	 */
	public function setMaxQuantity($maxQuantity) {
		$this->maxQuantity = $maxQuantity;
	}

	/**
	 * Get maxQuantity
	 *
	 * @return integer
	 */
	public function getMaxQuantity() {
		return $this->maxQuantity;
	}

	/**
	 * Set minWeight
	 *
	 * @param float $minWeight
	 */
	public function setMinWeight($minWeight) {
		$this->minWeight = $minWeight;
	}

	/**
	 * Get minWeight
	 *
	 * @return float
	 */
	public function getMinWeight() {
		return $this->minWeight;
	}

	/**
	 * Set maxWeight
	 *
	 * @param float $maxWeight
	 */
	public function setMaxWeight($maxWeight) {
		$this->maxWeight = $maxWeight;
	}

	/**
	 * Get maxWeight
	 *
	 * @return float
	 */
	public function getMaxWeight() {
		return $this->maxWeight;
	}

	/**
	 * Set ordered
	 *
	 * @param integer $count
	 */
	public function setTotalOrdered($count) {
		$this->totalOrdered = $count;
	}

	/**
	 * Get ordered
	 *
	 * @return integer
	 */
	public function getTotalOrdered() {
		return $this->totalOrdered;
	}

	/**
	 *  Alias to getTotalOrdered
	 * @return int
	 */
	public function getOrdered() {
		return $this->getTotalOrdered();
	}

	/**
	 * Set totalOrderedEver
	 *
	 * @param integer $orderedEver
	 */
	public function setTotalOrderedEver($orderedEver) {
		$this->totalOrderedEver = $orderedEver;
	}

	/**
	 * Get totalOrderedEver
	 *
	 * @return integer
	 */
	public function getTotalOrderedEver() {
		return $this->totalOrderedEver;
	}

	/**
	 * Set viewed
	 *
	 * @param bigint $viewed
	 */
	public function setTotalViewsCount($viewed) {
		$this->totalViewsCount = $viewed;
	}

	/**
	 * Get viewed
	 *
	 * @return bigint
	 */
	public function getTotalViewsCount() {
		return $this->totalViewsCount;
	}

	/**
	 * Set totalArticles
	 *
	 * @param integer $totalArticles
	 */
	public function setTotalArticles($totalArticles) {
		$this->totalArticles = $totalArticles;
	}

	/**
	 * Get totalArticles
	 *
	 * @return integer
	 */
	public function getTotalArticles() {
		return $this->totalArticles;
	}

	/**
	 * Set activeArticles
	 *
	 * @param integer $activeArticles
	 */
	public function setActiveArticles($activeArticles) {
		$this->activeArticles = $activeArticles;
	}

	/**
	 * Get activeArticles
	 *
	 * @return integer
	 */
	public function getActiveArticles() {
		return $this->activeArticles;
	}

	/**
	 * Set sorting
	 *
	 * @param integer $int
	 */
	public function setSorting($int) {
		$this->sorting = $int;
	}

	/**
	 * Get sorting
	 *
	 * @return integer
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * Update isInActiveCategory

	 */
	public function updateIsInActiveCategory() {
		$this->isInActiveCategory = (bool)$this->getActiveCategories()->count();
	}

	/**
	 * Set $isInActiveCategory
	 *
	 * @param bool
	 */
	public function setIsInActiveCategory($isInActiveCategory) {
		$this->isInActiveCategory = $isInActiveCategory;
	}

	/**
	 * Get $isInActiveCategory
	 *
	 * @return bool
	 */
	public function getIsInActiveCategory() {
		return $this->isInActiveCategory;
	}

	/**
	 * Set isLowOnStock
	 *
	 * @param bool $isLowOnStock
	 */
	public function setIsLowOnStock($isLowOnStock) {
		$this->isLowOnStock = $isLowOnStock;
	}

	/**
	 * Get isLowOnStock
	 *
	 * @return bool
	 */
	public function getIsLowOnStock() {
		return $this->isLowOnStock;
	}

	/**
	 * Set isMetaNoindex
	 *
	 * @param bool $isMetaNoIndex
	 */
	public function setIsMetaNoIndex($isMetaNoIndex) {
		$this->isMetaNoIndex = $isMetaNoIndex;
	}

	/**
	 * Get isMetaNoIndex
	 *
	 * @return bool
	 */
	public function getIsMetaNoIndex() {
		return $this->isMetaNoIndex;
	}

	/**
	 * Set shippingTime
	 *
	 * @param string $shippingTime
	 */
	public function setShippingTime($shippingTime) {
		$this->shippingTime = $shippingTime;
	}

	/**
	 * Get shippingTime
	 *
	 * @return string
	 */
	public function getShippingTime() {
		return $this->shippingTime;
	}

	/**
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}

	/**
	 * Get createdAt
	 *
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * Set modifiedAt
	 *
	 * @param \DateTime $modifiedAt
	 */
	public function setModifiedAt($modifiedAt) {
		$this->modifiedAt = $modifiedAt;
	}

	/**
	 * Get modifiedAt
	 *
	 * @return \DateTime
	 */
	public function getModifiedAt() {
		return $this->modifiedAt;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $summary
	 */
	public function setSummary($summary) {
		$this->summary = $summary;
	}

	/**
	 * @return string
	 */
	public function getSummary() {
		return $this->summary;
	}

	/**
	 * Get product Files
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getFiles() {
		return $this->files;
	}

	/**
	 * Set product Files
	 *
	 * @param \Doctrine\Common\Collections\ArrayCollection $files
	 * @return void
	 */
	public function setFiles(\Doctrine\Common\Collections\ArrayCollection $files) {
		$this->files = $files;
	}

	/**
	 * @param \Angler\CoreBundle\Entity\Supplier $supplier
	 */
	public function setSupplier($supplier) {
		$this->supplier = $supplier;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\Supplier
	 */
	public function getSupplier() {
		return $this->supplier;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\Category
	 */
	public function getMainCategory() {
		return $this->mainCategory;
	}

	/**
	 * Set all product images
	 *
	 * @param \Doctrine\Common\Collections\ArrayCollection $images
	 */
	public function setImages($images) {
		$this->images = $images;
	}

	/**
	 * Get all product images
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getImages() {
		return $this->images;
	}

	/**
	 * Set all product categories
	 *
	 * @param \Doctrine\Common\Collections\ArrayCollection $categories
	 * @return void
	 */
	public function setCategories(\Doctrine\Common\Collections\ArrayCollection $categories) {
		$this->categories = $categories;
		$mainCategory     = $this->getMainCategory();
		if (!$categories->exists(function($key, $cat) use ($mainCategory) {
			return $cat === $mainCategory;
		})
		) {
			$this->setMainCategory($categories->get(0));
		}
	}

	/**
	 * Get all product categories
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Angler\CoreBundle\Entity\Category[]
	 */
	public function getCategories() {
		return $this->categories;
	}

	public function getActiveCategories() {
		return $this->getCategories()->filter(function(\Angler\CoreBundle\Entity\Category $category) {
			return $category->getIsActive();
		});
	}

	public function setAttributes(ArrayCollection $attributes) {
		$this->attributes = $attributes;
	}

	public function getAttributes() {
		return $this->attributes;
	}

	public function getReviews() {
		return $this->reviews;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductToCrossSell[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getProductToCrossSells() {
		return $this->productToCrossSells;
	}

	public function setProductToCrossSells($productToCrossSells) {
		$this->productToCrossSells = $productToCrossSells;
	}

	public function removeCrossSell(\Angler\CoreBundle\Entity\ProductToCrossSell $productToCrossSell) {
		$this->getProductToCrossSells()->removeElement($productToCrossSell);
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductToPlusSell[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getProductToPlusSells() {
		return $this->productToPlusSells;
	}

	public function setProductToPlusSells($productToPlusSells) {
		$this->productToPlusSells = $productToPlusSells;
	}

	public function removePlusSell(\Angler\CoreBundle\Entity\ProductToPlusSell $productToPlusSell) {
		$this->getProductToPlusSells()->removeElement($productToPlusSell);
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductArticleBundle[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBundles() {
		return $this->bundles;
	}

	public function setBundles($bundles) {
		$this->bundles = $bundles;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setFrozenUrl($frozenUrl) {
		$this->frozenUrl = $frozenUrl;
	}

	public function getFrozenUrl() {
		return $this->frozenUrl;
	}

	/**
	 * @param string $homepage
	 */
	public function setHomepage($homepage) {
		$this->homepage = $homepage;
	}

	/**
	 * @return string
	 */
	public function getHomepage() {
		return $this->homepage;
	}

	public function setPageMetaDescription($description) {
		$this->pageMetaDescription = $description;
	}

	public function getPageMetaDescription() {
		return $this->pageMetaDescription;
	}

	public function setPageMetaKeywords($keywords) {
		$this->pageMetaKeywords = $keywords;
	}

	public function getPageMetaKeywords() {
		return $this->pageMetaKeywords;
	}

	public function setPageTitle($title) {
		$this->pageTitle = $title;
	}

	public function getPageTitle() {
		return $this->pageTitle;
	}

	public function setVideo($video) {
		$this->video = $video;
	}

	public function getVideo() {
		return $this->video;
	}

	/**
	 * @param int $viewsCount
	 */
	public function setViewsCount($viewsCount) {
		$this->viewsCount = $viewsCount;
	}

	/**
	 * @return int
	 */
	public function getViewsCount() {
		return $this->viewsCount;
	}

	/**
	 * @param \Angler\CoreBundle\Entity\TaxClass $taxClass
	 */
	public function setTaxClass(\Angler\CoreBundle\Entity\TaxClass $taxClass = null) {
		$this->taxClass = $taxClass;
	}

	public function getTaxClass() {
		return $this->taxClass;
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $tierPrices
	 */
	public function setTierPrices($tierPrices) {
		$this->tierPrices = $tierPrices;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductTierPrice[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTierPrices() {
		return $this->tierPrices;
	}

	/**
	 * @return bool
	 */
	public function getHasTierPrices() {
		return ($this->getTierPrices()->count() > 0) ? true : false;
	}

	/**
	 * @param float $cachedMaxPrice
	 */
	public function setCachedMaxPrice($cachedMaxPrice) {
		$this->cachedMaxPrice = $cachedMaxPrice;
	}

	/**
	 * @return float
	 */
	public function getCachedMaxPrice() {
		return $this->cachedMaxPrice;
	}

	/**
	 * @param float $cachedMinPrice
	 */
	public function setCachedMinPrice($cachedMinPrice) {
		$this->cachedMinPrice = $cachedMinPrice;
	}

	/**
	 * @return float
	 */
	public function getCachedMinPrice() {
		return $this->cachedMinPrice;
	}

	/**
	 * @param \Angler\CoreBundle\Entity\Base\decimal $cachedFormerPrice
	 */
	public function setCachedFormerPrice($cachedFormerPrice) {
		$this->cachedFormerPrice = $cachedFormerPrice;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\Base\decimal
	 */
	public function getCachedFormerPrice() {
		return $this->cachedFormerPrice;
	}

	/**
	 * @param \Angler\CoreBundle\Entity\Base\decimal $rating
	 */
	public function setRating($rating) {
		$this->rating = $rating;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\Base\decimal
	 */
	public function getRating() {
		return $this->rating;
	}

	/**
	 * @return array | ArrayCollection | \Angler\CoreBundle\Entity\KeywordFocused[]
	 */
	public function getFocusedKeywords() {
		return $this->keywordsFocused;
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $keywords
	 */
	public function setFocusedKeywords($keywords) {
		$this->keywordsFocused = $keywords;
	}

	/**
	 * @return null | ArrayCollection | \Angler\CoreBundle\Entity\KeywordAdditional[]
	 */
	public function getAdditionalKeywords() {
		return $this->keywordsAdditional;
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $keywords
	 */
	public function setAdditionalKeywords($keywords) {
		$this->keywordsAdditional = $keywords;
	}

	/**
	 * @param string $bundlesTemplate
	 */
	public function setBundlesTemplate($bundlesTemplate) {
		$this->bundlesTemplate = $bundlesTemplate;
	}

	/**
	 * @return string
	 */
	public function getBundlesTemplate() {
		return $this->bundlesTemplate;
	}

	public function setHotExpiresAt($hotExpiresAt) {
		$this->hotExpiresAt = $hotExpiresAt;
	}

	public function getHotExpiresAt() {
		return $this->hotExpiresAt;
	}

	/**
	 * @param boolean $isHot
	 */
	public function setIsHot($isHot) {
		$this->isHot = $isHot;
	}

	/**
	 * @return boolean
	 */
	public function getIsHot() {
		return $this->isHot;
	}

	public function setProductToAlsoPurchasedProduct($productToAlsoPurchasedProduct) {
		$this->productToAlsoPurchasedProduct = $productToAlsoPurchasedProduct;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductToAlsoPurchasedProduct[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getProductToAlsoPurchasedProduct() {
		return $this->productToAlsoPurchasedProduct;
	}

	/**
	 * @return int
	 */
	public function getHasSorting() {
		return $this->hasSorting;
	}

	/**
	 * @param int $hasSorting
	 */
	public function setHasSorting($hasSorting) {
		$this->hasSorting = $hasSorting;
	}

	/**
	 * @param boolean $hasVariants
	 */
	public function setHasVariants($hasVariants) {
		$this->hasVariants = $hasVariants;
	}

	/**
	 * @return boolean
	 */
	public function getHasVariants() {
		return $this->hasVariants;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductPriceIndex[] | \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getIndexedPrices() {
		return $this->indexedPrices;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Angler\CoreBundle\Entity\PriceRule[]
	 */
	public function getPriceRules() {
		return $this->priceRules;
	}

	public static function loadValidatorMetadata(\Symfony\Component\Validator\Mapping\ClassMetadata $metadata) {
		$metadata->addPropertyConstraint('title', new \Symfony\Component\Validator\Constraints\NotBlank(array(
			'message' => 'Title is required',
		)));
		$metadata->addPropertyConstraint('title', new \Symfony\Component\Validator\Constraints\MinLength(array(
			'limit'   => '5',
			'message' => "Too small title",
		)));
		$metadata->addPropertyConstraint('rawPrice', new \Symfony\Component\Validator\Constraints\Min(array(
			'message'        => 'Dude, this value should be {{ limit }} or more',
			'invalidMessage' => 'Dude, this value should be a valid number',
			'limit'          => 0,
		)));
	}

	/**
	 * @param \Angler\CoreBundle\Entity\ProductSpecial|null $specialRule
	 */
	public function setSpecialRule(\Angler\CoreBundle\Entity\ProductSpecial $specialRule = null) {
		$this->specialRule = $specialRule;
	}

	/**
	 * @return \Angler\CoreBundle\Entity\ProductSpecial
	 */
	public function getSpecialRule() {
		return $this->specialRule;
	}

	/**
	 * @param float $maxDiscount
	 */
	public function setMaxDiscount ($maxDiscount) {
		$this->maxDiscount = $maxDiscount;
	}

	/**
	 * @return float
	 */
	public function getMaxDiscount () {
		return $this->maxDiscount;
	}

}
