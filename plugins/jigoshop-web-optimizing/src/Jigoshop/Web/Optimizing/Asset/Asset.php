<?php

namespace Jigoshop\Web\Optimizing\Asset;

use Assetic\Asset\AssetCache;
use Assetic\AssetWriter;
use Assetic\Cache\FilesystemCache;
use Assetic\Factory\AssetFactory;
use Jigoshop\Web\Optimizing\Assetic\Writer;

/**
 * Abstract class for asset creation.
 *
 * @package Jigoshop\Web\Optimizing
 */
abstract class Asset
{
	private $factory;
	private $assets;
	private $values;

	public function __construct(AssetFactory $factory, array $assets, array $values)
	{
		$this->factory = $factory;
		$this->assets = $assets;
		$this->values = $values;
	}

	/**
	 * @return AssetCache Prepared cached asset for assigned assets.
	 */
	public function getAsset()
	{
		$writer = new Writer(JIGOSHOP_WEB_OPTIMIZING_DIR.'/cache', array_map(function($item){
			return array($item);
		}, $this->values));

		$asset =  new AssetCache(
			$this->factory->createAsset(
				$this->assets,
				$this->getAssetFilters(),
				array(
					'output' => $this->getNamePattern(),
					'vars' => $this->getAssetVariables(),
				)
			),
			new FilesystemCache(JIGOSHOP_WEB_OPTIMIZING_DIR.'/cache')
		);
		$asset->setValues($this->values);
		$writer->writeAsset($asset);

		return $asset;
	}

	/**
	 * Returns pattern used for naming the asset.
	 *
	 * @return string Naming pattern.
	 */
	abstract protected function getNamePattern();

	/**
	 * Returns list of available variable names for the asset.
	 *
	 * All variables used in {@link getAssetOutput()) must be declared here.
	 *
	 * @return array List of variable names.
	 */
	abstract protected function getAssetVariables();

	/**
	 * Returns list of applicable filters for the asset.
	 *
	 * Available filters:
	 *   * cssmin - minimizes CSS using CssMin library
	 *   * jsmin - minimizes JS using JSMinPlus library
	 *
	 * @return array List of filters.
	 */
	abstract protected function getAssetFilters();
}