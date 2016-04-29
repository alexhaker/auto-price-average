<?php

namespace AutoRIABundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface {
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode    = $treeBuilder->root('auto_ria');

		$rootNode
			->children()
				->arrayNode('api')
					->isRequired()
					->children()
						->scalarNode('host')
							->isRequired()
						->end()
						->variableNode('dictionaries_endpoints')
							->isRequired()
						->end() //dictionaries_endpoints
						->variableNode('model_mapping')
					->end()
				->end() //api
			->end()
		;

		return $treeBuilder;
	}
}
