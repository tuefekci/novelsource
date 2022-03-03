<?php

namespace Tuefekci\NovelSource;

class NovelSource
{

	private array $novelSources = [];
	private array $metaSources = [];

	protected $client;

	public function __construct($args = [])
	{

		if(!empty($args['client'])) {
			$this->client = $args['client'];
		} else{
			$this->client = new \Tuefekci\NovelSource\Utils\HttpClient();
		}

		$this->initNovelSources();
	}


	private function initNovelSources() {
		$moduleArgs = array(
			'client' => $this->client,
		);

		// add all classes in the src/sources/novel directory to the $novelSources array
		$novelSources = glob(__DIR__ . '/Sources/Novel/*.php');
		foreach($novelSources as $novelSource) {
			$novelSource = basename($novelSource, '.php');

			if(empty($this->novelSources[$novelSource])) {
				$className = '\\Tuefekci\\NovelSource\\Sources\\Novel\\' . $novelSource;
				$this->novelSources[$novelSource] = new $className($moduleArgs);
			}
		}
	}
 
	public function getNovelSources()
	{
		return $this->novelSources;
	}

	public function locateNovelSource($url) {

		foreach($this->getNovelSources() as $source) {
			if($source->of($url)) {
				return $source;
			}
		}
		
		return false;
	}

	public function getMetaSources() {
		return $this->metaSources;
	}

	public function locateMetaSource($url) {
		return false;
	}

}