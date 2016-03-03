<?php
	namespace Undercloud\Lang\Parser;

	use Exception;

	class DefaultParser extends AbstractParser
	{
		public function parse()
		{
			$path = func_get_arg(0) . '.php';

			if (file_exists($path)) {
				if (@is_readable($path)) {
					return require($path);
				} else {
					throw new Exception(
						sprintf('Cannot read file %s', $path)
					);
				}
			} else {
				return array();
			}
		}
	}
?>