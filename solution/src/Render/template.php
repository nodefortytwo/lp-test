<?php
namespace Lp\Render;

use Lp\Utils;

class Template {

	/**
     * Take the template file and the whole taxonomy collection
     * to construct the class
     * @param  array $node
     * @param  string $parent
     */
	public function __construct($template_file, $taxonomy){
		$this->template = file_get_contents(__DIR__ . '/resources/' . $template_file);
		$this->taxonomy = $taxonomy;
	}


	/**
     * Output a completed template file to the specified directory
     * @param  Lp/Destination/Destination $destination
     * @param  string $output_directory
     * @param  bool $force
     */
	public function output($destination, $output_directory, $force = true){

		$this->destination = $destination;

		//create the directory if it doesn't exist
		if(!file_exists($output_directory)){
			$dir = @mkdir($output_directory);
			if(!$dir){
				throw new \Exception('Unable to make output directory');
			}
		}

		//copy the static assets to the the output directory
		if(!file_exists($output_directory . '/static')){
			mkdir($output_directory . '/static');
			copy(__DIR__ . '/resources/all.css', $output_directory . '/static/all.css');
			echo "Static assets copied to output directory!\n";
		}

		//check if the template already exists, skip it if the force flag isn't used
		if(file_exists($output_directory . '/' . $this->getFilename())){
			if($force){
				echo "{$this->getFilename()} already exists! Overwriting \n";
			}else{
				echo "{$this->getFilename()} already exists! Skipping \n";
				return;
			}
		}

		//output the file
		file_put_contents($output_directory . '/' . $this->getFilename(), $this->render());

		echo "{$this->getFilename()} Written!\n";

	}

	/**
     * Output the filename based on the set destination object
     * @return  string
     */
	public function getFilename(){
		return $this->destination->slug . '.html';
	}

	/**
     * Straight forward string replacement template engine, could be replaced by a full
     * template engine if scope increased.
     * @return  string
     */
	public function render(){

		$template_copy = $this->template;

		$template_copy = str_replace('{DESTINATION NAME}', $this->destination->title, $template_copy);

		$template_copy = str_replace('{CONTENT}', $this->destination->getBodyHtml(), $template_copy);

		$template_copy = str_replace('{NAVIGATION}', $this->renderNavigation(), $template_copy);

		//clean up the html to make reviewing easier
		$tidy = tidy_parse_string($template_copy, ['indent'=> true,
           											'output-xhtml'   => true,
           											'wrap' => 0], 'utf8');
		$tidy->cleanRepair();


		return (string) $tidy;
	}

	
	/**
     * Recursive nav render, currently hardcoded to 3 level (including active)
     * @return  Lp/Taxonomy/Node 	$taxoonomy_node
     * @return 	integer 			$level;
     */
	public function renderNavigation($taxonomy_node = null, $level = 0){
		
		$nav = '';

		$linkformat = '<a href="./%s.html">%s</a>';

		//if no taxonomy node has been provided, start at the destination and move up one level
		//if possible.
		if(!$taxonomy_node){
			$taxonomy_node = $active = $this->taxonomy->get($this->destination->id);
			if($taxonomy_node->parent){
				$taxonomy_node = $this->taxonomy->get($taxonomy_node->parent);
			}
		}

		$active = $this->destination->id == $taxonomy_node->id;

		//if first level, output the wrapping uls
		if($level == 0){
			$nav .= "<ul class=\"navigation\">";
		}

		$nav .= "<li>";

		$nav .= sprintf($linkformat, $taxonomy_node->slug, '- ' . $taxonomy_node->name);

		//only move beyone level 1 if the current node is the active page
		if(count($taxonomy_node->children ) && ($level < 1 || $active)){
			$level++;

			$nav .= "<ul class=\"navigation level_{$level}\">";
			foreach($taxonomy_node->children as $child_id){
				$child = $this->taxonomy->get($child_id);
				$nav .= self::renderNavigation($child, $level);
			}
			$nav .= "</ul>";
		}

		$nav .= '</li>';
		
		//if first level, output the wrapping close the wrapping ul
		if($level == 0){
			$nav .= "</ul>";
		}

		return $nav;
	}

}