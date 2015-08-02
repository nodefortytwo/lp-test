<?php
namespace Lp\Destination;

use SplFileObject;

class Collection implements \Iterator{

	private $position = 0;
	//allow the batch process to be extended to support resumable processing
	private $starting_postion = 0;

	//Because we are only interested in a single tag and its contents we can use
	//a very simple/effcient text match rather than full xml parsing.
	protected $start = '<destination ';
	protected $end = '</destination>';

    public function __construct($path, $starting_postion = 0) {
    	$this->path = $path;
        $this->position = $starting_postion;
        $this->starting_postion = $starting_postion;

        if(!is_readable($path)){
            throw new \Exception('Unable to read Destination input file');
        }


        $this->file = new SplFileObject($this->path);
        $this->file->seek($starting_postion);

    }

    public function getNextDestination(){
    	$destination = '';
    	$found = false;


    	while (!$this->file->eof()) {
		    $line = trim($this->file->fgets());
		    //check if the current line starts with our starting string
    		if(substr($line, 0, strlen($this->start)) == $this->start){
    			$found = true;
    		}

    		//if we have found an opening tag, add that line and all subsequent lines to
    		//a string until we find the closing tag.
    		if($found){
    			$destination .= $line;
    		}

    		//check if the current line starts with our ending string
    		if(substr($line, 0, strlen($this->end)) == $this->end){
    			$found = false;
    			break;	
    		}

		}
		//store the line number as the iterator position;
		$this->position = $this->file->key();

		//if we didn't find any lines we are done.
		if(empty($destination)){
			return false;
		}
		
		//return a Lp\Destination\Destination by passing in the xml fragment.
		return new Destination($destination);

    }

    //Standard PHP iterator methods
    function rewind() {
        $this->position = $this->starting_postion;
        $this->file->seek($this->position);
        unset($this->current);
    }

    function current() {
        return $this->current;
    }

    function key() {
        return $this->position;
    }

    function next() {
        $this->current = $this->getNextDestination();
    }

    function valid() {

       	if(!isset($this->current)){
       		$this->next();
       	}

        if($this->current){
        	return true;
        }else{
        	return false;
        }
        
    }
	
}