<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\VO;

use Drupal\fuel_calculator\Enum\FuelKeys;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

/**
 * Value Object for Fuel Data.
 *
 * This class encapsulates the data related to fuel consumption and distance
 * travelled, providing a structured way to handle this information.
 */
final class FuelDataVO {

  public function __construct(
    public float $distanceTravelled,
    public float $fuelConsumption,
    public float $costPerLiter,
  ) {
    // Ensure that distance travelled and fuel consumption are non-negative.
    if ($distanceTravelled < 0 || $fuelConsumption < 0 || $costPerLiter < 0) {
      throw new \InvalidArgumentException('Distance travelled, fuel consumption, and cost per liter must be non-negative.');
    }
  }

  /**
   * Converts array to FuelDataVO.
   *
   * @param array $data
   *   An associative array containing the fuel data.
   *
   * @return \Drupal\fuel_calculator\VO\FuelDataVO
   *   The FuelDataVO instance.
   */
  public static function fromArray(array $data): self {
    $validator = Validation::createValidator();
    $violations = $validator->validate($data, new Collection([
      FuelKeys::DistanceTravelled->value => [
        new NotBlank(),
        new Type(['type' => 'numeric']),
      ],
      FuelKeys::FuelConsumption->value => [
        new NotBlank(),
        new Type(['type' => 'numeric']),
      ],
      FuelKeys::PricePerLiter->value => [
        new NotBlank(),
        new Type(['type' => 'numeric']),
      ],
    ]));

    if (count($violations) > 0) {
      throw new \InvalidArgumentException('Invalid data provided: ' . (string) $violations);
    }

    return new self(
      (float) $data[FuelKeys::DistanceTravelled->value],
      (float) $data[FuelKeys::FuelConsumption->value],
      (float) $data[FuelKeys::PricePerLiter->value],
    );
  }

}
