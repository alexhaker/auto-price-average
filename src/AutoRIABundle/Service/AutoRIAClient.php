<?php
namespace AutoRIABundle\Service;

use Buzz\Browser;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;
use AutoRIABundle\Document;

/**
 * Client service for Auto RIA API
 * @package AutoRIABundle\Service
 */
class AutoRIAClient {
	const DOCUMENTS_NAMESPACE = 'AutoRIABundle\\Document\\';
	
	/**
	 * API host
	 * @var string
	 */
	private $host;

	/**
	 * List of endpoints
	 * @var array
	 */
	private $endpoints;

	/**
	 * Mapping array: key - endpoint name, value - document class name
	 * @var array
	 */
	private $modelMapping;

	/**
	 * Buzz browser instance
	 * @var Browser
	 */
	private $buzzBrowser;

	/**
	 * Monolog logger
	 * @var Logger
	 */
	private $logger;

	/**
	 * Doctrine Object Manager
	 * @var ObjectManager
	 */
	private $doctrineManager;

	public function __construct(array $apiSettings, Browser $buzzBrowser, Logger $logger, ManagerRegistry $doctrineMongo)
	{
		$this->host = $apiSettings['host'];
		$this->endpoints['dictionaries'] = $apiSettings['dictionaries_endpoints'];
		$this->modelMapping = $apiSettings['model_mapping'];
		$this->buzzBrowser = $buzzBrowser;
		$this->logger = $logger;
		$this->doctrineManager = $doctrineMongo->getManager();
	}

	public function syncDictionaries()
	{
		foreach ($this->endpoints['dictionaries'] as $dictionaryName => $dictionaryOptions) {
			$dictionaryItems = $this->_fetchDictionary($dictionaryName, $dictionaryOptions);
			foreach ($dictionaryItems as $dictionary) {
				$this->doctrineManager->persist($dictionary);
			}
			$this->doctrineManager->flush();
			$this->doctrineManager->clear();
		}
	}

	/**
	 * @param string $dictionaryName
	 * @param array  $dictionaryOptions
	 * @param string|null $parentEndpoint
	 * @param int|null $parentId
	 * @throws \Exception
	 *
	 * @return array Array of Document\Dictionary
	 */
	private function _fetchDictionary($dictionaryName, $dictionaryOptions, $parentEndpoint = null, $parentId = null)
	{
		$this->logger->debug('Fetch dictionary', array(
			'dictionaryName' => $dictionaryName,
			'parentEndpoint' => $parentEndpoint,
			'parentId' => $parentId
		));
		$dictionaryItems = array();

		if (is_string($dictionaryOptions)) {
			$dictionaryOptions = array('self' => $dictionaryOptions);
		}

		if (is_array($dictionaryOptions)) {
			if (!isset($dictionaryOptions['self'])) {
				throw new \Exception('Dictionary '.$dictionaryName.' must contain self option with endpoint URL');
			}
			$endpoint = $this->_getEndpoint($dictionaryOptions, $parentEndpoint, $parentId);
			unset($dictionaryOptions['self']);

			if (isset($this->modelMapping[$dictionaryName])) {
				$className = self::DOCUMENTS_NAMESPACE . $this->modelMapping[$dictionaryName];
				$dictionaryData = $this->_fetchDictionaryData($endpoint);
				if (is_null($dictionaryData)) {
					$endpoint = str_replace('/_group', '', $endpoint); //Hack for API errors
					$dictionaryData = $this->_fetchDictionaryData($endpoint);
				}
				if (!is_null($dictionaryData) && is_array($dictionaryData)) {
					if (in_array('AutoRIABundle\Document\GroupableTrait', class_uses($className))) {
						$dictionaryData = $this->_assignGroupValues($dictionaryData);
					}
					foreach ($dictionaryData as $dictionaryItem) {
						$dictionary = $this->_getDictionaryObject($dictionaryItem, $className);

						if (count($dictionaryOptions) > 0) {
							foreach ($dictionaryOptions as $parentDictionaryName => $parentDictionaryOptions) {
								if (isset($this->modelMapping[$parentDictionaryName]) ) {
									$parentClassName = $this->modelMapping[$parentDictionaryName];
									$parentAddMethod = 'add'.$parentClassName;
									$parentGetMethod = 'get'.$parentClassName.'s';
									if (!method_exists($dictionary, $parentAddMethod)) {
										throw new \Exception('Method "'.$parentAddMethod.'" is not exists in '.get_class($dictionary).' class');
									}
									$parentDictionaryItems = $this->_fetchDictionary(
										$parentDictionaryName,
										$parentDictionaryOptions,
										$endpoint,
										$dictionary->getValue()
									);
									/** @var Document\Dictionary $parentDictionary */
									foreach ($parentDictionaryItems as $parentDictionary) {
										$parentDBItems = $dictionary->$parentGetMethod();
										if ($parentDictionary instanceof Document\Dictionary
											&& !$parentDBItems->contains($parentDictionary)
										) {
											$dictionary->$parentAddMethod($parentDictionary);
										}
									}
								} else {
									$this->logger->warning('Model mapping is not set for dictionary = '.$parentDictionaryName);
								}
							}
						}

						$dictionaryItems[] = $dictionary;
//						if (!isset($parentEndpoint, $parentId)) {
//							$this->doctrineManager->flush();
//							$this->doctrineManager->clear();
//						}
					}
				}
			} else {
				$this->logger->warning('Model mapping is not set for dictionary = '.$dictionaryName);
			}
		}

		return $dictionaryItems;
	}

	/**
	 * Get endpoint API URL for dictionary
	 * @param $dictionaryOptions
	 * @param $parentEndpoint
	 * @param $parentId
	 * @return string
	 */
	private function _getEndpoint($dictionaryOptions, $parentEndpoint, $parentId)
	{
		if (is_null($parentEndpoint)) {
			$endpoint = $this->host.$dictionaryOptions['self'];
		} else {
			$endpoint = $parentEndpoint.'/'.$parentId.$dictionaryOptions['self'];
		}

		return $endpoint;
	}

	/**
	 * @param array $groups Data with groups array
	 *
	 * @return array List of dictionary objects
	 */
	private function _assignGroupValues(array $groups)
	{
		$result = array();
		$groupId = 0;
		foreach ($groups as $group) {
			$groupId++;
			if (is_array($group)) {
				foreach ($group as $itemObject) {
					$itemObject->group = $groupId;
					$result[] = $itemObject;
				}
			} elseif (is_object($group)) {
				$group->group = $groupId;
				$result[] = $group;
			}
		}

		return $result;
	}

	/**
	 * @param \stdClass $dictionaryItem dictionary item data array
	 * @param string $className name of class that needs to return
	 *
	 * @return Document\Dictionary
	 */
	private function _getDictionaryObject(\stdClass $dictionaryItem, $className)
	{
		$repository = $this->doctrineManager->getRepository($className);
		/** @var Document\Dictionary $dictionaryObject */
		$dictionaryObject = $repository->findOneBy(array(
			'name' => $dictionaryItem->name,
			'value' =>$dictionaryItem->value
		));
		if (empty($dictionaryObject)) {
			/** @var Document\Dictionary $dictionaryObject */
			$dictionaryObject = new $className();
			$this->doctrineManager->persist($dictionaryObject);
		}
				
		$dictionaryObject->setName($dictionaryItem->name);
		$dictionaryObject->setValue($dictionaryItem->value);
		if (in_array('AutoRIABundle\Document\GroupableTrait', class_uses($className))) {
			$dictionaryObject->setGroup($dictionaryItem->group);
		}

		return $dictionaryObject;
	}

	/**
	 * @param string $endpoint Endpoint API URL
	 *
	 * @return null|array result data array or null if error occurred
	 */
	private function _fetchDictionaryData($endpoint)
	{
		$result = null;
		try {
			$response = $this->buzzBrowser->get($endpoint);
			$code = $response->getStatusCode();
			if ($code === 200) {
				$result = json_decode($response->getContent());
			} else {
				$this->logger->error('Failed to fetch data', array(
					'statusCode' => $code,
					'endpoint' => $endpoint
				));
			}
		} catch	(\Exception $e) {
			$this->logger->error($e->getMessage());
		}

		return $result;
	}
}