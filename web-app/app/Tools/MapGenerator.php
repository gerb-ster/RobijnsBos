<?php

namespace App\Tools;

use App\Models\SpeciesType;
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

  private array $mapAssets;

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

    SpeciesType::all()->each(function (SpeciesType $speciesType) {
      $assetFile = SVG::fromFile(resource_path("images/assets/{$speciesType->name}.svg"));
      $this->mapAssets[$speciesType->name] = $assetFile->getDocument();
    });

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

  private function calculateTreeSize(Vegetation $vegetation): float
  {
    $year = (int) preg_replace("/[^0-9]/", "", $vegetation->placed);
    $treeAge = Carbon::now()->year - $year;
    $maxTreeHeight = (int) preg_replace("/[^0-9.]/", "", $vegetation->species->height);
    $maxTreeSize = ($maxTreeHeight / 2) * 20;

    if ($treeAge <= 10) {
      return $maxTreeSize * ($treeAge / 10);
    } else {
      return $maxTreeSize;
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

    $assetNode = clone $this->mapAssets[$speciesType];
    $vegetationSize = 120;

    if (in_array($speciesType, ['Kroonboom', 'Fruitboom'])) {
      $vegetationSize = $this->calculateTreeSize($vegetation);
    }

    $assetNode->setAttribute('width', $vegetationSize);
    $assetNode->setAttribute('height', $vegetationSize);

    $assetNode->setAttribute('x', $calculatedLocation['x'] - $vegetationSize / 2);
    $assetNode->setAttribute('y', $calculatedLocation['y'] - $vegetationSize / 2);

    return $assetNode;
  }
}
