#Lang
PHP Multilanguage System

##install
`composer require undercloud/lang`

##translates
```PHP
// /en/message.php
return [
	'hello' => 'Hello, :username',
	'hobbies' => 'Your hobbies is: :hobbies'
];
```

##setup
```PHP
require 'vendor/autoload.php';

$lang = new Undercloud\Lang([
	// translates path
	'root' => '/path/to/translates',
	// list of user accepted, default parse http header Accept-Language
	'accept' => ['en', 'de'],
	// list of supported translates
	'avail' => ['en','fr','de'],
	// setup translates file handler, default PHP Arrays
	'parser' => new Undercloud\Lang\Parser\AbstractParser
]);

// Hello, John
$lang('message.hello', ['username' => 'John']);

//Your hobbies is: music, football, web
$lang('message.hobbies', ['hobbies': ['music', 'football', 'web']]);
```

##api
```PHP
// parse Accept-Language header and return supported locales
$lang->getLangsHttp();

// return primary locale name
$lang->getPrimaryLocale();

// return fallback locale
$lang->getFallBackLocale();
```

##parser
By default avail two type of language files parsers `Undercloud\Lang\Parser\DefaultParser` and `Undercloud\Lang\Parser\JsonParser`,
You can define you'r own parser:
```PHP
// file based parser
use Undercloud\Lang\Parser\AbstractParser;

class MyOwnParser extends AbstractParser
{
	public function parse()
	{
		$path = func_get_arg(0) . '.extension';
		/*...*/
	}
}
```
or
```PHP
// database like parser
use Undercloud\Lang\Parser\AbstractParser;

class MyOwnParser extends AbstractParser
{
	public $buildPath = false;

	public function parse()
	{
		$locale = func_get_arg(0);
		$entity = func_get_arg(1);

		/*...*/
	}
}
```