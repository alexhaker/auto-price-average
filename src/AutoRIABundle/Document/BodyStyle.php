<?php
namespace AutoRIABundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
*/
class BodyStyle extends Dictionary
{
	use GroupableTrait;
}