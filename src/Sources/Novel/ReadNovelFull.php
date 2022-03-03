<?php

namespace Tuefekci\NovelSource\Sources\Novel;

use Tuefekci\NovelSource\Models\Chapter;
use Tuefekci\NovelSource\Models\Novel;

class ReadNovelFull extends \Tuefekci\NovelSource\Sources\Novel
{
	protected array $base_urls = array("https://readnovelfull.com/");
	protected string $last_updated = "2022-03-02";

	public function novels(array $args = array())
	{
		$novels = $this->search("*");
		return $novels;
	}

	public function novel(string $url, array $args = array())
	{

		$response = $this->client->get($url);


		$novel = new Novel();

		$novel->title = $response->find("h3.title")[0]->text();
		$novel->image = $this->toAbsoluteUrl($response->find("div.book img")[0]->getAttribute("src"));

		$novel->id = $response->find("div#rating")[0]->getAttribute("data-novel-id");

		$novel->metadata[] = new \Tuefekci\NovelSource\Models\Metadata(['name' => 'rating', 'value' => $response->find("input#rateVal")[0]->getAttribute("value")]);
		$novel->metadata[] = new \Tuefekci\NovelSource\Models\Metadata(['name' => 'ratings', 'value' => $response->find("span[itemprop=reviewCount]")[0]->text()]);

		// Info Meta
		foreach($response->find("ul.info.info-meta li") as $meta) {

			if(\tuefekci\helpers\Strings::contains($meta->find("h3")[0]->text(), "Alternative names")) {
				if(!empty($meta->text())) {
					$alternative_names = explode(", ", $meta->text());

					foreach($alternative_names as $alternative_name) {
						$novel->metadata[] = new \Tuefekci\NovelSource\Models\Metadata(['name' => 'title', 'value' => $alternative_name]);
					}
				}
			}

			if(\tuefekci\helpers\Strings::contains($meta->find("h3")[0]->text(), "Author")) {
				foreach($meta->find("a") as $author) {
					$novel->author[] = $author->text();
				}
			}

			if(\tuefekci\helpers\Strings::contains($meta->find("h3")[0]->text(), "Genre")) {
				foreach($meta->find("a") as $genre) {
					$novel->metadata[] = new \Tuefekci\NovelSource\Models\Metadata(['name' => 'genre', 'value' => $genre->text()]);
				}
			}

			if(\tuefekci\helpers\Strings::contains($meta->find("h3")[0]->text(), "Source")) {
				if(!empty($meta->text())) {
					$novel->metadata[] = new \Tuefekci\NovelSource\Models\Metadata(['name' => 'publisher', 'value' => $meta->text()]);
				}
			}

			if(\tuefekci\helpers\Strings::contains($meta->find("h3")[0]->text(), "Status")) {
				if(\tuefekci\helpers\Strings::contains($meta->text(), "Completed")) {
					$novel->status = "completed";
				} else {
					$novel->status = "ongoing";
				}
			}

		}

		$novel->description = $response->find("div[itemprop=description]")[0]->innerHtml;

		$novel->url = $url;
		//$novel->date = $response->find("span.date")[0]->text();

		$chapters_url = "https://readnovelfull.com/ajax/chapter-archive?novelId=" . $novel->id;

		if(empty($args['skipChapters'])) {

			$chapters_response = $this->client->get($chapters_url);
			$chapters = $chapters_response->find("li a");

			$novel->chapters = array();

			foreach($chapters as $chapter) {
				$novel->chapters[] = new Chapter(['index' => count($novel->chapters), 'url' => $this->toAbsoluteUrl($chapter->getAttribute("href")), 'title' => $chapter->getAttribute("title")]);
			}
		}


		return $novel;
	}

	public function chapter(string $url, array $args = array())
	{
		$response = $this->client->get($url);

		$chapter = new Chapter();

		$chapter->title = $response->find("h2 a.chr-title")[0]->text();
		$chapter->url = $url;
		$chapter->content = $this->cleanContent($response->find("#chr-content")[0]->innerHtml);

		return $chapter;
	}

	public function search($query, array $args = array()) {

		$result = array();

		$response = $this->client->get($this->toAbsoluteUrl($this->base_urls[0])."search?keyword=" . urlencode($query)."&page=1");

		$novels = array();
		foreach($response->find(".list-novel")[0]->find(".row") as $novel) {
			$novels[] = $novel;
		}

		if($response->find(".pagination .last a")[0]) {
			parse_str(parse_url(html_entity_decode($response->find(".pagination .last a")[0]->getAttribute("href")), PHP_URL_QUERY), $get);
			$lastPage = $get['page'];

			for($i = 2; $i <= $lastPage; $i++) {
				$pageResponse = $this->client->get($this->toAbsoluteUrl($this->base_urls[0])."search?keyword=" . urlencode($query)."&page=".$i);

				foreach($pageResponse->find(".list-novel")[0]->find(".row") as $novel) {
					$novels[] = $novel;
				}
			}
		}

		

		foreach($novels as $novel) {

			$author = array();

			if($novel->find(".author")) {
				if(\tuefekci\helpers\Strings::contains($novel->find(".author")[0]->text(), ",")) {
					$author = explode(", ", trim($novel->find("span.author")[0]->text()));
				} else {
					$author[] = trim($novel->find("span.author")[0]->text());
				}
			}

			$image = str_replace("t-200x89", "t-300x439", $this->toAbsoluteUrl($novel->find(".cover")[0]->getAttribute("src")));

			$result[] = new Novel([
				'title' => $novel->find("h3.novel-title a")[0]->text(),
				'author' => $author,
				'url' => $this->toAbsoluteUrl($novel->find("h3.novel-title a")[0]->getAttribute("href")),
				'image' => $image,
			]);

		}
 

		return $result;
	}

}