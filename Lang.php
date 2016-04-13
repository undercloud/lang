<?php
	namespace Undercloud;

	use Undercloud\Lang\LangIterator;
	use Undercloud\Lang\Storage;
	use Undercloud\Lang\TranslateNotFound;
	use Undercloud\Lang\Parser\DefaultParser;

	require_once __DIR__ . '/Storage.php';
	require_once __DIR__ . '/LangIterator.php';
	require_once __DIR__ . '/TranslateNotFound.php';
	require_once __DIR__ . '/Parser/AbstractParser.php';
	require_once __DIR__ . '/Parser/DefaultParser.php';
	require_once __DIR__ . '/Parser/JsonParser.php';

	class Lang
	{
		private $options = array();
		private $iterator;
		private $storage;

		public function __construct(array $options = array())
		{
			if (false == isset($options['accept'])) {
				$options['accept'] = $this->getLangsHttp();
			}

			if (false == isset($options['avail'])) {
				$options['avail'] = array();
			}

			$options['lang'] = array_values(
				array_intersect(
					$options['accept'],
					$options['avail']
				)
			);

			$this->options = $options;

			$this->iterator = new LangIterator($options['lang']);

			$this->storage = new Storage();

			if (isset($options['root'])) {
				$this->storage->setRoot($options['root']);
			}

			$this->storage->setParser(
				isset($options['parser'])
				? $options['parser']
				: (new DefaultParser)
			);
		}

		public function getLangsHttp(array $default = array('en'))
		{
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

				$langs = preg_replace('~;q=[0-9].[0-9]~i', '', $langs);
				$langs = strtolower($langs);
				$langs = explode(',', $langs);

				if (!$langs) {
					return $default;
				}

				return $langs;
			} else {
				return $default;
			}
		}

		public function getPrimaryLocale()
		{
			return reset($this->options['lang']);
		}

		public function getFallBackLocale()
		{
			return end($this->options['lang']);
		}

		public function assign($message, array $placeholders = array())
		{
			$index = 0;
			foreach ($placeholders as $key => $value) {
				$index++;

				if (false == is_scalar($value)) {
					if (is_array($value)) {
						$placeholders[$key] = implode(', ', array_filter($value, function($item) {
							return is_scalar($item);
						}));
					} else {
						$placeholders[$key] = null;
					}
				}

				$message = str_replace(':' . $key, '%' . $index . '$s', $message);
			}

			return vsprintf($message, array_values($placeholders));
		}

		public function __invoke($message, array $assoc = array())
		{
			return $this->get($message, $assoc);
		}

		public function get($message, array $assoc = array())
		{
			$current = $this->iterator->current();

			$value = $this->storage->load($current, $message);

			if ($value instanceof TranslateNotFound) {
				$this->iterator->next();

				if ($this->iterator->valid()) {
					return $this->get($message, $assoc);
				} else {
					return;
				}
			} else {
				$this->iterator->rewind();
				$value = $this->assign($value, $assoc);

				return $value;
			}
		}
	}
?>