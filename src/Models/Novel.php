<?php

namespace Tuefekci\NovelSource\Models;

class Novel extends Model
{
	public $id;
	public string $title;
	public string $url;
	public array  $author;
	public string $description;
	public string $image;
	public string $status;
	public string $language;

	public array $volumes;
	public array $chapters;
	public array $metadata;

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