<?php
class ParseTest extends PHPUnit_Framework_TestCase
{
    //check the total taxonomy items found
    public function testTaxonomyParse()
    {
        $taxonomy_collection = new Lp\Taxonomy\Collection(__DIR__.'/assets/taxonomy.xml');
        $taxonomy_collection->parseXml();

        $this->assertEquals(24, $taxonomy_collection->count());

    }

    //check the total destination items found
    public function testDestinationParse()
    {
        $destination_collection = new Lp\Destination\Collection(__DIR__.'/assets/destinations.xml');
        $total = 0;

        foreach($destination_collection as $dest){
            $total++;
        }

        $this->assertEquals(24, $total);

    }


    public function testUnwritableOutputDirectory(){
        $taxonomy_collection = new Lp\Taxonomy\Collection(__DIR__.'/assets/taxonomy.xml');
        $taxonomy_collection->parseXml();
        $destination_collection = new Lp\Destination\Collection(__DIR__.'/assets/destinations.xml');

        $template = new Lp\Render\Template('template.html', $taxonomy_collection);
        
        $dest = current($destination_collection);

        try {
            $template->output($dest, 'random/directory', false);
        }
        catch (Exception $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

}
