<?php
namespace AutoRIABundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Brand extends Dictionary
{
	/**
	 * @MongoDB\ReferenceMany(targetDocument="Model")
	 */
	private $models = array();

	/**
	 * @return array
	 */
	public function getModels()
	{
		return $this->models;
	}

	/**
	 * @param array $models
	 */
	public function setModels($models)
	{
		$this->models = $models;
	}

	public function addModel(Model $model)
	{
		$this->models[] = $model;
	}
}