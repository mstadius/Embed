<?php
/**
 * Adapter to provide information from archive.org api
 */
namespace Embed\Adapters;

use Embed\Url;
use Embed\Viewers;
use Embed\Providers\Provider;

class Archive extends Webpage implements AdapterInterface {
	public $Api;

	static public function check (Url $Url) {
		return $Url->match(array(
			'http://archive.org/details/*'
		));
	}

	protected function initProviders (Url $Url) {
		parent::initProviders($Url);

		$this->Api = new Provider();

		$UrlApi = clone $Url;
		$UrlApi->setParameter('output', 'json');

		if (($json = $UrlApi->getJsonContent())) {
			$this->Api->set($json);
		}
	}

	private function getMetadata ($key) {
		if (($metadata = $this->Api->get('metadata', $key)) && isset($metadata[0])) {
			return $metadata[0];
		}
	}

	public function getTitle () {
		return $this->getMetadata('title') ?: parent::getTitle();
	}

	public function getDescription () {
		return $this->getMetadata('description') ?: parent::getDescription();
	}

	public function getType () {
		switch ($this->getMetadata('mediatype')) {
			case 'movies':
				return 'video';

			case 'audio':
				return 'audio';

			case 'texts':
				return 'rich';
		}

		return parent::getType();
	}

	public function getCode () {
		switch ($this->getMetadata('mediatype')) {
			case 'movies':
				$embed_url = str_replace('/details/', '/embed/', $this->getUrl());
				return Viewers::iframe($embed_url);

			case 'audio':
				$embed_url = str_replace('/details/', '/embed/', $this->getUrl());
				return Viewers::iframe($embed_url, 0, 30);

			case 'texts':
				$embed_url = str_replace('/details/', '/stream/', $this->getUrl()).'?ui=embed';
				return Viewers::iframe($embed_url, 480, 430);
		}
	}

	public function getAuthorName () {
		return $this->getMetadata('creator') ?: parent::getAuthorName();
	}

	public function getImages () {
		$images = array();

		if (($image = $this->Api->get('misc', 'image'))) {
			$images[] = $this->Url->getAbsolute($image);
		}

		if (($files = $this->Api->get('files'))) {
			foreach ($files as $url => $info) {
				if ($info['format'] === 'Thumbnail') {
					$images[] = $this->Url->getAbsolute($url);
				}
			}
		}

		return $images;
	}
}
