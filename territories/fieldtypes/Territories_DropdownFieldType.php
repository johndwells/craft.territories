<?php
namespace Craft;

class Territories_DropdownFieldType extends BaseOptionsFieldType
{
	private $_options;
	private $_promotedOptions;
	private $_availableOptions;
	private $_territories;

	const DIVIDER = '----------------';

    /**
     * Fieldtype name
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Territories - Dropdown');
    }
    
	/**
	 * Returns the label for the Options setting.
	 *
	 * @access protected
	 * @return string
	 */
	protected function getOptionsSettingsLabel()
	{
		return Craft::t('Territories - Dropdown Options');
	}

    /**
     * Define database column
     *
     * @return AttributeType::String
     */
    public function defineContentAttribute()
    {
        return array(AttributeType::String);
    }
    
	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
	
		$settings['promoted'] = array(AttributeType::Mixed, 'default' => array('' => Craft::t('None')));
		$settings['available'] = array(AttributeType::Mixed, 'default' => array('' => Craft::t('All')));
		$settings['options'] = array(AttributeType::Mixed, 'default' => array());

		return $settings;
	}

	/**
	 * Returns the field's settings HTML.
	 *
	 * @return string|null
	 */
	public function getSettingsHtml()
	{		
		$entryElementType = craft()->elements->getElementType(ElementType::Entry);
		$assetElementType = craft()->elements->getElementType(ElementType::Asset);

		$territories = $this->getTerritories();

		// add ability to select "all"
		$allTerritories = array_merge(array('' => Craft::t('All'), '--' => self::DIVIDER), $territories);

		// add ability to select "none"
		$availableTerritories = array_merge(array('' => Craft::t('None'), '--' => self::DIVIDER), $territories);
	
		return craft()->templates->render('territories/_fieldtype/settings', array(
			'settings' => $this->getSettings(),
			'availableTerritories' => $availableTerritories,
			'allTerritories' => $allTerritories
		));
	}

    /**
     * Display our fieldtype
     *
     * @param string $name  Our fieldtype handle
     * @return string Return our fields input template
     */
    public function getInputHtml($name, $value)
    {
    	$settings = $this->getSettings();
    	$territories = $this->getTerritories();

    	// build our dropdown
    	$options = array('' => '');

    	// build the "promoted" section if it exists and does not only contain an empty (e.g. "None") selection
    	if($promotedOptions = $this->_getPromotedOptions())
    	{
			foreach($promotedOptions as $id => $val)
			{
	    		if(array_key_exists($id, $territories))
	    		{
					$options[$id] = $territories[$id];
	    		}
			}

			// add our visual divider
			$options['--'] = self::DIVIDER;
    	}


		foreach($this->_getAvailableOptions() as $id => $val)
		{
			if(array_key_exists($id, $territories) && ! array_key_exists($id, $options))
			{
				$options[$id] = $territories[$id];
			}
		}

		// sanity check - if a person as selected every available option to also be "promoted",
		// then the last item will be our dashes.
		if(end($options) == self::DIVIDER)
		{
			unset($options['--']);
		}

		// Render Field
    	return craft()->templates->render('territories/_fieldtype/input', array(
            'name'  => $name,
            'value' => $value,
            'options' => $options
        ));        
    }

    /**
     * Sanitise our settings
     *
     * @param $settings Array
     * @return Array
     */
    public function prepSettings($settings)
    {
    	if($settings['available'])
    	{
	    	foreach($settings['available'] as $key => $value)
	    	{
				// selecting our dividing line is not allowed, so let's remove
	    		if($value == '--')
	    		{
	    			unset($settings['available'][$key]);
	    		}
	    	}
    	}

    	//empty or unset array?
    	if( ! $settings['available'])
    	{
    		$settings['available'] = array('');
    	}

    	if($settings['promoted'])
    	{
	    	foreach($settings['promoted'] as $key => $val)
	    	{
				// selecting our dividing line is not allowed, so let's remove
	    		if($val == '--')
	    		{
	    			unset($settings['promoted'][$key]);
	    		}
	    	}
		}

    	//empty?
    	if( ! $settings['promoted'])
    	{
    		$settings['promoted'] = array('');
    	}

    	return $settings;
    }

    /**
     * Return an array of "promoted" territories, as configured in settings
     *
     * @return Array
     */
    protected function _getPromotedOptions()
    {
		if ( ! isset($this->_promotedOptions))
		{
			$settings = $this->getSettings();
			$availableOptions = $this->_getAvailableOptions();

	    	$this->_promotedOptions = array();
	    	foreach($settings->promoted as $id)
	    	{
	    		if(array_key_exists($id, $availableOptions))
	    		{
	    			$this->_promotedOptions[$id] = $availableOptions[$id];
	    		}
	    	}
		}

		return $this->_promotedOptions;
    }

    /**
     * Return an array of "available" territories, as configured in settings
     *
     * @return Array
     */
    protected function _getAvailableOptions()
    {
		if ( ! isset($this->_availableOptions))
		{
	    	$settings = $this->getSettings();
	    	$territories = $this->getTerritories();

	    	$this->_availableOptions = array();
	    	foreach($settings->available as $id)
	    	{
	    		if(array_key_exists($id, $territories) && ! array_key_exists($id, $this->_availableOptions))
	    		{
	    			$this->_availableOptions[$id] = $territories[$id];
	    		}
	    	}

	    	// if empty, set to $territories
	    	if( ! $this->_availableOptions)
	    	{
	    		$this->_availableOptions = $territories;
	    	}
	    }

    	return $this->_availableOptions;
    }

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

    /**
     * On the front-end returns a list of all available options configured for the fieldtype
     *
     * @return array
     */
    public function getOptions()
    {
		if (!isset($this->_options))
		{
			$this->_options = array();

			foreach($this->_getAvailableOptions() as $key => $option)
			{
				$this->_options[] = array('label' => $option, 'value' => $key, 'default' => '');
			}

		}
		return $this->_options;

    }

    /**
     * Given a territory key, returns the label
     *
     * @param $value String
     * @return String
     */
    public function getOptionLabel($value)
    {
    	$territories = $this->getTerritories();

		if(array_key_exists($value, $territories))
		{
			return $territories[$value];
		}

		return '';
    }

	/**
	 * Returns 'true' or any custom validation errors.
	 *
	 * @param array $value
	 * @return true|string|array
	 */
	public function validate($value)
	{
		$errors = array();
		
		$allowed = $this->_getAvailableOptions() ?: $this->getTerritories();

		if( ! array_key_exists($value, $allowed))
		{
			$errors[] = Craft::t('That territory is not available for selection.');

			return $errors;
		}

		return true;
	}
}
