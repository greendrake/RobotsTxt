# RobotsTxt
A simple class for parsing robots.txt files and telling whether certain paths are allowed for certain user agents

## Usage

```php
<?php

// Get the class
require __DIR__ . '/path/to/RobotsTxt.php';

// Instantiate
$r = new DrakeES\RobotsTxt();

// Get the content as a string...
$r->setRobotsTxt('
User-agent: UA
User-agent: Googlebot
User-agent: Moz
Allow: /
Disallow: /private/
Disallow: /secret/page.html
');

// ... or from a file
$r->setRobotsTxt(file_get_contents('http://example.com/robots.txt');

// Set the user agent
$r->setUserAgent('Googlebot');

// Check if the bot is allowed
$r->isAllowed('/public/page.html'); // returns true
$r->isAllowed('/private/page.html'); // returns true
$r->isAllowed('/secret/page.html'); // returns false
$r->isAllowed('/public/private/page.html'); // returns true

```

