<?php

namespace App\Tools;

use App\Models\Vegetation;
use SVG\Nodes\Texts\SVGText;
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

    // add latin name
    $latinName = $this->createTextNode(
      style: 'italic',
      text: $this->vegetation->species->latin_name,
      fontSize: 40,
      yValue: 75,
      bold: false
    );
    $textLayer->addChild($latinName);

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
    $svgQRCode = SVG::fromFile(public_path(env('QR_CODES_PATH').$this->vegetation->uuid.'.svg'));
    $svgQRCodeDoc = $svgQRCode->getDocument();
    $svgQRCodeDoc->setAttribute('x', 520);
    $svgQRCodeDoc->setAttribute('y', 200);
    $svgQRCodeDoc->setHeight(110);
    $svgQRCodeDoc->setWidth(110);
    $qrLayer->addChild($svgQRCodeDoc);

    if (!is_dir(storage_path(env('BOARDS_PATH')))) {
      mkdir(storage_path(env('BOARDS_PATH')));
    }

    file_put_contents(storage_path(env('BOARDS_PATH').$this->vegetation->uuid.'.svg'), $svgTemplate);
  }

  /**
   * @param string $style
   * @param string $text
   * @param int $fontSize
   * @param int $yValue
   * @param bool $bold
   * @return SVGText
   */
  private function createTextNode(string $style, string $text, int $fontSize, int $yValue, bool $bold): SVGText
  {
    $textNode = new SVGText($text);
    $textNode->setAttribute('font-family', 'Roboto, Roboto-Regular, ArialMT, Arial, sans-serif');
    $textNode->setAttribute('font-style', $style);
    $textNode->setAttribute('font-size', $fontSize);
    $textNode->setAttribute('dominant-baseline', 'middle');
    $textNode->setAttribute('text-anchor', 'middle');
    $textNode->setAttribute('x', '50%');
    $textNode->setAttribute('y', $yValue);

    if($bold) {
      $textNode->setAttribute(' font-weight', 'bold');
    }

    return $textNode;
  }
}
