<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Plugin\Block;

use Drupal\fuel_calculator\FuelCalculator;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a block to display the Fuel Calculator form.
 */
#[Block(
  id: "fuel_calculator_form_block",
  admin_label: new TranslatableMarkup("Fuel Calculator Form"),
  forms: [
    'settings_tray' => FALSE,
  ]
)]
final class FuelCalculatorFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected readonly FormBuilderInterface $formBuilder,
    protected readonly FuelCalculator $fuelCalculator,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
      $container->get('fuel_calculator.fuel_calculator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      'content' => $this->formBuilder->getForm('Drupal\fuel_calculator\Form\FuelCalculatorForm'),
    ];
  }

}
