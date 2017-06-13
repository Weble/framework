<?php

namespace ZFTests\Service;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Service\Path;
use ZFTests\Classes\Filesystem;

/**
 * Class PathServiceTest
 * Path service test
 *
 * @package ZFTests\Service
 */
class PathServiceTest extends ServiceTest
{
    /**
     * Get Path service instance
     */
    private function getServiceInstance(){
        return self::$container->path;
    }

    /**
     * Test path register
     *
     * @covers          Path::register()
     * @covers          Path::path()
     * @covers          Path::paths()
     */
    public function testRegister(){
        $path = $this->getServiceInstance();
        $path->register(FIXTURES_PATH , 'fixtures');
        // Check if registered:
        $this->assertArraySubset([ FIXTURES_PATH ], $path->paths('fixtures:'));
        $this->assertEquals( FIXTURES_PATH, $path->path('fixtures:'));
    }

    /**
     * Test files list retrievement
     *
     * @depends         testRegister
     * @covers          Path::files()
     * @covers          Path::dirs()
     * @covers          Path::ls()
     */
    public function testFilesDirs(){
        $filteredFS = ['test1.txt', 'test2.txt'];
        $innerFS = ['subdir/test3.txt'];
        $dirsFS = ['subdir'];

        $path = $this->getServiceInstance();
        $path->register(JOOMLA_ENV_PATH . '/fixtures', 'jfixtures');
        // Check files:
        $this->assertArraySubset($filteredFS, $path->files('jfixtures:filesystem'));
        $this->assertArraySubset($filteredFS, $path->ls('jfixtures:filesystem', 'file'));

        // Check dirs:
        $this->assertArraySubset($dirsFS, $path->dirs('jfixtures:filesystem'));
        $this->assertArraySubset($dirsFS, $path->ls('jfixtures:filesystem', 'dir'));

        // Check universal method:
        $this->assertArraySubset(array_merge($innerFS, $filteredFS), $path->ls('jfixtures:filesystem', 'file', true));
        $this->assertArraySubset($innerFS, $path->ls('jfixtures:filesystem', 'file', true, '~test3~i'));
    }

    /**
     * Test resource string parsing
     *
     * @depends         testRegister
     * @covers          Path::parse()
     * @dataProvider    parsingDataSet
     */
    public function testParse($src, $expected){
        $path = $this->getServiceInstance();
        $path->register(JOOMLA_ENV_PATH . '/fixtures', 'jfixtures');
        $parsed = $path->parse($src);
        unset($parsed['paths']);

        $this->assertArraySubset($expected, $parsed);
    }

    /**
     * Test relative path maker
     *
     * @covers          Path::relative()
     * @dataProvider    relativePathDataSet
     */
    public function testRelativePath($src, $expected){
        $path = $this->getServiceInstance();
        $path->register(JOOMLA_ENV_PATH . '/fixtures', 'jfixtures');

        $this->assertEquals($expected, $path->relative($src));
    }

    /**
     * Parsing function test dataset
     */
    public function parsingDataSet(){
        return [
            [ 'jfixtures:filesystem/test1.txt', [ 'namespace' => 'jfixtures',
                'path' => 'filesystem/test1.txt'  ]
            ],
            [ 'jfixtures:', [ 'namespace' => 'jfixtures',
                'path' => ''  ]
            ],
            [ 'index.html', [ 'namespace' => 'default',
                'path' => 'index.html'  ]
            ]
        ];
    }

    /**
     * Relative path test set
     */
    public function relativePathDataSet(){
        return  [
            [ JOOMLA_ENV_PATH . '/fixtures/filesystem/test1.txt', 'fixtures/filesystem/test1.txt'],
            [ JOOMLA_ENV_PATH . '/index.php', 'index.php']
        ];
    }
}
