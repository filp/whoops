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
}
