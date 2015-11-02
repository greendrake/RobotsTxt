# RobotsTxt
A simple class for parsing robots.txt files and telling whether certain paths are allowed for certain user agents

## Installation

Here is an example how to install it using Composer:

```json
"minimum-stability": "dev",
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/DrakeES/RobotsTxt"
    }
],
"require": {
    "drakees/robotstxt": "dev-master"
}
```

## Usage

```php
<?php

// If not using Composer - include the class directly:
require __DIR__ . '/path/to/RobotsTxt.php';

// Instantiate
$r = new \DrakeES\RobotsTxt;

// Get the content as a string...
$r->setRobotsTxt('
User-agent: UA
User-agent: Googlebot
User-agent: Moz
Allow: /
Disallow: /private/
Disallow: /secret/page.html

User-agent: *
Crawl-delay: 7
Disallow: /private/
');

// ... or from a file
$r->setRobotsTxt(file_get_contents('http://example.com/robots.txt');

// Set the user agent
$r->setUserAgent('Googlebot');

// Check if the bot is allowed
$r->isAllowed('/public/page.html'); // returns TRUE
$r->isAllowed('/private/page.html'); // returns FALSE
$r->isAllowed('/secret/page.html'); // returns FALSE
$r->isAllowed('/public/private/page.html'); // returns TRUE

// Set another user agent (will be matched by the *)
$r->setUserAgent('FooBot');
$r->isAllowed('/public/page.html'); // returns 7 (which means "allowed" but 7 seconds need to be waited between requests; note that in theory this value could be 0 so do not mix it up with FALSE)
$r->isAllowed('/private/page.html'); // returns FALSE

```

