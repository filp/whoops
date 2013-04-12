<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\PrettyPageHandler;
use RuntimeException;
use InvalidArgumentException;

class PrettyPageHandlerTest extends TestCase
{
    /**
     * @return Whoops\Handler\JsonResponseHandler
     */
    private function getHandler()
    {
        return new PrettyPageHandler;
    }

    /**
     * @return RuntimeException
     */
    public function getException()
    {
        return new RuntimeException;
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
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setPageTitle
     * @covers Whoops\Handler\PrettyPageHandler::getPageTitle
     */
    public function testGetSetPageTitle()
    {
        $title = 'My Cool Error Handler';
        $handler = $this->getHandler();
        $handler->setPageTitle($title);

        $this->assertEquals($title, $handler->getPagetitle());
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setResourcesPath
     * @covers Whoops\Handler\PrettyPageHandler::getResourcesPath
     */
    public function testGetSetResourcesPath()
    {
        $path = __DIR__; // guaranteed to be valid!
        $handler = $this->getHandler();

        $handler->setResourcesPath($path);
        $this->assertEquals($path, $handler->getResourcesPath());
    }

    /**
     * @covers Whoops\Handler\PrettyPageHandler::setResourcesPath
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidResourcesPath()
    {
        $path = __DIR__ . '/ZIMBABWE'; // guaranteed to be invalid!
        $this->getHandler()->setResourcesPath($path);
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

        $tableOne = array(
            'ice' => 'cream',
            'ice-ice' => 'baby'
        );

        $tableTwo = array(
            'dolan' =>'pls',
            'time'  => time()
        );

        $handler->addDataTable('table 1', $tableOne);
        $handler->addDataTable('table 2', $tableTwo);

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
     * @covers Whoops\Handler\PrettyPageHandler::setEditor
     * @covers Whoops\Handler\PrettyPageHandler::getEditorHref
     */
    public function testSetEditorSimple()
    {
        $handler = $this->getHandler();
        $handler->setEditor('sublime');

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
     */
    public function testSetEditorCallable()
    {
        $handler = $this->getHandler();
        $handler->setEditor(function($file, $line) {
            $file = rawurlencode($file);
            $line = rawurlencode($line);
            return "http://google.com/search/?q=$file:$line";
        });

        $this->assertEquals(
            $handler->getEditorHref('/foo/bar.php', 10),
            'http://google.com/search/?q=%2Ffoo%2Fbar.php:10'
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
        $handler->addEditor('test-editor', function($file, $line) {
            return "cool beans $file:$line";
        });

        $handler->setEditor('test-editor');

        $this->assertEquals(
            $handler->getEditorHref('hello', 20),
            'cool beans hello:20'
        );
    }
}
