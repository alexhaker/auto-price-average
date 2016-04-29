<?php
namespace AutoRIABundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

trait GroupableTrait {
	/**
	 * @MongoDB\Int
	 */
	private $group;

	/**
	 * @return int
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @param int $group
	 */
	public function setGroup($group)
	{
		$this->group = $group;
	}
}