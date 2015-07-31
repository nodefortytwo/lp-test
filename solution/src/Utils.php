<?php
namespace Lp;

class Utils {
	
	/**
     * Dump and Die (for debugging)
     *
     * @return null
     */
	public static function dd(){
		call_user_func_array('var_dump', func_get_args());
		die();
	}

	/**
     * Flatten an array to dot notation style
     *
     * @param  array  $array
     * @param  string  $prepend
     * @return string
     */
	public static function arrayDot($array, $prepend = ''){
		$results = array();

		foreach ($array as $key => $value){

			if (is_array($value)){
				$results = array_merge($results, self::arrayDot($value, $prepend.$key.'.'));
			} else {
				$results[$prepend.$key] = $value;
			}
		}

		return $results;
	}


	/**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public static function strSlug($title, $separator = '-')
    {
        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);
        return trim($title, $separator);
    }

}