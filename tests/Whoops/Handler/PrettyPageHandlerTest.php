<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;

use InvalidArgumentException;
use RuntimeException;
use Whoops\TestCase;

class PrettyPageHandlerTest extends TestCase
{
    /**
     * @return \Whoops\Handler\PrettyPageHandler
     */
    private function getHandler()
    {
        $handler = new PrettyPageHandler();
        $handler->handleUnconditionally();
        return $handler;
    }

    /**
     * @return RuntimeException
     */
    public function getException()
    {
        return new RuntimeException();
    }

    /**
     * Test that PrettyPageHandle handles the template without
     * any errors.
     * @covers Whoops\Handler\PrettyPageHandler::handle
     */
    public function testHandleWithoutErrors()
    {
        $run     = $this->getRunInstance();
        $handler = $this->getHandler();

        $run->pushHandler($handler);

        ob_start();
        $run->handleException($this->getException());
        ob_get_clean();

        // Reached the end without errors
        $this->assertTrue(true);
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setPageTitle
     * @covers Whoops\Handler\PrettyPageHandler::getPageTitle
     */
    public function testGetSetPageTitle()
    {
        $title = 'My Cool Error Handler';
        $handler = $this->getHandler();
        $this->assertEquals($handler, $handler->setPageTitle($title));

        $this->assertEquals($title, $handler->getPagetitle());
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::addResourcePath
     * @covers Whoops\Handler\PrettyPageHandler::getResourcePaths
     */
    public function testGetSetResourcePaths()
    {
        $path = __DIR__; // guaranteed to be valid!
        $handler = $this->getHandler();

        $this->assertEquals($handler, $handler->addResourcePath($path));
        $allPaths = $handler->getResourcePaths();

        $this->assertCount(2, $allPaths);
        $this->assertEquals($allPaths[0], $path);
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::addResourcePath
     */
    public function testSetInvalidResourcesPath()
    {
        $this->expectExceptionOfType('InvalidArgumentException');

        $this->getHandler()->addResourcePath(__DIR__ . '/ZIMBABWE');
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::getDataTables
     * @covers Whoops\Handler\PrettyPageHandler::addDataTable
     */
    public function testGetSetDataTables()
    {
        $handler = $this->getHandler();

        // should have no tables by default:
        $this->assertEmpty($handler->getDataTables());

        $tableOne = [
            'ice' => 'cream',
            'ice-ice' => 'baby',
        ];

        $tableTwo = [
            'dolan' => 'pls',
            'time'  => time(),
        ];

        $this->assertEquals($handler, $handler->addDataTable('table 1', $tableOne));
        $this->assertEquals($handler, $handler->addDataTable('table 2', $tableTwo));

        // should contain both tables:
        $tables = $handler->getDataTables();
        $this->assertCount(2, $tables);

        $this->assertEquals($tableOne, $tables['table 1']);
        $this->assertEquals($tableTwo, $tables['table 2']);

        // should contain only table 1
        $this->assertEquals($tableOne, $handler->getDataTables('table 1'));

        // should return an empty table:
        $this->assertEmpty($handler->getDataTables('ZIMBABWE!'));
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::getDataTables
     * @covers Whoops\Handler\PrettyPageHandler::addDataTableCallback
     */
    public function testSetCallbackDataTables()
    {
        $handler = $this->getHandler();

        $this->assertEmpty($handler->getDataTables());
        $table1 = function () {
            return [
                'hammer' => 'time',
                'foo'    => 'bar',
            ];
        };
        $expected1 = ['hammer' => 'time', 'foo' => 'bar'];

        $table2 = function () use ($expected1) {
            return [
                'another' => 'table',
                'this'    => $expected1,
            ];
        };
        $expected2 = ['another' => 'table', 'this' => $expected1];

        $table3 = function() {
			return array("oh my" => "how times have changed!");
		};
        $expected3 = ['oh my' => 'how times have changed!'];

        // Test inspector parameter in data table callback
        $table4 = function (\Whoops\Exception\Inspector $inspector) {
            return array(
              'Exception class' => get_class($inspector->getException()),
              'Exception message' => $inspector->getExceptionMessage(),
            );
        };
        $expected4 = array(
          'Exception class' => 'InvalidArgumentException',
          'Exception message' => 'Test exception message',
        );
        $inspectorForTable4 = new \Whoops\Exception\Inspector(
            new \InvalidArgumentException('Test exception message')
        );

        // Sanity check, make sure expected values really are correct.
        $this->assertSame($expected1, $table1());
        $this->assertSame($expected2, $table2());
        $this->assertSame($expected3, $table3());
        $this->assertSame($expected4, $table4($inspectorForTable4));

        $this->assertEquals($handler, $handler->addDataTableCallback('table1', $table1));
        $this->assertEquals($handler, $handler->addDataTableCallback('table2', $table2));
        $this->assertEquals($handler, $handler->addDataTableCallback('table3', $table3));
        $this->assertEquals($handler, $handler->addDataTableCallback('table4', $table4));

        $tables = $handler->getDataTables();
        $this->assertCount(4, $tables);

        // Supplied callable is wrapped in a closure
        $this->assertInstanceOf('Closure', $tables['table1']);
        $this->assertInstanceOf('Closure', $tables['table2']);
        $this->assertInstanceOf('Closure', $tables['table3']);
        $this->assertInstanceOf('Closure', $tables['table4']);

        // Run each wrapped callable and check results against expected output.
        $this->assertEquals($expected1, $tables['table1']());
        $this->assertEquals($expected2, $tables['table2']());
        $this->assertEquals($expected3, $tables['table3']());
        $this->assertEquals($expected4, $tables['table4']($inspectorForTable4));

        $this->assertSame($tables['table1'], $handler->getDataTables('table1'));
        $this->assertSame($expected1, call_user_func($handler->getDataTables('table1')));
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setEditor
     * @covers Whoops\Handler\PrettyPageHandler::getEditorHref
     */
    public function testSetEditorSimple()
    {
        $handler = $this->getHandler();
        $this->assertEquals($handler, $handler->setEditor('sublime'));

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar.php', 10),
            'subl://open?url=file://%2Ffoo%2Fbar.php&line=10'
        );

        $this->assertEquals(
            $handler->getEditorHref('/foo/with space?.php', 2324),
            'subl://open?url=file://%2Ffoo%2Fwith%20space%3F.php&line=2324'
        );

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar/with-dash.php', 0),
            'subl://open?url=file://%2Ffoo%2Fbar%2Fwith-dash.php&line=0'
        );
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setEditor
     * @covers Whoops\Handler\PrettyPageHandler::getEditorHref
     * @covers Whoops\Handler\PrettyPageHandler::getEditorAjax
     */
    public function testSetEditorCallable()
    {
        $handler = $this->getHandler();

        // Test Callable editor with String return
        $this->assertEquals($handler, $handler->setEditor(function ($file, $line) {
            $file = rawurlencode($file);
            $line = rawurlencode($line);
            return "http://google.com/search/?q=$file:$line";
        }));

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar.php', 10),
            'http://google.com/search/?q=%2Ffoo%2Fbar.php:10'
        );

        // Then test Callable editor with Array return
        $this->assertEquals($handler, $handler->setEditor(function ($file, $line) {
            $file = rawurlencode($file);
            $line = rawurlencode($line);
            return [
                'url' => "http://google.com/search/?q=$file:$line",
                'ajax' => true,
            ];
        }));

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar.php', 10),
            'http://google.com/search/?q=%2Ffoo%2Fbar.php:10'
        );

        $this->assertEquals(
            $handler->getEditorAjax('/foo/bar.php', 10),
            true
        );


        $this->assertEquals($handler, $handler->setEditor(function ($file, $line) {
            $file = rawurlencode($file);
            $line = rawurlencode($line);
            return [
                'url' => "http://google.com/search/?q=$file:$line",
                'ajax' => false,
            ];
        }));

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar.php', 10),
            'http://google.com/search/?q=%2Ffoo%2Fbar.php:10'
        );

        $this->assertEquals(
            $handler->getEditorAjax('/foo/bar.php', 10),
            false
        );

        $this->assertEquals($handler, $handler->setEditor(function ($file, $line) {
            return false;
        }));

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar.php', 10),
            false
        );
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setEditor
     * @covers Whoops\Handler\PrettyPageHandler::addEditor
     * @covers Whoops\Handler\PrettyPageHandler::getEditorHref
     */
    public function testAddEditor()
    {
        $handler = $this->getHandler();
        $this->assertEquals($handler, $handler->addEditor('test-editor',
                function ($file, $line) {
            return "cool beans $file:$line";
        }));

        $this->assertEquals($handler, $handler->setEditor('test-editor'));

        $this->assertEquals(
            $handler->getEditorHref('hello', 20),
            'cool beans hello:20'
        );
    }

    public function testEditorXdebug()
    {
        if (!extension_loaded('xdebug')) {
            // Even though this test only uses ini_set and ini_get,
            // without xdebug active, those calls do not work.
            // In particular, ini_get after ini_setting returns false.
            $this->markTestSkipped('The xdebug extension is not loaded.');
        }

        $originalValue = ini_get('xdebug.file_link_format');

        ini_set('xdebug.file_link_format', '%f:%l');

        $handler = $this->getHandler();
        $this->assertEquals($handler, $handler->setEditor('xdebug'));

        $this->assertEquals(
            '/foo/bar.php:10',
            $handler->getEditorHref('/foo/bar.php', 10)
        );

        ini_set('xdebug.file_link_format', 'subl://open?url=%f&line=%l');

        // xdebug doesn't do any URL encoded, matching that behaviour.
        $this->assertEquals(
            'subl://open?url=/foo/with space?.php&line=2324',
            $handler->getEditorHref('/foo/with space?.php', 2324)
        );

        ini_set('xdebug.file_link_format', $originalValue);
    }
}
