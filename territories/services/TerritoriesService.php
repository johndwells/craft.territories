<?php
namespace Craft;

/**
 * TerritoriesService
 */
class TerritoriesService extends BaseApplicationComponent
{

	private $_territories;

	/**
	 * Return an array of available territories
	 *
	 * @return array
	 */
	public function getTerritories()
	{
		if( ! isset($this->_territories))
		{
			Craft::import('plugins.territories.libraries.TerritoriesLocaleData');
			$locale = TerritoriesLocaleData::getInstance(craft()->locale->getId());

			$this->_territories = array();
			foreach($locale->getTerritories() as $key => $val)
			{
				// skip non-integer keys
				if( ! is_numeric($key))
				{
					$this->_territories[$key] = $val;
				}
			}
		}

		return $this->_territories;
	}

}