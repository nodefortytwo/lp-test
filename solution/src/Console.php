<?php
namespace Lp;

//Simple helper for parsing and handling CLI arguments

class Console {
	
	protected $signature = "t:d:o:hf";
	protected $requiredArgs = ['t', 'd', 'o'];


	/**
     * get arguments / options passed into the application
     */
 	public function getArgs(){
		$this->args = getopt($this->signature);
		return $this->args;
	}

	/**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string 	$arg
     * @param  mixed  	$default
     * @return mixed
     */
	public function getArg($arg, $default = null){
		//Process the arguments
		$this->getArgs();
		//if the argument isn't set, return the default
		if(isset($this->args[$arg])){

			//if the argument is a boolean, its just a switch which is better inverted
			if(is_bool($this->args[$arg])){
				return true;
			}else{
				return $this->args[$arg];
			}

		}else{
			return $default;
		}

	}

	/**
     * Ensure all required args are present, error and display help if not
     */
	public function validateArgs(){

		foreach($this->requiredArgs as $arg){
			if(!$this->getArg($arg)){
				echo "\nArgument -{$arg} is required!\n\n";
				$this->displayHelp();
			}
		}

	}

	/**
     * Echo the contents of the help file.
     */
	public function displayHelp(){
		echo file_get_contents(__DIR__.'/../help.txt');
		die();
	}

}