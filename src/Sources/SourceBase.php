<?php

namespace Tuefekci\NovelSource\Sources;

class SourceBase
{

	protected array $base_urls;

    public array $bad_tags = [
        "noscript",
        "script",
        "style",
        "iframe",
        "ins",
        "header",
        "footer",
        "button",
        "input",
        "amp-auto-ads",
        "pirate",
        "figcaption",
        "address",
        "tfoot",
        "object",
        "video",
        "audio",
        "source",
        "nav",
        "output",
        "select",
        "textarea",
        "form",
        "map",
	];

	public array $blacklist_patterns = [];

	public array $notext_tags = [
        "img",
    ];

	public array $preserve_attrs = [
        "href",
        "src",
        "alt",
    ];

	public function __construct($args = [])
	{
		if(!empty($args)) {
			foreach($args as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	// 	get class name of this class
	public function getName() {
		$path = explode('\\', (string) get_class($this));
		return array_pop($path);
	}

	public function cleanContent($content) {
		return $content;
	}

	public function of($url)
	{
		if(!empty($this->base_urls)) {
			foreach($this->base_urls as $base_url) {
				if(strpos($url, $base_url) !== false) {
					return true;
				}
			}
		} else {
			throw new \Exception("No base_urls defined");
		}

		return false;
	}


	// convert relative to absolute url
	public function toAbsoluteUrl($url)
	{

		$base_url = rtrim($this->base_urls[0], '/');


		if($this->startsWith($url, "http://") or $this->startsWith($url, "https://")) {
			return $url;
		} 
		
		if($this->startsWith($url, "//")) {
			return "http:" . $url;
		}

		if($this->startsWith($url, "/")) {
			return $base_url . "/" . ltrim($url, "/");
		}

		return $base_url . "/" . ltrim($url, "/");

	}

	function startsWith($haystack, $needle, $case = true) {
		if ($case) {
			return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
		}
		return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
	}
	
	function endsWith($haystack, $needle, $case = true) {
		if ($case) {
			return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
		}
		return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
	}
}