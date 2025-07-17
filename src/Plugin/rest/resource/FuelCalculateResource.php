<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxy;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\fuel_calculator\Enum\FuelKeys;
use Drupal\fuel_calculator\Service\FuelCalculator;
use Drupal\fuel_calculator\VO\FuelDataVO;
use Drupal\rest\Attribute\RestResource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Represents entities as resources.
 *
 * @see \Drupal\rest\Plugin\Deriver\EntityDeriver
 */
#[RestResource(
  id: "fuel_calculate_resource",
  label: new TranslatableMarkup("Fuel Calculate Resource"),
  uri_paths: [
    "create" => "/api/fuel-calculate",
  ],
)]
final class FuelCalculateResource extends ResourceBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    protected FuelCalculator $fuelCalculator,
    protected AccountProxy $currentUser,
    protected RequestStack $requestStack,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    /** @var array $serializer_formats */
    $serializer_formats = $container->getParameter('serializer.formats');

    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $serializer_formats,
      $container->get('logger.factory')->get('fuel_calculate_resource'),
      $container->get('fuel_calculator'),
      $container->get('current_user'),
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceType(): string {
    return 'fuel_calculate';
  }

  /**
   * Calculate fuel cost based on input data.
   *
   * @param array $data
   *   The input data containing distance, consumption, and price.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The calculated fuel cost response.
   */
  public function post(array $data): ResourceResponse {
    try {
      $fuel_data = FuelDataVO::fromArray($data);
    }
    catch (\InvalidArgumentException $e) {
      $this->logger->error('Invalid input data: @message', ['@message' => $e->getMessage()]);
      return new ResourceResponse(['error' => $e->getMessage()], 400);
    }

    $fuel_cost = $this->fuelCalculator->getFuelCost($fuel_data->distanceTravelled, $fuel_data->fuelConsumption, $fuel_data->costPerLiter);
    $fuel_spent = $this->fuelCalculator->getFuelSpent($fuel_data->distanceTravelled, $fuel_data->fuelConsumption);

    $this->logger->info('
    Ip: @ip,
    User: @user,
    Distance travelled: @distance,
    Fuel consumption: @consumption,
    Price per liter: @price,
    Fuel cost calculated: @cost,
    Fuel spent: @spent', [
      '@ip' => $this->requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown',
      '@user' => $this->currentUser->getAccountName(),
      '@distance' => $fuel_data->distanceTravelled,
      '@consumption' => $fuel_data->fuelConsumption,
      '@price' => $fuel_data->costPerLiter,
      '@cost' => $fuel_cost,
      '@spent' => $fuel_spent,
    ]);
    return new ResourceResponse([
      FuelKeys::FuelCost->value => $fuel_cost,
      FuelKeys::FuelSpent->value => $fuel_spent,
    ]);
  }

}
