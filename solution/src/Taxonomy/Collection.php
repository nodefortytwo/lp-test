<?php
namespace Lp\Taxonomy;

//This class parses the XML (using simplexml) flattens the structure into an indexed array of Lp\Taxonomy\Node objects

//I have made the assumption that the taxonomy dataset won't be larger than available php memory.

class Collection {

	public $data = [];


	/**
     * Get arguments / options passed into the application
     * @param  string $path
     */
	public function __construct($path){

		if(!is_readable($path)){
			throw new \Exception('Unable to read Taxonomy input file');
		}

		$this->path = $path;
	}

	/**
     * Take the path provided, parse the xml and 
     * populate the collection will all of the taxonomy
     * terms
     */
	public function parseXml(){

		$xml = simplexml_load_string(file_get_contents($this->path));
		$json = json_encode($xml);
		$xml = json_decode($json, TRUE);
		$node = $xml['taxonomy']['node'];

		//check if there are more than one top level node
		if(!isset($node['@attributes'])){
			foreach($node as $top_level_node){
				$this->parseNode($top_level_node);
			}
		}else{
			$this->parseNode($node);
		}
	}

	/**
     * Take array representation of a taxonomy item and its children
     * recursively walk through and flatten them.
     * @param  array $node
     * @param  string $parent
     */
	public function parseNode($node, $parent = null){
		$node_obj = new Node($node['@attributes']['atlas_node_id'], $node['node_name'], [], $parent);

		//check if the node has more than one child
		if(isset($node['node']) && !isset($node['node']['@attributes'])){
			foreach($node['node'] as $child){
				$node_obj->children[] = $child['@attributes']['atlas_node_id'];
				$this->parseNode($child, $node_obj->id);
			}
		//Just a single child
		}elseif(isset($node['node'])){
			$node_obj->children[] = $node['node']['@attributes']['atlas_node_id'];
			$this->parseNode($node['node'], $node_obj->id);
		}

		$this->data[$node_obj->id] = $node_obj;
		
	}

	/**
     * Take array representation of a taxonomy item and its children
     * recursively walk through and flatten them.
     */
	public function get($id){
		return $this->data[$id];
	}

	/**
     * Count the number of items in the collection
     */
	public function count(){
		return count($this->data);
	}
	
}