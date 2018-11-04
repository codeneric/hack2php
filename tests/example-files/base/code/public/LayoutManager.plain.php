<?php

class LayoutManager {
	static $knownThemesMap = array(
		"Twenty Fifteen" => array('<div id="primary" class="content-area"><main id="main" class="site-main" role="main"><article class="hentry"><div class="entry-content">', '</div></article></main></div>'),
		"Photolux" => array('<div id="content-container" class="layout-full"><div id="full-width">', '</div><div class="clear"></div></div>')
	);
//

	static $defaultTags = array('<div class="cc-phmm-container">', '</div>');


	static function getOpeningTags() {
		$currentThemeName = wp_get_theme()->Name;

		$tag = '';
		foreach (LayoutManager::$knownThemesMap as $name => $tags) {
			if($currentThemeName == $name) {
				$tag = $tags[0];
				break;
			}
		}
		if($tag == '')
			$tag = LayoutManager::$defaultTags[0];

		echo $tag;
	}

	static function getClosingTags() {
		$currentThemeName = wp_get_theme()->Name;

		$tag = '';
		foreach (LayoutManager::$knownThemesMap as $name => $tags) {
			if($currentThemeName == $name) {
				$tag = $tags[1];
				break;
			}
		}
		if($tag == '')
			$tag = LayoutManager::$defaultTags[1];

		echo $tag;
	}

}