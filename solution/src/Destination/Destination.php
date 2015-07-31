<?php
namespace Lp\Destination;

use Lp\Utils;

class Destination {

	//map the dotnotated array to the structure we actually want to display. missing values will be empty strings
	//The order of the elements will determine the output order (introduction first etc)
	protected $mapping = [
		'introduction' => 'introductory.introduction.overview',
		'history' => 'history.history.overview',
		'getting_there' => 'transport.getting_there_and_away.overview',
		'when_to_go' => 'weather.when_to_go.overview',
		'getting_around' => 'transport.getting_around.overview'
	];
	

	/**
     * Construct the Class, takes a single xml fragment containing a single destination 
     *
     * @param  string  $xml
     */
	public function __construct($xml){
		
		$xml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
		$json = json_encode($xml);
		$xml = json_decode($json, TRUE);

		//flatten the data to make the mapping schema simple
		$dotnotated_array = Utils::arrayDot($xml);

		$this->id = $dotnotated_array['@attributes.atlas_id'];
		$this->title = $dotnotated_array['@attributes.title'];
		$this->slug = Utils::strSlug($dotnotated_array['@attributes.title-ascii']);

		$this->map($dotnotated_array);
	}

	/**
     * Take a flattened array and map the values onto the class
     * using the mapping array.
     *
     * @param  array  $array
     */
	public function map($dotnotated_array){
		foreach($this->mapping as $target => $src){

			if(isset($dotnotated_array[$src])){
				$this->{$target} = $dotnotated_array[$src];
			}else{
				$this->{$target} = '';
			}

		}
	}

	/**
     * Produce the html required for the template engine
     */
	public function getBodyHtml(){
		$body = '';
		foreach(array_keys($this->mapping) as $element){
			if(!empty($this->{$element})){

				$h1 = self::titleCase($element);

				$body .= "<h1>{$h1}</h1>\n";
				$body .= self::plainTextToHtml($this->{$element});
			}
		}

		return $body;

	}

	/**
     * Convert a _ delimeted string into title case
     */
	public static function titleCase($string){
		return ucwords(str_replace('_', ' ', $string));
	}


	/**
     * Basic html formatting, take linebreaks and turn
     * them into <p> tags
     */
	public static function plainTextToHtml($text){
		   // Add paragraph elements
		   $lf = chr(10);
		   return preg_replace('/
		      \n
		     (.*)
		     \n
		     /Ux' , $lf.'<p>'.$lf.'$1'.$lf.'</p>'.$lf, $text);
	}

}