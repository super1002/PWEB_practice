<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Validator;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Validator\Length(
     *     max = 50,
     *     maxMessage = "The product name cannot be longer than {{ limit }} characters"
     *     )
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="normalized_name", type="string", length=255, unique=true)
     */
    private $normalizedName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=5000)
     */
    private $description;

    /**
     * @var float
     * @Validator\Range(
     *     min = 0,
     *     minMessage = "The product cannot have a negative cost"
     *     )
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     * @Validator\Range(
     *     min = 1,
     *     minMessage = "The product cannot have a negative stock or 0 stock"
     *     )
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock;

    /**
     * @var DateTime
     *
     * @Validator\Range(
     *      min = "now"
     *     )
     *
     * @ORM\Column(name="expiring_date", type="datetime")
     */
    private $expiringDate;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=550)
     */
    private $picture;

    /**
    * @var string
    *
    * @ORM\Column(name="category", type="string", length=550)
    */
    private $category;

    /**
     * @Validator\Image(
     *     mimeTypes= {"image/png", "image/gif", "image/jpg", "image/jpeg", "image/pjpeg"},
     *     mimeTypesMessage="Unsupported file type, use gif, jpg or png"
     * )
     * @var UploadedFile
     */
    private $file;

    /**
     * @var User $owner
     * @ORM\ManyToOne(targetEntity="User", inversedBy="products")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $owner;

    /**
     * @var DateTime $creationDate
     * @ORM\Column(name="creation_date", type="datetime")
     *
     */
    private $creationDate;

    /**
     * @var int $numVisits
     * @ORM\Column(name="num_visits", type="integer")
     */
    private $numVisits;

    /**
     * @var int $numSells
     * @ORM\Column(name="num_sells", type="integer")
     */
    private $numSells;

    /**
     * @var Arraycollection $purchases
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="product")
     */
    private $purchases;

    public function __construct()
    {
        $this->creationDate = new DateTime();
        $this->numVisits = 0;
        $this->numSells = 0;
        $this->expiringDate = new DateTime();
        $this->file = null;
        $this->purchases = new ArrayCollection();

    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set normalizedName
     *
     * @param string $normalizedName
     *
     * @return Product
     */
    public function setNormalizedName($normalizedName)
    {
        $this->normalizedName = $normalizedName;

        return $this;
    }

    /**
     * Get normalizedName
     *
     * @return string
     */
    public function getNormalizedName()
    {
        return $this->normalizedName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        $this->description;


        $ltrimmed = ltrim($this->description, "\"");
        $trimmed = rtrim($ltrimmed, "\"");


        return $trimmed;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set expiringDate
     *
     * @param \DateTime $expiringDate
     *
     * @return Product
     */
    public function setExpiringDate($expiringDate)
    {
        $this->expiringDate = $expiringDate;

        return $this;
    }

    /**
     * Get expiringDate
     *
     * @return \DateTime
     */
    public function getExpiringDate()
    {
        return $this->expiringDate;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return Product
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture 100x100
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture.'.100.png';
    }

    /**
     * Get picture 100x100
     *
     * @return string
     */
    public function getPicture100()
    {
        return $this->picture.'.100.png';
    }

    /**
     * Get picture 400x300
     *
     * @return string
     */
    public function getPicture400(){

        return $this->picture.'.400.png';
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     *
     * @return Product
     */
    public function setOwner(\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Product
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set numVisits
     *
     * @param integer $numVisits
     *
     * @return Product
     */
    public function setNumVisits($numVisits)
    {
        $this->numVisits = $numVisits;

        return $this;
    }

    /**
     * Get numVisits
     *
     * @return int
     */
    public function getNumVisits()
    {
        return $this->numVisits;
    }

    /**
     * Set numSells
     *
     * @param integer $numSells
     *
     * @return Product
     */
    public function setNumSells($numSells)
    {
        $this->numSells = $numSells;

        return $this;
    }

    /**
     * Get numSells
     *
     * @return int
     */
    public function getNumSells()
    {
        return $this->numSells;
    }


    public function isNotAvailable(){
        //returns true if it has no stock or it is expired
        return $this->stock <= 0 or $this->expiringDate->diff(new \DateTime())->invert == 0;
    }

    /**
     * Add buyer
     *
     * @param \AppBundle\Entity\User $buyer
     *
     * @return Product
     */
    public function addBuyer(\AppBundle\Entity\User $buyer)
    {
        $this->buyers[] = $buyer;

        return $this;
    }

    /**
     * Remove buyer
     *
     * @param \AppBundle\Entity\User $buyer
     */
    public function removeBuyer(\AppBundle\Entity\User $buyer)
    {
        $this->buyers->removeElement($buyer);
    }

    /**
     * Get buyers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuyers()
    {
        return $this->buyers;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     *
     * @return Product
     */
    public function addPurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     */
    public function removePurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }
}
