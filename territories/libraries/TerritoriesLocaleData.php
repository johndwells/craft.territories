<?php
namespace Craft;

class TerritoriesLocaleData extends \CLocale
{
	/**
	 * Returns the instance of the specified locale. Since the constructor of CLocale is protected, you can only use
	 * this method to obtain an instance of the specified locale.
	 *
	 * @param  string $id The locale ID (e.g. en_US)
	 * @return LocaleData The locale instance
	 */
	public static function getInstance($id)
	{
		static $locales = array();

		if (isset($locales[$id]))
		{
			return $locales[$id];
		}
		else
		{
			return $locales[$id] = new TerritoriesLocaleData($id);
		}
	}

	public function getTerritories()
	{
		return $this->_data['territories'];
	}
}