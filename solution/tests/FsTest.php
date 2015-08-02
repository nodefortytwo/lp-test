<?php
class FsTest extends PHPUnit_Framework_TestCase
{
    public function testUnwritableOutputDirectory(){
        $taxonomy_collection = new Lp\Taxonomy\Collection(__DIR__.'/assets/taxonomy.xml');
        $taxonomy_collection->parseXml();
        $destination_collection = new Lp\Destination\Collection(__DIR__.'/assets/destinations.xml');

        $template = new Lp\Render\Template('template.html', $taxonomy_collection);
        
        $dest = current($destination_collection);

        try {
            $template->output($dest, '/random/directory', false);
        }
        catch (Exception $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

}
