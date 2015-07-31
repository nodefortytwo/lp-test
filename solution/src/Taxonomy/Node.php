<?php
namespace Lp\Taxonomy;
use Lp\Utils;

class Node {

	public $id;
	public $name;
	public $children;
	public $parent;
	
	/**
     * Initialize a Taxonomy Node
     *
     * @param  string 	$id
     * @param  string 	$name
     * @param  array 	$children
     * @param  string  	$parent
     */
	public function __construct($id, $name, $children = [], $parent = null){
		$this->id = (int) $id;
		$this->name = $name;
		$this->children = $children;
		$this->parent = (int) $parent;
		$this->slug = Utils::strSlug($name);
	}
}