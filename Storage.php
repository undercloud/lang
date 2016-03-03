<?php
	namespace Undercloud\Lang;

	class Storage
	{
		private $root;
		private $parser;
		private $stack = array();

		public function setRoot($root)
		{
			$this->root = $root;

			return $this;
		}

		public function setParser($parser)
		{
			$this->parser = $parser;

			return $this;
		}

		public function load($locale, $message)
		{
			$keys   = explode('.', $message);
			$entity = array_shift($keys);
			
			if (false == isset($this->stack[$locale][$entity])) {
				$arguments = array();

				if (property_exists($this->parser, 'buildPath') and false === $this->parser->buildPath) {
					$arguments[] = $locale;
					$arguments[] = $entity;
				} else {
					$arguments[] = $this->root . '/' . str_replace('.', '/', $locale) . '/' . $entity;
				}

				$this->stack[$locale][$entity] = call_user_func_array(array($this->parser, 'parse'), $arguments);
			}

			$array = &$this->stack[$locale][$entity]; 

			foreach ($keys as $key) {
				if (isset($array[$key])) {
					$array = $array[$key];
				} else {
					return new TranslateNotFound;
				}
			}

			return $array;
		}
	}
?>