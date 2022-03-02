<?php

namespace Tuefekci\NovelSource\Models;

class Metadata extends Model
{

	public function __construct(array $data=[])
	{
		parent::__construct();

		if(!empty($data)) {
			foreach($data as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	public string $name;
	public string $value;

}