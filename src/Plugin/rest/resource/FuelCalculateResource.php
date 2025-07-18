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
 * Provides a REST resource for calculating fuel costs.
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
      $container->get('logger.factory')->get('fuel_calculator_resource'),
      $container->get('fuel_calculator.fuel_calculator'),
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
    $user_name = !empty($this->currentUser->getAccountName()) ? $this->currentUser->getAccountName() : 'anonymous';
    $ip = $this->requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown';
    try {
      $fuel_data = FuelDataVO::fromArray($data);
    }
    catch (\InvalidArgumentException $e) {
      $this->logger->error('
        Invalid input data error: @message,
        Data: @data,
        Ip: @ip,
        User: @user',
        [
          '@message' => $e->getMessage(),
          '@data' => json_encode($data),
          '@ip' => $ip,
          '@user' => $user_name,
        ]
      );

      return new ResourceResponse(['error' => $e->getMessage()], 400);
    }

    $fuel_cost = $this->fuelCalculator->getFuelCost($fuel_data->distanceTravelled, $fuel_data->fuelConsumption, $fuel_data->pricePerLiter);
    $fuel_spent = $this->fuelCalculator->getFuelSpent($fuel_data->distanceTravelled, $fuel_data->fuelConsumption);

    $this->logger->info('
      Success,
      Data: @data,
      Fuel cost calculated: @cost,
      Fuel spent: @spent,
      Ip: @ip,
      User: @user',
      [
        '@data' => json_encode($data),
        '@cost' => $fuel_cost,
        '@spent' => $fuel_spent,
        '@ip' => $ip,
        '@user' => $user_name,
      ]
    );

    return new ResourceResponse([
      FuelKeys::FuelCost->value => $fuel_cost,
      FuelKeys::FuelSpent->value => $fuel_spent,
    ]);
  }

}
