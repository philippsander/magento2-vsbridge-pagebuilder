<?php
/**
 * @copyright Copyright (c) netz98 GmbH (https://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */
namespace Divante\VsbridgePageBuilder\Model;

use Divante\VsbridgePageBuilder\Model\DataConverter\AttributesProcessor;
use Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRendererPool;
use Divante\VsbridgePageBuilder\Model\DataConverter\Renderer;
use Divante\VsbridgePageBuilder\Model\DataConverter\RendererPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ConvertPageBuilderToJsonUnitTest
 */
class ConvertPageBuilderToJsonUnitTest extends \PHPUnit\Framework\TestCase
{
    /** @var ConvertPageBuilderToJson */
    private $sut;

    public function setUp()
    {
        $attrProc = new AttributesProcessor();

        $childRenderers = [
            "slide" => new \Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer\Slide($attrProc),
            "banner" => new \Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer\Slide($attrProc),
            "link" => new \Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer\Link(),
            "empty_link" => new \Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer\EmptyLink(),
            "line" => new \Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer\Line($attrProc)
        ];
        $childRendererPool = new ChildrenRendererPool($childRenderers);

        $rendererPool = new RendererPool([
            "default" => new Renderer\Base($attrProc),
            "text" => new Renderer\Html($attrProc),
            "html" => new Renderer\Html($attrProc),
            "products" => new Renderer\Products($attrProc),
            "video" => new Renderer\Video($attrProc),
            "heading" => new Renderer\Heading($attrProc),
            "buttons" => new Renderer\Buttons($attrProc, $childRendererPool),
            "image" => new Renderer\Image($attrProc, $childRendererPool),
            "slider" => new Renderer\Slider($attrProc, $childRendererPool),
            "banner" => new Renderer\Banner($attrProc, $childRendererPool),
            "divider" => new Renderer\Divider($attrProc, $childRendererPool)
        ]);

        /** @var ConvertPageBuilderToJson $sut */
        $this->sut = new ConvertPageBuilderToJson($rendererPool);
    }

    /**
     * Tests if the input of the $samplesName.phtml file will result in the JSON in $samplesName.json
     * TODO: currently fails, because the provided sample data is probably outdated
     * @dataProvider samples
     * @param $samplesName
     */
    public function testConvert(string $samplesName)
    {
        $this->markTestSkipped(true);
        $html = file_get_contents(__DIR__ . '/../../../../docs/sample/' . $samplesName . '.phtml');
        $expected = file_get_contents(__DIR__ . '/../../../../docs/sample/' . $samplesName . '.json');
        $expected = json_decode($expected, true);

        $result = $this->sut->convert($html);

        self::assertEquals($expected, $result, "\$canonicalize = true", 0.0, 10, true);
    }

    /**
     * DataProvider for sample files
     * @return array
     */
    public function samples()
    {
        return [
            ['page-banner'],
            ['page-image'],
            ['page-slider']
        ];
    }
}
