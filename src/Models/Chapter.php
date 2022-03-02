<?php

namespace Tuefekci\NovelSource\Models;

class Chapter extends Model
{
	public int $index;
	public string $title;
	public string $url;
	public string $date;
	public string $content;

	public function __construct(array $data=[])
	{
		parent::__construct();

		if(!empty($data)) {
			foreach($data as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}
}