<?php //strict
// use codeneric\phmm\base\includes\Labels;

// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies


require_once __DIR__.'/../../../../admin/dbupdater.php'; 

/* HH_IGNORE_ERROR[4123] shut up */
final class DBUpdaterTest extends Codeneric_UnitTest {
    public static function setUpBeforeClass(){
        parent::setUpBeforeClass();
        $path = dirname(__FILE__);
        exec("cd $path && wp plugin install wordpress-importer --activate --allow-root");  
        echo exec("cd $path && wp import $path/clients-3.6.5.xml --authors=skip --allow-root");  
    }

    public function testDecoupleProjects(){
        $fc = new \codeneric\phmm\FunctionContainer();
        $fc->update_to_4_0_0();
        $this->assertSame(get_option('decoupled_projects'), 'done');
    }
  

}
