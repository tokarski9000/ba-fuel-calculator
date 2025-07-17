<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Enum;

/**
 * Enum for Fuel Keys.
 *
 * This enum defines the keys used in the FuelDataVO and FuelCalculatorService.
 */
enum FuelKeys: string {
  case DistanceTravelled = 'distance_travelled';
  case FuelConsumption = 'fuel_consumption';
  case PricePerLiter = 'price_per_liter';
  case FuelSpent = 'fuel_spent';
  case FuelCost = 'fuel_cost';
}
