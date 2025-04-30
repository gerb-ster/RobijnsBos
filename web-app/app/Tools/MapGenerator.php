<?php

namespace App\Tools;

use App\Models\Vegetation;
use App\Models\VegetationStatus;
use SVG\Nodes\Embedded\SVGImage;
use SVG\Nodes\Texts\SVGText;
use SVG\SVG;

class MapGenerator
{
  private SVG $mapFile;

  /**
   *
   */
  function __construct()
  {
    $this->setupBasicMap();
  }

  /**
   * @return void
   */
  private function setupBasicMap():void
  {
    SVG::addFont(resource_path('/fonts/Roboto-Bold.ttf'));
    SVG::addFont(resource_path('/fonts/Roboto-Italic.ttf'));
    SVG::addFont(resource_path('/fonts/Roboto-Regular.ttf'));

    $this->mapFile = SVG::fromFile(resource_path("images/empty_map.svg"));
  }

  /**
   * @return void
   */
  public function render(): void
  {
    $allVegetation = Vegetation::where('status', VegetationStatus::PLANTED)->get();
    $templateDoc = $this->mapFile->getDocument();

    $allVegetation->each(function (Vegetation $vegetation) use ($templateDoc) {
      $templateDoc->addChild($this->createImageNode($vegetation));
    });

    // write to file
    if (!is_dir(storage_path(env('MAP_PATH')))) {
      mkdir(storage_path(env('MAP_PATH')));
    }

    file_put_contents(storage_path(env('MAP_PATH').'full_map.svg'), $this->mapFile);
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
    $textNode->setAttribute('font-family', 'Roboto, Roboto-Regular');
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

  /**
   * @param Vegetation $vegetation
   * @return SVGImage
   */
  private function createImageNode(Vegetation $vegetation): SVGImage
  {
    $imageNode = new SVGImage();
    $imageNode->setAttribute('x', '50%');
    $imageNode->setAttribute('y', '50%');

    return $imageNode;
  }
}
