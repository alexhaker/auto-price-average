<?php
namespace AutoRIABundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Category extends Dictionary
{
	/**
	 * @MongoDB\ReferenceMany(targetDocument="BodyStyle")
	 */
	private $bodyStyles;

	/**
	 * @MongoDB\ReferenceMany(targetDocument="Brand")
	 */
	private $brands;

	/**
	 * @MongoDB\ReferenceMany(targetDocument="Gearbox")
	 */
	private $gearboxes;

	/**
	 * @MongoDB\ReferenceMany(targetDocument="DriverType")
	 */
	private $driverTypes;

	/**
	 * @MongoDB\ReferenceMany(targetDocument="Option")
	 */
	private $options;


	public function __construct()
	{
		$this->bodyStyles = new ArrayCollection();
		$this->brands = new ArrayCollection();
		$this->gearboxes = new ArrayCollection();
		$this->driverTypes = new ArrayCollection();
		$this->options = new ArrayCollection();
	}

	/**
	 * @return array
	 */
	public function getBodyStyles()
	{
		return $this->bodyStyles;
	}

	/**
	 * @param array $bodyStyles
	 */
	public function setBodyStyles($bodyStyles)
	{
		$this->bodyStyles = $bodyStyles;
	}

	/**
	 * @return array
	 */
	public function getBrands()
	{
		return $this->brands;
	}

	/**
	 * @param array $brands
	 */
	public function setBrands($brands)
	{
		$this->brands = $brands;
	}

	/**
	 * @return array
	 */
	public function getGearboxs()
	{
		return $this->gearboxes;
	}

	/**
	 * @param array $gearboxes
	 */
	public function setGearboxs($gearboxes)
	{
		$this->gearboxes = $gearboxes;
	}

	/**
	 * @return array
	 */
	public function getDriverTypes()
	{
		return $this->driverTypes;
	}

	/**
	 * @param array $driverTypes
	 */
	public function setDriverTypes($driverTypes)
	{
		$this->driverTypes = $driverTypes;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}

	public function addBodyStyle(BodyStyle $bodyStyle)
	{
		$this->bodyStyles[] = $bodyStyle;
	}

	public function addBrand(Brand $brand)
	{
		$this->brands[] = $brand;
	}

	public function addGearbox(Gearbox $gearbox)
	{
		$this->gearboxes[] = $gearbox;
	}

	public function addDriverType(DriverType $driverType)
	{
		$this->driverTypes[] = $driverType;
	}

	public function addOption(Option $option)
	{
		$this->options[] = $option;
	}
}