<?php

namespace Tuefekci\NovelSource\Utils;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class HttpClient
{

	private $client;
	private $cache;

	public function __construct()
	{

		// create Flysystem object
		$adapter = new \League\Flysystem\Local\LocalFilesystemAdapter(
			// Determine the root directory
			sys_get_temp_dir().'/novel-source-cache',

			// Customize how visibility is converted to unix permissions
			\League\Flysystem\UnixVisibility\PortableVisibilityConverter::fromArray([
				'file' => [
					'public' => 0640,
					'private' => 0604,
				],
				'dir' => [
					'public' => 0740,
					'private' => 7604,
				],
			]),

			// Write flags
			LOCK_EX,
		
		);
		$filesystem = new \League\Flysystem\Filesystem($adapter);
		// create Scrapbook KeyValueStore object
		$cache = new \MatthiasMullie\Scrapbook\Adapters\Flysystem($filesystem);
		// create Simplecache object from Scrapbook KeyValueStore object
		$this->cache = new \MatthiasMullie\Scrapbook\Psr16\SimpleCache($cache);



		// sys_get_temp_dir().'/novel-source-cache'
		#endregion


		$this->client = new Client([
			// You can set any number of default request options.
			'timeout'  => 2.0,
			'defaults' => [
				'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36']
			]
		]);
	}


	public function get(string $url, $cacheTime=60*60, array $args = array()) {

		$cacheKey = sha1($url);

		if ($cachedResponse = $this->cache->get($cacheKey)) {
			$content = $cachedResponse;
		} else {
			$response = $this->client->get($url);

			if($response->getStatusCode() == 200) {

				$content = (string) $response->getBody()->getContents();
				$this->cache->set($cacheKey, $content, $cacheTime);

			} else {
				return throw new \Exception("Error fetching URL");
			}
		}

		if(!empty($content)) {
			$dom = new Dom;
			$dom->loadStr($content);
			return $dom;
		}



	
	}
}