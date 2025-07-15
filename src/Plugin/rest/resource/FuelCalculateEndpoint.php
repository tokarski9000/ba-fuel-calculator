<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Plugin\rest\resource;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rest\Attribute\RestResource;
use Drupal\rest\Plugin\ResourceBase;

/**
 * Represents entities as resources.
 *
 * @see \Drupal\rest\Plugin\Deriver\EntityDeriver
 */
#[RestResource(
  id: "fuel_calculate_endpoint",
  label: new TranslatableMarkup("Fuel Calculate Endpoint"),
  serialization_class: "Drupal\Core\Entity\Entity",
  uri_paths: [
    "canonical" => "/entity/{entity_type}/{entity}",
    "create" => "/entity/{entity_type}",
  ],
)]
final class FuelCalculateEndpoint extends ResourceBase {

  /**
   * {@inheritdoc}
   */
  public function getResourceType(): string {
    return 'fuel_calculate';
  }

  /**
   * {@inheritdoc}
   */
  public function getMethods(): array {
    return [
      'POST' => [
        'callback' => 'calculateFuelCost',
        'access callback' => TRUE,
      ],
    ];
  }

  /**
   * Calculate fuel cost based on input data.
   *
   * @param array $data
   *   The input data containing distance, consumption, and price.
   *
   * @return array
   *   The calculated fuel cost.
   */
  public function calculateFuelCost(array $data): array {
    // Implement the calculation logic here.
    // This is a placeholder for the actual calculation logic.
    return ['cost' => 0.0];
  }

}
