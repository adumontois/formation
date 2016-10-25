<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 25/10/2016
 * Time: 10:19
 */

namespace Helpers;

/**
 * Trait LinkHelper
 *
 * Helper contenant des méthodes pour gérer les liens
 *
 * @package Helpers
 */
trait LinkHelper {
	
	/**
	 * Adds a link to the given array, and returns the array by reference
	 *
	 * @param string[][] $link_a Array of all links already generated
	 * @param string $url Url to be called on click
	 * @param string $label Label to be displayed
	 * @param string $src Source file from link-image
	 * @param string $alt Alternative text for link-image
	 * @param string $js_function_name JS function name to call
	 *
	 * @return string[][] Array of all links already generated + the new link (reference)
	 */
	static public function &addLink(&$link_a, $url, $label = '', $src = null, $alt = '', $js_function_name = null) {
		if (!is_string($label)) {
			throw new \InvalidArgumentException('Label must be a string !');
		}
		if (!is_string($url) OR empty($url)) {
			throw new \InvalidArgumentException('Url must be a non-empty string !');
		}
		$new_link_a = ['label' => $label,
					 'url' => $url];
		if (null !== $src) {
			if (!is_string($src) OR empty($src)) {
				throw new \InvalidArgumentException('Source attribute must be a non-empty string !');
			}
			if (!is_string($alt)) {
				throw new \InvalidArgumentException('Alternative text attribute must be a string !');
			}
			$new_link_a['src'] = $src;
			$new_link_a['alt'] = $alt;
		}
		if (null !== $js_function_name) {
			if (!is_string($js_function_name) OR empty($js_function_name) OR ctype_digit($js_function_name[0])) {
				throw new \InvalidArgumentException('JS function name must be a non empty string beginning with "_" or a letter !');
			}
			$new_link_a['js_function_name'] = $js_function_name;
		}
		$link_a[] = $new_link_a;
		return $link_a;
	}
}