<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class RobotsTxtTest extends \PHPUnit_Framework_TestCase
{

    private static $robotsTxt = <<<TXT
# Robots.txt file for www.seek.co.nz 
# URLs are case sensitive!
Sitemap: http://www.seek.co.nz/sitemap.xml

# Google will not spider 
User-agent: Rlkjkljh
User-agent: Googlebot 
User-agent: Ojhgjhgy
Disallow: /alliances/
Disallow: /Apply/ 
Disallow: /JobApply/ 
Disallow: /JobListing/ 
Disallow: /Register/
Disallow: /Login/
Disallow: /MyAccount/ResetPassword

User-agent: bingbot 
Disallow: /alliances/
Disallow: /Apply/ 
Disallow: /JobApply/ 
Disallow: /JobListing/ 
Disallow: /Register/
Disallow: /Login/
Disallow: /MyAccount/ResetPassword 

# All other agents will not spider 
User-agent: * 
Crawl-delay: 7 
Disallow: /alliances/
Disallow: /content/images/
Disallow: /Resource/
Disallow: /JobSearch?* 
Disallow: /Job/
Disallow: /Register/
Disallow: /Login/
Disallow: /MyAccount/ResetPassword 
Disallow: /JobApply/SubmittedByKnownUser?*

# SEOmoz 
User-agent: Ojsdf4gjhgy
User-agent: rogerbot 
User-agent: Oj4353ghgy
Disallow: /alliances/
Disallow: /content/images/
Disallow: /Resource/
Disallow: /JobSearch?* 
Disallow: /Job/ 
Disallow: /JobMail/Create?* 
Disallow: /Register?* 
Disallow: /Login?* 
Disallow: /Apply/ 
Disallow: /JobApply/ 
Disallow: /JobListing/ 
Disallow: /Login/ 
Disallow: /MyAccount/ResetPassword?* 

# YottaMonitor 
User-agent: YottaaMonitor 
Disallow: / 

# Google Ad Sense 
User-agent: Mediapartners-Google 
Disallow:

# LinkedIn Bot
User-agent: Akjhkh
User-agent: LinkedInBot
User-agent: Ljhg76
Disallow: /

# proximic
User-agent: proximic
Disallow: /JobApply/SubmittedByKnownUser*
TXT;

    public function testIt()
    {
        $r = new \DrakeES\RobotsTxt;
        $r->setUserAgent('Google');
        $r->setRobotsTxt(self::$robotsTxt);
        $this->assertTrue($r->isAllowed('/Job/'));
        $this->assertFalse($r->isAllowed('/JobListing/'));
        $this->assertTrue($r->isAllowed('/JobMail/Create?kjhkjhkjh'));
        $r->setUserAgent('Foo');
        $this->assertFalse($r->isAllowed('/Job/'));
        $this->assertEquals(7, $r->isAllowed('/blablabla'));
        $this->assertFalse($r->isAllowed('/JobApply/SubmittedByKnownUser?kjhkjhkjh'));
        $r->setUserAgent('rogerbot');
        $this->assertFalse($r->isAllowed('/JobMail/Create?kjhkjhkjh'));
        $r->setUserAgent('LinkedInBot');
        $this->assertFalse($r->isAllowed('/blablabla'));
    }

}