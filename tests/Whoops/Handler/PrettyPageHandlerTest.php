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
}
