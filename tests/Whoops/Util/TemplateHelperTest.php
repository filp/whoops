<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;
use Whoops\TestCase;
use Whoops\Util\TemplateHelper;

class TemplateHelperTest extends TestCase
{
    /**
     * @var Whoops\Util\TemplateHelper
     */
    private $helper;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->helper = new TemplateHelper;
    }

    /**
     * @covers Whoops\Util\TemplateHelper::escapeButPreserveUris
     * @covers Whoops\Util\TemplateHelper::escape
     */
    public function testEscape()
    {
        $original = "This is a <a href=''>Foo</a> test string";

        $this->assertEquals(
            $this->helper->escape($original),
            "This is a &lt;a href=&#039;&#039;&gt;Foo&lt;/a&gt; test string"
        );
    }

    public function testEscapeBrokenUtf8()
    {
        // The following includes an illegal utf-8 sequence to test.
        // Encoded in base64 to survive possible encoding changes of this file.
        $original = base64_decode('VGhpcyBpcyBhbiBpbGxlZ2FsIHV0Zi04IHNlcXVlbmNlOiDD');

        // Test that the escaped string is kinda similar in length, not empty
        $this->assertLessThan(
            10,
            abs(strlen($original) - strlen($this->helper->escape($original)))
        );
    }

    /**
     * @covers Whoops\Util\TemplateHelper::escapeButPreserveUris
     */
    public function testEscapeButPreserveUris()
    {
        $original = "This is a <a href=''>http://google.com</a> test string";

        $this->assertEquals(
            $this->helper->escapeButPreserveUris($original),
            "This is a &lt;a href=&#039;&#039;&gt;<a href=\"http://google.com\" target=\"_blank\">http://google.com</a>&lt;/a&gt; test string"
        );
    }

    /**
     * @covers Whoops\Util\TemplateHelper::slug
     */
    public function testSlug()
    {
        $this->assertEquals("hello-world", $this->helper->slug("Hello, world!"));
        $this->assertEquals("potato-class", $this->helper->slug("Potato class"));
    }

    /**
     * @covers Whoops\Util\TemplateHelper::render
     */
    public function testRender()
    {
        $template = __DIR__ . "/../../fixtures/template.php";

        ob_start();
        $this->helper->render($template, array("name" => "B<o>b"));
        $output = ob_get_clean();

        $this->assertEquals(
            $output,
            "hello-world\nMy name is B&lt;o&gt;b"
        );
    }

    /**
     * @covers Whoops\Util\TemplateHelper::setVariables
     * @covers Whoops\Util\TemplateHelper::getVariables
     * @covers Whoops\Util\TemplateHelper::setVariable
     * @covers Whoops\Util\TemplateHelper::getVariable
     * @covers Whoops\Util\TemplateHelper::delVariable
     */
    public function testTemplateVariables()
    {
        $this->helper->setVariables(array(
            "name" => "Whoops",
            "type" => "library",
            "desc" => "php errors for cool kids"
        ));

        $this->helper->setVariable("name", "Whoops!");
        $this->assertEquals($this->helper->getVariable("name"), "Whoops!");
        $this->helper->delVariable("type");

        $this->assertEquals($this->helper->getVariables(), array(
            "name" => "Whoops!",
            "desc" => "php errors for cool kids"
        ));
    }
}
