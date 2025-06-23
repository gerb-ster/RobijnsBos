<?php

namespace App\Tools;

use App\Models\Vegetation;
use App\Models\VegetationStatus;
use Carbon\Carbon;
use DOMDocument;
use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Structures\SVGLinkGroup;
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
      $vegetationTextLayer->addChild($this->createTextNode($calculatedLocation, $vegetation));
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
    $xOrigin = 2988;
    $yOrigin = 5220;
    $stepSizeW = 117.6;
    $stepSizeH = 118.1;

    return [
      'x' =>  $xOrigin + ((float) $location['x'] * $stepSizeW),
      'y' =>  $yOrigin - ((float) $location['y'] * $stepSizeH),
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
        return 90.0;
      case $treeAge > 20:
        return 120.0;
    }
  }

  /**
   * @param array $location
   * @param Vegetation $vegetation
   * @return SVGGroup
   */
  private function createTextNode(array $location, Vegetation $vegetation): SVGGroup
  {
    $group = new SVGGroup();
    $link = new SVGLinkGroup();

    $textNode = new SVGText($vegetation->species->dutch_name);

    $textNode->setAttribute('font-family', 'ArialMT, Arial, sans-serif');
    $textNode->setAttribute('font-size', 16);
    $textNode->setAttribute('dominant-baseline', 'middle');
    $textNode->setAttribute('text-anchor', 'middle');

    $textNode->setAttribute('x', $location['x']);
    $textNode->setAttribute('y', $location['y'] - 8);

    $cordNode = new SVGText($vegetation->location['x'] . ", " . $vegetation->location['y']);

    $textNode->setAttribute('font-family', 'ArialMT, Arial, sans-serif');
    $cordNode->setAttribute('font-size', 18);
    $cordNode->setAttribute('dominant-baseline', 'middle');
    $cordNode->setAttribute('text-anchor', 'middle');

    $cordNode->setAttribute('x', $location['x']);
    $cordNode->setAttribute('y', $location['y'] + 12);

    $link->setAttribute('xlink:href', route('public.vegetation.redirect', ['shortCode' => $vegetation->qr_shortcode]));
    $link->setAttribute('xlink:show',"new");
    $link->addChild($textNode);
    $link->addChild($cordNode);

    $group->addChild($link);

    return $group;
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
