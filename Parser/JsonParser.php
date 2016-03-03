<?php
	namespace Undercloud\Lang\Parser;

	use Exception;

	class JsonParser extends AbstractParser
	{
		public function parse()
		{
			$path = func_get_arg(0) . '.json';

			if (file_exists($path)) {
				$raw = @file_get_contents($path);

				if (false === $raw) {
					throw new Exception(
						sprintf('Cannot read file %s', $path)
					);
				}

				$json  = json_decode($raw, true);
				$error = json_last_error();

				if (JSON_ERROR_NONE != $error) {
					throw new Exception(
						sprintf(
							'Error parsing JSON file %s with code %s',
							$path,
							$error
						)
					);
				}

				return $json;
			} else {
				return array();
			}
		}
	}
?>