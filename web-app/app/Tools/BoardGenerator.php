<?php

namespace App\Tools;

use App\Models\Vegetation;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Texts\SVGText;
use SVG\Nodes\Texts\SVGTSpan;
use SVG\Rasterization\SVGRasterizer;
use SVG\SVG;

class BoardGenerator
{
  /**
   * @var Vegetation
   */
  private Vegetation $vegetation;

  /**
   * @param Vegetation $vegetation
   */
  function __construct(Vegetation $vegetation)
  {
    $this->vegetation = $vegetation;
  }

  /**
   * @return void
   */
  public function render(): void
  {
    SVG::addFont(resource_path('/fonts/Roboto-Bold.ttf'));
    SVG::addFont(resource_path('/fonts/Roboto-Italic.ttf'));
    SVG::addFont(resource_path('/fonts/Roboto-Regular.ttf'));

    $svgTemplate = SVG::fromFile(resource_path("images/board-template.svg"));
    $templateDoc = $svgTemplate->getDocument();

    $qrLayer = $templateDoc->getElementById('qrLayer');
    $textLayer = $templateDoc->getElementById('textLayer');

    $this->processLatinName($this->vegetation->species->latin_name, $textLayer);

    // add regular name
    $regularName = $this->createTextNode(
      style: 'regular',
      text: $this->vegetation->species->dutch_name,
      fontSize: 40,
      yValue: 122,
      bold: true
    );
    $textLayer->addChild($regularName);

    // add location and year
    $yearLocation = $this->createTextNode(
      style: 'regular',
      text: "{$this->vegetation->placed} @ {$this->vegetation->location['x']},{$this->vegetation->location['y']}",
      fontSize: 34,
      yValue: 165,
      bold: false
    );
    $textLayer->addChild($yearLocation);

    // add QR Code
    $svgQRCode = SVG::fromFile($this->vegetation->getQRCodeFilePath());
    $svgQRCodeDoc = $svgQRCode->getDocument();

    //$svgQRCodeDoc->removeAttribute('viewBox');
    $svgQRCodeDoc->setAttribute('x', '520px');
    $svgQRCodeDoc->setAttribute('y', '200px');

    $qrLayer->addChild($svgQRCodeDoc);

    if (!is_dir(storage_path(env('BOARDS_PATH')))) {
      mkdir(storage_path(env('BOARDS_PATH')));
    }

    $svgPath = storage_path(env('BOARDS_PATH').$this->vegetation->uuid.'.svg');

    file_put_contents($svgPath, $svgTemplate);
  }

  /**
   * @param string $text
   * @param $textLayer
   * @return void
   */
  private function processLatinName(string $text, $textLayer): void
  {
    $preText = null;
    $familyName = $text;

    if (preg_match('/\'([^\']+)\'/', $text, $matches)) {
      $preText = $matches[0];
      $familyName = trim(str_replace($preText, '', $familyName));
    }

    if ($preText) {
      $preTextNode = $this->createTextNode(
        style: 'regular',
        text: $preText,
        fontSize: 40,
        yValue: 75,
        bold: false
      );

      $familyNameNode = new SVGTSpan();
      $familyNameNode->setValue($familyName . " ");
      $familyNameNode->setAttribute('font-style', 'italic');

      $preTextNode->addChild($familyNameNode);
      $textLayer->addChild($preTextNode);
    } else {
      $textLayer->addChild($this->createTextNode(
        style: 'italic',
        text: $familyName,
        fontSize: 40,
        yValue: 75,
        bold: false
      ));
    }
  }

  /**
   * @param string $style
   * @param string $text
   * @param int $fontSize
   * @param int $yValue
   * @param bool $bold
   * @param int $xPos
   * @return SVGText
   */
  private function createTextNode(string $style, string $text, int $fontSize, int $yValue, bool $bold, int $xPos = 320): SVGText
  {
    $textNode = new SVGText($text);
    $textNode->setAttribute('font-family', 'Roboto, Roboto-Regular, ArialMT, Arial, sans-serif');
    $textNode->setAttribute('font-style', $style);
    $textNode->setAttribute('font-size', $fontSize);
    $textNode->setAttribute('dominant-baseline', 'middle');
    $textNode->setAttribute('text-anchor', 'middle');
    $textNode->setAttribute('x', $xPos . 'px');
    $textNode->setAttribute('y', $yValue);

    if($bold) {
      $textNode->setAttribute(' font-weight', 'bold');
    }

    return $textNode;
  }
}
