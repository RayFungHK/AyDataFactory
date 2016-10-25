<?php
define('AYDF_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

function aydf_getname($type, $funcname) {
	return array(
		'filename' => $type . '.' . $funcname . '.php',
		'funcname' => 'aydf_' . $type . '_' . $funcname
	);
}

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
		return self::LoadPlugin('factory', $name, $args, $this);
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
	static private function GetPluginFunction($type, $funcname) {
		if (empty(self::$pluginDir)) {
			self::$pluginDir[AYDF_DIR . 'aydf_plugins' . DIRECTORY_SEPARATOR] = true;
		}

		$datatypeName = aydf_getname($type, $funcname);
		$anyName = aydf_getname('any', $funcname);

		if (function_exists($datatypeName['funcname']) || (isset(self::$loadedPlugin[$datatypeName['funcname']]) && self::$loadedPlugin[$datatypeName['funcname']])) {
			return $datatypeName['funcname'];
		} elseif (function_exists($anyName['funcname']) || (isset(self::$loadedPlugin[$anyName['funcname']]) && self::$loadedPlugin[$anyName['funcname']])) {
			return $anyName['funcname'];
		}

		foreach (self::$pluginDir as $pluginDir => $var) {
			// If the plugin file exists
			foreach (array($datatypeName, $anyName) as $name) {
				if (file_exists($pluginDir . $name['filename'])) {
					include $pluginDir . $name['filename'];

					if (function_exists($name['funcname'])) {
						self::$loadedPlugin[$name['funcname']] = true;
						return $name['funcname'];
					}
				}
			}
		}

		self::$loadedPlugin[$datatypeName['funcname']] = false;
		return '';
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

		$datatypeName = aydf_getname($type, $funcname);
		$anyName = aydf_getname('any', $funcname);

		if (isset(self::$bindedPlugin[$datatypeName['funcname']])) {
			// DataType binded function
			$closureFunction = self::$bindedPlugin[$datatypeName['funcname']];
		} elseif (isset(self::$bindedPlugin[$anyName['funcname']])) {
			// 'Any' datatype binded function
			$closureFunction = self::$bindedPlugin[$anyName['funcname']];
		} else {
			if (!$closureFunction = self::GetPluginFunction($type, $funcname)) {
				return '';
			}
		}

		// Reflect the function and get a new closure function, re-bind as AyDataFactoryProcessor
		$closureFunction = new ReflectionFunction($closureFunction);
		return call_user_func_array($data->reflection($closureFunction->getClosure()), $parameters);
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
		return AyDataFactory::LoadPlugin($type, $name, $args, $this);
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
