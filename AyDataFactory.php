<?php
define('AYDF_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
class AyDataFactory extends ArrayObject {
	static private $pluginDir = array();
	static private $loadedPlugin = array();
	static private $bindedPlugin = array();

	public function __construct($data = array()) {
		if (!isset($data)) {
			$data = array();
		} elseif (!is_array($data) && array_key_exists('ArrayAccess', class_implements($data))) {
			$data = array($data);
		}
		parent::__construct($data);
	}

	/**
	 * Clone a new AyDataFactory
	 * 
	 * @access public
	 * @return AyDataFactory
	 */
	public function copy() {
		return new AyDataFactory($this->getArrayCopy());
	}

	/**
	 * Rewrite offsetGet, ignore any warning if missing key
	 * 
	 * @access public
	 * @param int $index
	 * @return mixed
	 */
	public function offsetGet($index) {
		if (array_key_exists($index, $this)) {
			return call_user_func_array('parent::offsetGet', func_get_args()); 
		}
		return null;
	}

	/**
	 * Get the function name and arguments, if 'factory' plugin exists,
	 * excute it.
	 * 
	 * @access public
	 * @param string $name
	 * @param array $args
	 * @return AyDataFactory
	 */
	public function __call($name, $args) {
		if (self::PluginExists('factory', $name)) {
			return self::LoadPlugin('factory', $name, $args, $this);
		}

		// If no plugin was found, return as chain.
		return $this;
	}

	/**
	 * Get the relection closure function that bound with AyDataFactory
	 * 
	 * @access public
	 * @param callback $callback
	 * @return callback
	 */
	public function reflection($callback) {
		return $callback->bindTo($this);
	}

	/**
	 * When object invoked, return AyDataFactoryProcessor
	 * 
	 * @access public
	 * @param string $index
	 * @return AyDataFactoryProcessor
	 */
	public function __invoke($index) {
		if (!array_key_exists($index, $this)) {
			$this[$index] = null;
		}
		return new AyDataFactoryProcessor($this, $index);
	}

	/**
	 * Bind the custom function
	 * 
	 * @access public
	 * @static
	 * @param string $type
	 * @param string $name
	 * @param callback $callback
	 * @return void
	 */
	static public function BindPlugin($type, $name, $callback) {
		$type = trim($type);
		$name = trim($name);
		if ($type && $name && $callback instanceof Closure) {
			$funcname = 'aydf_' . $type . '_' .$name;
			if (!isset(self::$bindedPlugin[$funcname])) {
				self::$bindedPlugin[$funcname] = $callback;
			}
		}
	}

	/**
	 * Add plugin folder path to plugin directory list
	 * 
	 * @access public
	 * @static
	 * @param string $path
	 * @return bool
	 */
	static public function AddPluginDir($path) {
		// Put the default plugin folder into plugin directory list if there is
		// no plugin folder has been added
		if (empty(self::$pluginDir)) {
			self::$pluginDir[AYDF_DIR . 'aydf_plugins' . DIRECTORY_SEPARATOR] = true;
		}

		$path = trim($path);
		if ($path) {
			$path = rtrim(preg_replace('/[\/\\\]+/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);

			// Identify the path is relationship path from Template Class location or not
			if ($path[0] == '.' || $path[0] == DIRECTORY_SEPARATOR) {
				if (is_dir($path)) {
					self::$loadedPlugin = array();
					self::$pluginDir[$path] = true;
					return true;
				}
			} else {
				$relatedPath = AYDF_DIR . $path . DIRECTORY_SEPARATOR;
				if (is_dir($relatedPath)) {
					self::$loadedPlugin = array();
					self::$pluginDir[$relatedPath] = true;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check the plugin function is exists or not
	 * 
	 * @access public
	 * @static
	 * @param string $type
	 * @param string $funcname
	 * @return bool
	 */
	static public function PluginExists($type, $funcname) {
		if (empty(self::$pluginDir)) {
			self::$pluginDir[AYDF_DIR . 'aydf_plugins' . DIRECTORY_SEPARATOR] = true;
		}

		$pluginFileName = $type . '.' . $funcname . '.php';
		$functionName = 'aydf_' . $type . '_' . $funcname;

		if (isset(self::$bindedPlugin[$functionName])) {
			return true;
		} elseif (!isset(self::$loadedPlugin[$functionName])) {
			foreach (self::$pluginDir as $pluginDir => $var) {
				// If the plugin file exists
				if (file_exists($pluginDir . $pluginFileName)) {
					include $pluginDir . $pluginFileName;
					// If the plugin function still not be found after the plugin file was loaded
					// Mark the plugin function as failed.
					if (!function_exists($functionName)) {
						self::$loadedPlugin[$functionName] = false;
						return false;
					} else {
						self::$loadedPlugin[$functionName] = true;
						return true;
					}
				}
			}

			// Until all plugin folder has searched, it the plugin function still not be marked
			// Mark the plugin function as failed.
			if (!isset(self::$loadedPlugin[$functionName])) {
				self::$loadedPlugin[$functionName] = false;
				return false;
			}
		}
		return true;
	}

	/**
	 * Load plugin function from plugin directory list
	 * If the plugin function not found or cannot be loaded, it will mark
	 * the plugin function as loaded and no longer be searched
	 * 
	 * @access public
	 * @static
	 * @param string $type
	 * @param string $funcname
	 * @param string $value
	 * @param array $parameters
	 * @return mixed
	 */
	static public function LoadPlugin($type, $funcname, $parameters, $data) {
		if (empty(self::$pluginDir)) {
			self::$pluginDir[AYDF_DIR . 'aydf_plugins' . DIRECTORY_SEPARATOR] = true;
		}
		$pluginFileName = $type . '.' . $funcname . '.php';
		$functionName = 'aydf_' . $type . '_' . $funcname;

		// If plugin function not marked as loaded
		if (isset(self::$bindedPlugin[$functionName])) {
			$functionName = new ReflectionFunction(self::$bindedPlugin[$functionName]);
		} elseif (!isset(self::$loadedPlugin[$functionName])) {
			foreach (self::$pluginDir as $pluginDir => $var) {
				// If the plugin file exists
				if (file_exists($pluginDir . $pluginFileName)) {
					include $pluginDir . $pluginFileName;
					// If the plugin function still not be found after the plugin file was loaded
					// Mark the plugin function as failed.
					if (!function_exists($functionName)) {
						self::$loadedPlugin[$functionName] = false;
						return '';
					} else {
						self::$loadedPlugin[$functionName] = true;
						break;
					}
				}
			}

			// Until all plugin folder has searched, it the plugin function still not be marked
			// Mark the plugin function as failed.
			if (!isset(self::$loadedPlugin[$functionName])) {
				self::$loadedPlugin[$functionName] = false;
				return '';
			}
		} elseif (!self::$loadedPlugin[$functionName]) {
			// If the plugin function has marked as failed, ignore it.
			return '';
		}

		if (is_string($functionName)) {
			$functionName = new ReflectionFunction($functionName);
		}
		$functionName = $functionName->getClosure();
		return call_user_func_array($data->reflection($functionName), $parameters);
	}
}

class AyDataFactoryProcessor {
	private $source = null;
	private $index = '';

	public function __construct($source, $index) {
		$this->source = $source;
		$this->index = $index;
	}

	/**
	 * Clone the source when cloning the AyDataFactoryProcessor .
	 * 
	 * @access public
	 * @return void
	 */
	public function __clone() {
		$this->source = clone $this->source;
	}

	/**
	 * Get the function name and arguments, if the data type plugin is exists,
	 * execute the plugin, either or an 'any' data type function.
	 * 
	 * @access public
	 * @param string $name
	 * @param array $args
	 * @return AyDataFactoryProcessor
	 */
	public function __call($name, $args) {
		// Get the data type
		$type = gettype($this->get());
		$type = strtolower($type);
		switch ($type) {
			case 'boolean':
			case 'integer':
			case 'double':
			case 'string':
			case 'array':
				break;
			case 'resource':
				// If the data is a resource, get the resource type
				$type = strtolower(preg_replace('/[^0-9a-z]+/i', '_', get_resource_type($this->get())));
				break;
			case 'object':
				// If object implemented ArrayAccess, change the type as 'array'
				if (array_key_exists('ArrayAccess', class_implements($this->get()))) {
					$type = 'array';
				} else {
					// If the data is an object, get the class name
					$type = get_class($this->get());
				}
				break;
			default:
				$type = 'any';
				break;
		}

		// If data type plugin exists, execute it, else try to find the 'any' data
		// type plugin.
		if (AyDataFactory::PluginExists($type, $name)) {
			return AyDataFactory::LoadPlugin($type, $name, $args, $this);
		} elseif (AyDataFactory::PluginExists('any', $name)) {
			return AyDataFactory::LoadPlugin('any', $name, $args, $this);
		}

		// If no plugin was found, return as chain.
		return $this;
	}

	/**
	 * Set the data value
	 * 
	 * @access public
	 * @param mixed $value
	 * @return mixed
	 */
	public function set($value) {
		if ($value instanceof Closure) {
			$this->source[$this->index] = $value($this->source[$this->index]);
		} else {
			$this->source[$this->index] = $value;
		}
	}

	/**
	 * Get the data value
	 * 
	 * @access public
	 * @return mixed
	 */
	public function get($callback = null) {
		if ($callback instanceof Closure) {
			$data = clone $this;
			$result = call_user_func($data->reflection($callback));
			if ($data === $result) {
				return $result->get();
			}
			return $result;
		} else {
			return $this->source[$this->index];
		}
	}

	/**
	 * Get the relection closure function that bound with AyDataFactoryProcessor
	 * 
	 * @access public
	 * @param callback $callback
	 * @return callback
	 */
	public function reflection($callback) {
		return $callback->bindTo($this);
	}
}
?>