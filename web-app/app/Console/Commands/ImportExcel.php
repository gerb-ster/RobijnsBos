<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\Group;
use App\Models\Species;
use App\Models\SpeciesType;
use App\Models\Vegetation;
use App\Models\VegetationStatus;
use Exception;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExcel extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'import:excel {file}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Import Excel file';

  private ?int $currentAreaId = null;

  private ?int $currentGroupId = null;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle(): int
  {
    // make sure we're not going to send any emails
    $this->comment("Importing data into database...\n\n");

    $spreadsheet = IOFactory::load(app_path('../') . $this->argument('file'));
    $worksheet = $spreadsheet->getSheetByName('Beplantingslijst Robijnsbos');

    $rowIterator = $worksheet->getRowIterator();
    $headers = [];

    foreach ($rowIterator as $rowCounter => $row) {
      if ($row->isEmpty()) { // Ignore empty rows
        continue;
      }

      $cellIterator = $row->getCellIterator();
      $cellCounter = 1;
      $rowData = [];

      foreach ($cellIterator as $cell) {
        if ($rowCounter === 1) {
          $headers[$cell->getColumn()] = $cell->getValue();
        } else {
          if ($cellCounter === 1) {
            if ($cell->getValue() === null) {
              continue;
            }

            $color = $cell->getAppliedStyle()->getFill()->getStartColor()->getRGB();
            $fontSize = $cell->getAppliedStyle()->getFont()->getSize();

            if ($color === '93C47D' || $color === '93C47E') {
              $this->handleGroupLine($cell->getValue());
              continue;
            }

            if ($fontSize === 14.0) {
              $this->handleAreaLine($cell->getValue());
              continue;
            }
          }

          $rowData[$headers[$cell->getColumn()]] = $cell->getValue();
        }

        $cellCounter++;
      }

      if (!empty($rowData)) {
        try {
          $species = $this->findSpecies($rowData);

          $status = $this->determineStatus($rowData);

          Vegetation::create([
            'specie_id' => $species->id,
            'status_id' => $status,
            'group_id' => $this->currentGroupId,
            'location' => [
              'x' => $rowData['X'],
              'y' => $rowData['Y']
            ],
            'label' => $species->dutch_name . "-" . $rowData['X'] . "-" . $rowData['Y'],
            'amount' => 1,
            'placed' => $rowData['Plantjaar'],
            'remarks' => $rowData['Opmerking'],
            'created_by' => 1
          ]);
        } catch (Exception $exception) {
          $this->error($exception->getMessage());
        }
      }
    }

    $this->comment('🏁 Done!');

    return 0;
  }

  /**
   * @param string $value
   * @return void
   */
  private function handleAreaLine(string $value): void
  {
    $area = Area::updateOrCreate(['name' => $value]);
    $this->currentAreaId = $area->id;
  }

  private function handleGroupLine(string $value): void
  {
    $group = Group::updateOrCreate(['name' => $value, 'area_id' => $this->currentAreaId]);
    $this->currentGroupId = $group->id;
  }

  /**
   * @param array $row
   * @return Species
   */
  private function findSpecies(array $row): Species
  {
    $blossomMonth = explode(",", $row['Bloeimaand']);

    array_walk($blossomMonth, function (&$value, $key) {
      $value = $this->translateMonth(strtolower(trim($value)));
    });

    if (empty($row['Type'])) {
      ray($row);
    }

    $typeId = SpeciesType::firstOrCreate(['name' => $row['Type']], ['name' => $row['Type']]);

    if (empty($row['Latijnse naam'])) {
      ray($row);
    }

    return Species::updateOrCreate([
      'latin_name' => $row['Latijnse naam']
    ], [
      'dutch_name' => $row['Nederlandse naam'],
      'blossom_month' => $blossomMonth,
      'height' => $row['Hoogte'],
      'type_id' => $typeId->id
    ]);
  }

  /**
   * @param string $month
   * @return string
   */
  private function translateMonth(string $month): string
  {
    return match ($month) {
      'januari' => 'january',
      'februari' => 'february',
      'maart' => 'march',
      'april' => 'april',
      'mei' => 'may',
      'juni' => 'june',
      'juli' => 'july',
      'augustus' => 'august',
      'september' => 'september',
      'oktober' => 'october',
      'november' => 'november',
      'december' => 'december',
      default => $month,
    };
  }

  /**
   * @param array $row
   * @return int
   */
  private function determineStatus(array $row): int
  {
    $yearInNumbers = intval(preg_replace("/[^0-9]/", "", strtolower(trim($row['Plantjaar']))));

    if ($yearInNumbers === 0) {
      return VegetationStatus::TO_BO_PLANTED;
    }

    if ($yearInNumbers <= 2025) {
      return VegetationStatus::PLANTED;
    }

    return VegetationStatus::TO_BO_PLANTED;
  }
}
