<?php

namespace Angler\StoreBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Keyword
 * @ORM\Entity (repositoryClass="Angler\StoreBundle\Repository\KeywordRepository")
 * @ORM\Table(name="_obb_keyword",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="keyword_unique", columns={"category_id", "product_id", "locale", "text"})}
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"focused" = "\Angler\StoreBundle\Entity\KeywordFocused", "additional" = "\Angler\StoreBundle\Entity\KeywordAdditional"})
 */
abstract class Keyword {

	const KEYWORD_STATUS_OK = 'ok';
	const KEYWORD_STATUS_WARN = 'warning';
	const KEYWORD_STATUS_ERROR = 'error';

	static public $KEYWORD_STATUSES = array(self::KEYWORD_STATUS_OK, self::KEYWORD_STATUS_WARN, self::KEYWORD_STATUS_ERROR);

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @var integer
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\StoreBundle\Entity\Category")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
	 * @var Category
	 */
	protected $category;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\StoreBundle\Entity\Product")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
	 * @var Product
	 */
	protected $product;

	/**
	 * @ORM\Column(name="locale", type="string")
	 * @var string
	 */
	protected $locale;

	/**
	 * @ORM\Column(name="status", type="string", columnDefinition="ENUM('ok','warning','error')")
	 * @var string
	 */
	protected $status = self::KEYWORD_STATUS_OK;


	/**
	 * @ORM\Column(name="text", type="string", nullable=false)
	 * @var string
	 */
	protected $text;

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

	public function __construct() {
		$this->locale = \Context::getInstance()->getTranslationListener()->getListenerLocale();
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
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	 * @param string $status
	 */
	public function setStatus($status) {
		if (in_array($status, self::$KEYWORD_STATUSES))
			$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $text
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}

	public function getPageType() {
		if ($this->getProduct()) {
			return 'product';
		} else if ($this->getCategory()) {
			return 'category';
		}
		return 'unknown';
	}

	public function getType() {
		if ($this instanceof KeywordFocused) return 'focused';
		return 'extra';
	}

	public static function getAllPageTypes() {
		return array('product'=> _('Product'),
					 'categoy'=> _('Category'));
	}

	/**
	 * @param \Angler\StoreBundle\Entity\Category $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * @return \Angler\StoreBundle\Entity\Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param \Angler\StoreBundle\Entity\Product $product
	 */
	public function setProduct($product) {
		$this->product = $product;
	}

	/**
	 * @return \Angler\StoreBundle\Entity\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return null | \Angler\StoreBundle\Entity\Category | \Angler\StoreBundle\Entity\Product
	 */
	public function getPageObject() {
		switch ($this->getPageType()) {
			case 'product': return $this->getProduct();
			case 'category': return $this->getCategory();
		}
		return null;
	}

	/**
	 * @param string $locale
	 */
	public function setLocale($locale) {
		$this->locale = $locale;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	public static function getFocusedChoices() {
		return array(
			'focused' => 'Focused',
			'additional' => 'Extra',
		);
	}

	public static function getStatusChoices() {
		return array(
			'ok' => 'Good',
			'error' => 'Error',
			'warning' => 'Warning',
		);
	}

	/**
	 * @return \Angler\StoreBundle\Entity\Language
	 */
	public function getLanguage() {
		foreach (\Context::getInstance()->getFELanguages() as $lang) {
			if ($lang->getCode() == $this->getLocale()) return $lang;
		}
		return  \Context::getInstance()->getEm()->getRepository('Angler\StoreBundle\Entity\Language')->findOneBy(array('code'=>$this->getLocale()));
	}

	public function __toString() {
		return $this->getText();
	}
}
