<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Service;

/**
 * Service for calculating fuel costs.
*/
final class FuelCalculator {

  /**
   * Multiplier for float to int conversion.
   *
   * @var int
   */
  const int MULTIPLIER = 1000;

  /**
   * Calculate the amount of fuel spent based on distance and consumption.
   *
   * @param float $distance
   *   The distance travelled in kilometers.
   * @param float $consumption
   *   The fuel consumption in liters per 100 kilometers.
   *
   * @return float
   *   The amount of fuel spent in liters.
   */
  public function getFuelSpent(float $distance, float $consumption): float {
    // Validate inputs.
    if ($distance < 0 || $consumption < 0) {
      throw new \InvalidArgumentException('Distance and consumption must be non-negative.');
    }

    return round(
      $this->getLiterUsed($distance, $consumption), 1);
  }

  /**
   * Calculate the total fuel cost based on distance, consumption, and price.
   *
   * @param float $distance
   *   The distance travelled in kilometers.
   * @param float $consumption
   *   The fuel consumption in liters per 100 kilometers.
   * @param float $price
   *   The price of fuel per liter.
   *
   * @return float
   *   The total fuel cost.
   */
  public function getFuelCost(
    float $distance,
    float $consumption,
    float $price,
  ): float {
    if ($distance < 0 || $consumption < 0 || $price < 0) {
      throw new \InvalidArgumentException('Distance, consumption, and price must be non-negative.');
    }

    $price = $price * self::MULTIPLIER;
    $litersUsed = $this->getLiterUsed($distance, $consumption) * self::MULTIPLIER;
    $totalCost = ($litersUsed * $price) /
                 (self::MULTIPLIER * self::MULTIPLIER);

    return round($totalCost, 1);
  }

  /**
   * Calculate the amount of fuel used based on distance and consumption.
   *
   * @param float $distance
   *   The distance travelled in kilometers.
   * @param float $consumption
   *   The fuel consumption in liters per 100 kilometers.
   *
   * @return float
   *   The amount of fuel used in liters.
   */
  protected function getLiterUsed(float $distance, float $consumption): float {
    // Calculate liter used and return to original units.
    return (($distance * self::MULTIPLIER / 100) * ($consumption * self::MULTIPLIER)) /
           (self::MULTIPLIER * self::MULTIPLIER);

  }

}
