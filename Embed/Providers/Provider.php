<?php
/**
 * Abstract class with basic functions to all providers (data store, load content, etc)
 */
namespace Embed\Providers;

class Provider {
	protected $parameters = array();

	
	/**
	 * Save a value
	 * 
	 * @param string $name Name of the value
	 * @param string $value The value to save
	 */
	public function set ($name, $value = null) {
		if (is_array($name)) {
			$this->parameters = array_replace($this->parameters, $name);
		} else {
			$this->parameters[trim($name)] = is_string($value) ? trim($value) : $value;
		}
	}


	/**
	 * Get a value or null if not exists
	 * 
	 * @param string $name Value name
	 * @param string $subname A subvalue name
	 * 
	 * @return string/null
	 */
	public function get ($name = null, $subname = null) {
		if ($name === null) {
			return $this->parameters;
		}

		if ($subname === null) {
			return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
		}
		
		if (!isset($this->parameters[$name][$subname])) {
			return null;
		}

		return $this->parameters[$name][$subname];
	}


	/**
	 * Check if a value exists
	 * 
	 * @param string $name Value name
	 * 
	 * @return boolean True if exists, false if not
	 */
	public function has ($name) {
		return isset($this->parameters[$name]);
	}


	/**
	 * Specific get values
	 */
	public function __call ($name, $arguments) {
		return null;
	}
}
?>
