<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Lp\Console();

//Check if we should display the help file
if($app->getArg('h')){
	$app->displayHelp();
}

//Ensure all args are present
$app->validateArgs();


//init and parse the taxonomy xml file
$taxonomy_collection = new Lp\Taxonomy\Collection($app->getArg('t'));
$taxonomy_collection->parseXml();

//initialise the template class
$template = new Lp\Render\Template('template.html', $taxonomy_collection);

//Create an Iterator for the destinations. Note: no data is loaded at this point
$destination_collection = new Lp\Destination\Collection($app->getArg('d'));
//Loop through destinations, XML is loaded and parsed one destination at a time
foreach($destination_collection as $destination){
	$template->output($destination, $app->getArg('o'), $app->getArg('f'));
	echo "\n\n";
}
