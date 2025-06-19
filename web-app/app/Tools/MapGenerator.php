<?php

namespace App\Tools;

use App\Models\Vegetation;
use App\Models\VegetationStatus;
use Carbon\Carbon;
use DOMDocument;
use SVG\Nodes\Embedded\SVGImage;
use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\Nodes\SVGNode;
use SVG\Nodes\Texts\SVGText;
use SVG\SVG;

class MapGenerator
{
  private SVG $mapFile;

  private SVGDocumentFragment $mapAssets;

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

    $assetsFile = SVG::fromFile(resource_path("images/map_assets.svg"));
    $this->mapAssets = $assetsFile->getDocument();

    $this->mapFile = SVG::fromFile(resource_path("images/empty_map.svg"));
  }

  /**
   * @return void
   */
  public function render(): void
  {
    $allVegetation = Vegetation::where('status_id', VegetationStatus::PLANTED)->get();
    $templateDoc = $this->mapFile->getDocument();

    $vegetationLayer = $templateDoc->getElementById('Beplanting');
    $vegetationTextLayer = $templateDoc->getElementById('Benaming-Beplanting');

    $allVegetation->each(function (Vegetation $vegetation) use ($vegetationLayer, $vegetationTextLayer) {
      $calculatedLocation = $this->calculateLocation($vegetation->location);

      $vegetationLayer->addChild($this->createImageNode($calculatedLocation, $vegetation));
      $vegetationTextLayer->addChild($this->createTextNode($calculatedLocation, $vegetation->species->dutch_name, 15, false));
    });

    // clean up XML
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->loadXML($this->mapFile->toXMLString());
    $dom->formatOutput = true;

    // write to file
    if (!is_dir(storage_path(env('MAP_PATH')))) {
      mkdir(storage_path(env('MAP_PATH')));
    }

    file_put_contents(storage_path(env('MAP_PATH').'full_map.svg'), $dom->saveXML());
  }

  /**
   * @param array $location
   * @return float[]
   */
  private function calculateLocation(array $location): array
  {
    $xOrigin = 2867;
    $yOrigin = 5098;
    $stepSize = 120;

    return [
      'x' =>  $xOrigin + ((float) $location['x'] * $stepSize),
      'y' =>  $yOrigin - ((float) $location['y'] * $stepSize),
    ];
  }

  private function calculateTreeSize(string $placed): float
  {
    $year = (int) preg_replace("/[^0-9]/", "", $placed );
    $treeAge = Carbon::now()->year - $year;

    switch ($treeAge) {
      case $treeAge <= 10:
        return 60.0;
      case $treeAge > 10 && $treeAge <= 20:
        return 120.0;
      case $treeAge > 20:
        return 180.0;
    }
  }

  /**
   * @param array $location
   * @param string $text
   * @param int $fontSize
   * @param bool $bold
   * @return SVGText
   */
  private function createTextNode(array $location, string $text, int $fontSize, bool $bold): SVGText
  {
    $textNode = new SVGText($text);

    $textNode->setAttribute('font-family', 'Roboto, Roboto-Regular');
    $textNode->setAttribute('font-size', $fontSize);
    $textNode->setAttribute('dominant-baseline', 'middle');
    $textNode->setAttribute('text-anchor', 'middle');

    $textNode->setAttribute('x', $location['x']);
    $textNode->setAttribute('y', $location['y']);

    if($bold) {
      $textNode->setAttribute(' font-weight', 'bold');
    }

    return $textNode;
  }

  /**
   * @param array $calculatedLocation
   * @param Vegetation $vegetation
   * @return SVGNode
   */
  private function createImageNode(array $calculatedLocation, Vegetation $vegetation): SVGNode
  {
    $speciesType = $vegetation->species->type->name;

    $assetNode = clone $this->mapAssets->getElementById($speciesType);

    $assetNode->setAttribute('cx', $calculatedLocation['x']);
    $assetNode->setAttribute('cy', $calculatedLocation['y']);

    if (in_array($speciesType, ['kroon_boom', 'fruit_boom'])) {
      $assetNode->setAttribute('r', $this->calculateTreeSize($vegetation->placed));
    }

    return $assetNode;
  }
}
