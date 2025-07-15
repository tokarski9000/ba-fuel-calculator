<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Service;

/**
 * Service for calculating fuel costs.
*/
final class FuelCalculator {

  /**
   * Calculate the amount of fuel spent based on distance and consumption.
   *
   * @param float $float_distance
   *   The distance travelled in kilometers.
   * @param float $float_consumption
   *   The fuel consumption in liters per 100 kilometers.
   *
   * @return float
   *   The amount of fuel spent in liters.
   */
  public function calculateFuelSpent(float $float_distance, float $float_consumption): float {
    // Validate inputs.
    if ($float_distance < 0 || $float_consumption < 0) {
      throw new \InvalidArgumentException('Distance and consumption must be non-negative.');
    }

    $distance = $float_distance * 1000;
    $consumption = $float_consumption * 1000;

    $litersUsed = ($distance / 100) * $consumption / 1000;

    return round($litersUsed, 2);
  }

  /**
   * Calculate the total fuel cost based on distance, consumption, and price.
   *
   * @param float $float_distance
   *   The distance travelled in kilometers.
   * @param float $float_consumption
   *   The fuel consumption in liters per 100 kilometers.
   * @param float $float_price
   *   The price of fuel per liter.
   *
   * @return float
   *   The total fuel cost.
   */
  public function calculateFuelCost(float $float_distance, float $float_consumption, float $float_price): float {
    // Validate inputs.
    if ($float_distance < 0 || $float_consumption < 0 || $float_price < 0) {
      throw new \InvalidArgumentException('Distance, consumption, and price must be non-negative.');
    }

    $distance = $float_distance * 1000;
    $consumption = $float_consumption * 1000;
    $price = $float_price * 1000;

    $litersUsed = ($distance / 100) * $consumption / 1000;
    $totalCost = $litersUsed * $price;

    return round($totalCost, 2);
  }

}
