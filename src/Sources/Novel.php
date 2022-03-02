<?php

namespace Tuefekci\NovelSource\Sources;

use DateTime;

abstract class Novel extends \Tuefekci\NovelSource\Sources\SourceBase
{

	protected string $last_updated;
	protected array $novels = [];

	public function __construct($args = [])
	{
		parent::__construct($args);
	}

	/*
	 * Get all available Novels from Source
	 */
    abstract protected function novels(array $args = array());

	/*
	 * Get Novel from Source
	 */
    abstract protected function novel(string $url, array $args = array());

	/*
	 * Get Chapter from Source
	 */
    abstract protected function chapter(string $url, array $args = array());

	public function login($username, $password, array $args = array())
	{
		throw new \Exception("Not implemented");
	}

	public function search($query, array $args = array())
	{
		throw new \Exception("Not implemented");
	}
		
}