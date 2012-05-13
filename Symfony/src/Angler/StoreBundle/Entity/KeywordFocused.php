<?php

namespace Angler\StoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * KeywordFocused
 * @ORM\Entity
 */
class KeywordFocused  extends Keyword {
	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\StoreBundle\Entity\Category", cascade={"persist"}, inversedBy="keywordsFocused")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
	 * @var Category
	 */
	protected $category;

	/**
	 * @ORM\ManyToOne (targetEntity="\Angler\StoreBundle\Entity\Product", cascade={"persist"}, inversedBy="keywordsFocused")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
	 * @var Product
	 */
	protected $product;

}
