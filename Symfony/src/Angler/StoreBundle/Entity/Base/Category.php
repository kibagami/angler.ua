<?php

namespace Openbizbox\CoreBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Openbizbox\CoreBundle\Model\OBBTranslatable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Gedmo\Tree\Node;
use Openbizbox\CoreBundle as Core;

/** 
 * @ORM\MappedSuperclass 
 * @Gedmo\TranslationEntity(class="Openbizbox\CoreBundle\Entity\Translations\CategoryTranslation")
 */
abstract class Category extends OBBTranslatable implements Node, Core\SEOKeywordsInterface {

	const PATH_SEPARATOR = '-';

	/**
	 * @ORM\Id
	 * @ORM\Column(name="category_id", type="integer")
	 * @ORM\GeneratedValue
	 * @var integer $Id
	 */
	protected $id;

	/**
	 * @gedmo\TreeLevel
	 * @ORM\Column(name="level", type="integer", nullable=true)
	 */
	protected $level = null;

	/**
	 * @gedmo\TreeLeft
	 * @ORM\Column(name="`left`", type="integer", nullable=true)
	 */
	protected $left = null;

	/**
	 * @gedmo\TreeRight
	 * @ORM\Column(name="`right`", type="integer", nullable=true)
	 */
	protected $right = null;

	/**
	 * @gedmo\TreeRoot
	 * @ORM\Column(name="root", type="integer", nullable=true)
	 */
	protected $root = null;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 * @var boolean $isActive
	 */
	protected $isActive = true; //TODO: rename to isEnabled

	/**
	 * @ORM\Column(name="is_enabled", type="boolean")
	 * @var boolean $isEnabled
	 */
	protected $isEnabled = true;

	/**
	 * @ORM\Column(name="is_meta_noindex", type="boolean")
	 * @var boolean $isMetaNoIndex
	 */
	protected $isMetaNoIndex = false;
	/**
	 * @ORM\Column(name="image", type="string")
	 * @var string $image
	 */
	protected $image = '';
	/**
	 * @ORM\Column(name="icon", type="string")
	 * @var string $icon
	 */
	protected $icon = '';

	/**
	 * @ORM\Column(name="enabled_products_count", type="integer")
	 * @var integer $enabledProductsCount
	 */
	protected $enabledProductsCount = 0;

	/**
	 * @ORM\Column(name="purchasable_products_count", type="integer")
	 * @var integer $purchasableProductsCount
	 */
	protected $purchasableProductsCount = 0;

	/**
	 * @ORM\Column(name="enabled_products_count_self", type="integer")
	 * @var integer $enabledProductsCountSelf
	 */
	protected $enabledProductsCountSelf = 0;

	/**
	 * @ORM\Column(name="purchasable_products_count_self", type="integer")
	 * @var integer $purchasableProductsCountSelf
	 */
	protected $purchasableProductsCountSelf = 0;

	/**
	 * @ORM\Column(name="total_products_count_self", type="integer")
	 * @var integer $totalProductsCountSelf
	 */
	protected $totalProductsCountSelf = 0;

	/**
	 * @ORM\Column(name="sorting", type="integer", nullable=true)
	 * @var integer $sorting
	 */
	protected $sorting;

	/**
	 * "N-column", "1 column"
	 * @ORM\Column(name="product_listing_type", type="string")
	 * @var string $productListingType
	 */
	protected $productListingType = '1 column';

	/**
	 * @var string $title
	 * @Gedmo\Translatable
	 * @ORM\Column(type="string", length=255)
	 */
	protected $title = "";

	/**
	 * @var string $headingTitle
	 * @Gedmo\Translatable
	 * @ORM\Column(type="string", name="heading_title", length=255)
	 */
	protected $headingTitle = "";

	/**
	 * @var string $description
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text")
	 */
	protected $description = "";
	/**
	 * @var string $metaDescription
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text", name="meta_description")
	 */
	protected $metaDescription = "";

	/**
	 * @var string $secondaryDescription
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text", name="secondary_description")
	 */
	protected $secondaryDescription = "";

	/**
	 * @var string $pageMetaKeywords
	 * @Gedmo\Translatable
	 * @ORM\Column(name="page_meta_keywords", type="text")
	 */
	protected $pageMetaKeywords = "";  //FIXME rename to metaKeywords

	/**
	 * @var string $url
	 * @Gedmo\Translatable
	 * @ORM\Column(type="string", length=255)
	 */
	protected $url = "";

	/**
	 * @Gedmo\Locale
	 * Used locale to override Translation listener`s locale
	 * this is not a mapped field of entity metadata, just a simple property
	 */
	protected $locale;

	/**
	 * @var \Datetime $createdAt
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime", name="created_at", nullable=false)
	 */
	protected $createdAt;

	/**
	 * @var \DateTime $modifiedAt
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(type="datetime", name="modified_at", nullable=false)
	 */
	protected $modifiedAt;

	/**
	 * @var \Openbizbox\CoreBundle\Entity\KeywordFocused[] | \Doctrine\Common\Collections\ArrayCollection  $keywordsFocused
	 * @ORM\OneToMany(targetEntity="\Openbizbox\CoreBundle\Entity\KeywordFocused", mappedBy="category", cascade={"persist", "remove"}, orphanRemoval=true, indexBy="locale")
	 */
	protected $keywordsFocused = null;

	/**
	 * @var \Openbizbox\CoreBundle\Entity\KeywordAdditional[] | \Doctrine\Common\Collections\ArrayCollection $keywordsAdditional
	 * @ORM\OneToMany(targetEntity="\Openbizbox\CoreBundle\Entity\KeywordAdditional", mappedBy="category", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	protected $keywordsAdditional;

	/**
	 * @var \Openbizbox\CoreBundle\Entity\PriceRule[] | \Doctrine\Common\Collections\ArrayCollection $priceRules
	 * @ORM\ManyToMany(targetEntity="\Openbizbox\CoreBundle\Entity\PriceRule", mappedBy="categories")
	 */
	protected $priceRules;

	/**
	 * @var \Openbizbox\CoreBundle\Entity\Category
	 * @gedmo\TreeParent
	 * @ORM\ManyToOne(targetEntity="\Openbizbox\CoreBundle\Entity\Category", inversedBy="children", cascade={"persist"})
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="category_id", onDelete="CASCADE")
	 */
	protected $parent;

	/**
	 * @var \Openbizbox\CoreBundle\Entity\Category[]
	 * @ORM\OneToMany(targetEntity="\Openbizbox\CoreBundle\Entity\Category", mappedBy="parent", cascade={"remove"})
	 * @ORM\OrderBy({"left" = "ASC"})
	 */
	protected $children;

	/**
	 * @ORM\ManyToMany(targetEntity="Product", mappedBy="categories", cascade={"persist"})
	 */
	protected $products;

	/**
	 * @var \Openbizbox\CoreBundle\Entity\Translations\CategoryTranslation[] | \Doctrine\Common\Collections\ArrayCollection $translations
	 * @ORM\OneToMany(targetEntity="\Openbizbox\CoreBundle\Entity\Translations\CategoryTranslation", mappedBy="object", cascade={"persist", "remove"})
	*/
	protected $translations;

	public function __construct() {
		$this->keywordsAdditional = new \Doctrine\Common\Collections\ArrayCollection();
		$this->keywordsFocused    = new \Doctrine\Common\Collections\ArrayCollection();
		$this->priceRules         = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translations 	  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @param string $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param boolean $isActive
	 */
	public function setIsActive($isActive) {
		$this->isActive = $isActive;
	}

	/**
	 * @return boolean
	 */
	public function getIsActive() {
		return $this->isActive;
	}

	public function updateIsActive() {
		$this->setIsActive($this->getIsEnabled());
	}

	/**
	 * @param boolean $isEnabled
	 */
	public function setIsEnabled($isEnabled) {
		$this->isEnabled = $isEnabled;
		$this->updateIsActive();
	}

	/**
	 * @return boolean
	 */
	public function getIsEnabled() {
		return $this->isEnabled;
	}

	/**
	 * @param boolean $isMetaNoIndex
	 */
	public function setIsMetaNoIndex($isMetaNoIndex) {
		$this->isMetaNoIndex = $isMetaNoIndex;
	}

	/**
	 * @return boolean
	 */
	public function getIsMetaNoIndex() {
		return $this->isMetaNoIndex;
	}

	/**
	 * @param \DateTime $modifiedAt
	 */
	public function setModifiedAt($modifiedAt) {
		$this->modifiedAt = $modifiedAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getModifiedAt() {
		return $this->modifiedAt;
	}

	/**
	 * @param string $productListingType
	 */
	public function setProductListingType($productListingType) {
		$this->productListingType = $productListingType;
	}

	/**
	 * @return string
	 */
	public function getProductListingType() {
		return $this->productListingType;
	}


	/**
	 * @param int $int
	 */
	public function setSorting($int) {
		$this->sorting = $int;
	}

	/**
	 * @return int
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $headingTitle
	 */
	public function setHeadingTitle($headingTitle) {
		$this->headingTitle = $headingTitle;
	}

	/**
	 * @return string
	 */
	public function getHeadingTitle() {
		return $this->headingTitle;
	}

	/**
	 * @param string $metaDescription
	 */
	public function setMetaDescription($metaDescription) {
		$this->metaDescription = $metaDescription;
	}

	/**
	 * @return string
	 */
	public function getMetaDescription() {
		return $this->metaDescription;
	}

	/**
	 * @param string $secondaryDescription
	 */
	public function setSecondaryDescription($secondaryDescription) {
		$this->secondaryDescription = $secondaryDescription;
	}

	/**
	 * @return string
	 */
	public function getSecondaryDescription() {
		return $this->secondaryDescription;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return null | ArrayCollection | \Openbizbox\CoreBundle\Entity\KeywordFocused[]
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
	 * @return null | ArrayCollection | \Openbizbox\CoreBundle\Entity\KeywordAdditional[]
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
	 * @return string
	 */
	public function getPageMetaKeywords() {
		return $this->pageMetaKeywords;
	}

	/**
	 * @param string $keywords
	 */
	public function setPageMetaKeywords($keywords) {
		$this->pageMetaKeywords = $keywords;
	}

	/**
	 * @param int $enabledProductsCount
	 */
	public function setEnabledProductsCount($enabledProductsCount) {
		$this->enabledProductsCount = $enabledProductsCount;
	}

	/**
	 * @return int
	 */
	public function getEnabledProductsCount() {
		return $this->enabledProductsCount;
	}

	/**
	 * @param int $purchasableProductsCount
	 */
	public function setPurchasableProductsCount($purchasableProductsCount) {
		$this->purchasableProductsCount = $purchasableProductsCount;
	}

	/**
	 * @return int
	 */
	public function getPurchasableProductsCount() {
		return $this->purchasableProductsCount;
	}

	/**
	 * @param int $totalProductsCountSelf
	 */
	public function setTotalProductsCountSelf($totalProductsCountSelf) {
		$this->totalProductsCountSelf = $totalProductsCountSelf;
	}

	/**
	 * @return int
	 */
	public function getTotalProductsCountSelf() {
		return $this->totalProductsCountSelf;
	}

	/**
	 * @param int $enabledProductsCountSelf
	 */
	public function setEnabledProductsCountSelf($enabledProductsCountSelf) {
		$this->enabledProductsCountSelf = $enabledProductsCountSelf;
	}

	/**
	 * @return int
	 */
	public function getEnabledProductsCountSelf() {
		return $this->enabledProductsCountSelf;
	}

	/**
	 * @param int $purchasableProductsCountSelf
	 */
	public function setPurchasableProductsCountSelf($purchasableProductsCountSelf) {
		$this->purchasableProductsCountSelf = $purchasableProductsCountSelf;
	}

	/**
	 * @return int
	 */
	public function getPurchasableProductsCountSelf() {
		return $this->purchasableProductsCountSelf;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Openbizbox\CoreBundle\Entity\PriceRule[]
	 */
	public function getPriceRules() {
		return $this->priceRules;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getParentPriceRules() {
		$rules = new \Doctrine\Common\Collections\ArrayCollection;
		if ($this->getParent()) {
			foreach ($this->getParent()->getPriceRules() as $rule) {
				if (!$rules->contains($rule)) $rules->add($rule);
			}
			foreach ($this->getParent()->getParentPriceRules() as $rule) {
				if (!$rules->contains($rule)) $rules->add($rule);
			}
		}
		return $rules;
	}

	public function __toString() {
		return "Category: " . $this->getId() . ' ' . $this->getTitle();
	}

	public function getLevel() {
		return $this->level;
	}

	/**
	 * @return \Openbizbox\CoreBundle\Entity\Category | null
	 */
	public function getParent() {
		return $this->parent;
	}

}

