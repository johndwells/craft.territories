<?php
namespace Craft;

/**
 * TerritoriesVariable
 */
class TerritoriesVariable
{

	/**
	 * Return an array of available territories from the TerritoriesService
	 *
	 * @return array
	 */
	function getTerritories()
	{
		return craft()->territories->getTerritories();
	}

}