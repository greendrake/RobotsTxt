<?php

namespace DrakeES;

class RobotsTxt
{

    private $userAgent;
    private $robotsTxt;
    private $parsed;

    /*
        Sets short name of user agent, e.g. "Googlebot".
        This will be non-case-sensitively matched against any "User-agent" record
        the value of which starts with the name, e.g. the values "googlebot" and "gOOglebot@^%$&#^"
        will both match against the name "Googlebot"
        but "mygooglebot" will not.
    */
    public function setUserAgent($userAgent)
    {
        $this->updateInputData('userAgent', $userAgent);
    }

    /*
        Sets the content of robots.txt to be parsed.
    */
    public function setRobotsTxt($robotsTxt)
    {
        $this->updateInputData('robotsTxt', $robotsTxt);
    }

    /*
        Tells whether or not the given $path is allowed to be crawled.
        Returns FALSE if not allowed;
        If allowed and Crawl-delay is set, returns Crawl-delay (integer number).
        If allowed and Crawl-delay is not set, returns TRUE.
    */
    public function isAllowed($path = '/')
    {
        $parsed = $this->getParsed();
        foreach ($parsed['disallow'] as $disallow) {
            $disallow = preg_quote($disallow, '/');
            $last = substr($disallow, -1);
            if ($last !== '*' && $last !== '$') {
                $disallow .= '*';
            }
            $disallow = str_replace(array('\*', '\$'), array('*', '$'), $disallow);
            $disallow = str_replace('*', '(.*)?', $disallow);
            if (preg_match('/^' . $disallow . '/i', $path)) {
                return false;
            }
        }
        return $parsed['delay'] ? $parsed['delay'] : true;
    }

    private function updateInputData($name, $value)
    {
        if ($this->$name === $value) {
            // Already there - nothing to update;
            return;
        }
        $this->$name = $value;
        // Flush the parsed data (if any) so that it is parsed again with the new data when required
        $this->parsed = null;
    }

    private function getParsed()
    {
        if ($this->parsed === null) {
            $this->parse();
        }
        return $this->parsed;
    }

    private function parse()
    {
        $isCatchAll = false;
        $userAgentMatch = false;
        $rules = [
                'disallow' => [],
                'delay' => 0
            ];
        $rules = [
            'catchAll' => $rules,
            'userAgentMatch' => $rules
        ];
        $readingRules = false;
        $stackKey = null;
        // Split robots.txt in lines and walk it line by line:
        foreach (preg_split('/$\R?^/m', $this->robotsTxt) as $line) {
            if (preg_match("/^User\-agent:\s*([^#\s]+)/", $line, $m)) {
                if ($readingRules) {
                    if ($userAgentMatch) {
                        // We've parsed what interests us already and
                        // currently must be in a completely unrelevant group.
                        // Quit the loop.
                        break;
                    } else {
                        $readingRules = false;
                    }
                }
                if (
                        !($isCatchAll = ($m[1] === '*'))
                            &&
                        !$userAgentMatch
                            &&
                        stripos($m[1], $this->userAgent) === 0
                    ) {
                    $userAgentMatch = true;
                    $stackKey = 'userAgentMatch';
                }
            }
            if (!$userAgentMatch && !$isCatchAll) {
                continue; // nothing of our current interest in the current line
            } elseif ($stackKey === null) {
                $stackKey = 'catchAll';
            }
            if (preg_match("/^disallow:\s*([^#\s]+)/i", $line, $m)) {
                $rules[$stackKey]['disallow'][] = $m[1];
                $readingRules = true;
            } elseif (preg_match("/^crawl\-delay:\s*(\d+)/i", $line, $m)) {
                $rules[$stackKey]['delay'] = (int)$m[1];
                $readingRules = true;
            }
        }
        $this->parsed = $userAgentMatch ? $rules['userAgentMatch'] : $rules['catchAll'];
    }

}