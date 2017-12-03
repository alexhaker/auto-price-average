<?php
namespace AutoRIABundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Region extends Dictionary
{
	/**
	 * @MongoDB\ReferenceMany(targetDocument="City")
	 */
	private $cities;

	public function __construct()
	{
		$this->cities = new ArrayCollection();
	}

	public function addCity(City $city){
		$this->cities[] = $city;
	}

	/**
	 * @return array
	 */
	public function getCitys()
	{
		return $this->cities;
	}

	/**
	 * @param array $cities
	 */
	public function setCities($cities)
	{
		$this->cities = $cities;
	}
}