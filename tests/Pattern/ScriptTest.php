<?php declare(strict_types=1);

namespace WyriHaximus\HtmlCompress\Tests\Pattern;

use Prophecy\Prophecy\ObjectProphecy;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDomInterface;
use WyriHaximus\HtmlCompress\Compressor\CompressorInterface;
use WyriHaximus\HtmlCompress\Pattern\Script;
use WyriHaximus\TestUtilities\TestCase;

/**
 * @internal
 */
final class ScriptTest extends TestCase
{
    /**
     * @var SimpleHtmlDomInterface
     */
    private $simpleHtmlDom;

    /**
     * @var ObjectProphecy|CompressorInterface
     */
    private $compressor;

    /**
     * @var Script
     */
    private $script;

    protected function setUp(): void
    {
        parent::setUp();

        $this->simpleHtmlDom = HtmlDomParser::str_get_html(
            '<script bier="stout" ale="indian">innerHtml</script>'
        )->getElementByTagName('script');

        $this->compressor = $this->prophesize(CompressorInterface::class);

        $this->script = new Script($this->compressor->reveal());
    }

    /**
     * @test
     */
    public function emptyCompressResultIsIgnored(): void
    {
        $this->compressor->compress('innerHtml')->shouldBeCalled()->willReturn('');

        $this->script->compress($this->simpleHtmlDom);

        self::assertSame('innerHtml', $this->simpleHtmlDom->innerhtml);
    }

    /**
     * @test
     */
    public function biggerOutputThenInputCompressResultIsIgnored(): void
    {
        $this->compressor->compress('innerHtml')->shouldBeCalled()->willReturn('aaaaaaaaaaaaaaaaaaaaaaa');

        $this->script->compress($this->simpleHtmlDom);

        self::assertSame('innerHtml', $this->simpleHtmlDom->innerhtml);
    }

    /**
     * @test
     */
    public function sameSizedOutputThenInputCompressResultIsIgnored(): void
    {
        $this->compressor->compress('innerHtml')->shouldBeCalled()->willReturn('htmlInner');

        $this->script->compress($this->simpleHtmlDom);

        self::assertSame('innerHtml', $this->simpleHtmlDom->innerhtml);
    }

    /**
     * @test
     */
    public function compress(): void
    {
        $this->compressor->compress('innerHtml')->shouldBeCalled()->willReturn('bla');

        $this->script->compress($this->simpleHtmlDom);

        self::assertSame('bla', $this->simpleHtmlDom->innerhtml);
        self::assertSame(
            '<script bier="stout" ale="indian">bla</script>',
            $this->simpleHtmlDom->outerHtml
        );
    }
}
