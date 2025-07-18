<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Form;

use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fuel_calculator\Enum\FuelKeys;
use Drupal\fuel_calculator\Service\FuelCalculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for the Fuel Calculator.
 */
final class FuelCalculatorForm extends FormBase {

  const string FORM_ID = 'fuel_calculator_form';

  public function __construct(
    RequestStack $requestStack,
    protected LoggerInterface $logger,
    protected FuelCalculator $fuelCalculator,
  ) {
    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('request_stack'),
      $container->get('logger.factory')->get('fuel_calculator_form'),
      $container->get('fuel_calculator.fuel_calculator'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return self::FORM_ID;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attached']['library'][] = 'fuel_calculator/fuel_calculator';

    $form[FuelKeys::DistanceTravelled->value] = [
      '#type' => 'number',
      '#title' => $this->t('Distance Travelled'),
      '#min' => 0,
      '#required' => TRUE,
      '#default_value' => $this->getInputDefaultValue(FuelKeys::DistanceTravelled->value),
      '#field_suffix' => ' km',
    ];

    $form[FuelKeys::FuelConsumption->value] = [
      '#type' => 'number',
      '#step' => '0.1',
      '#min' => 0,
      '#title' => $this->t('Fuel Consumption'),
      '#required' => TRUE,
      '#default_value' => $this->getInputDefaultValue(FuelKeys::FuelConsumption->value),
      '#field_suffix' => ' l/100 km',
    ];

    $form[FuelKeys::PricePerLiter->value] = [
      '#type' => 'number',
      '#step' => '0.01',
      '#min' => 0,
      '#title' => $this->t('Fuel Price'),
      '#required' => TRUE,
      '#default_value' => $this->getInputDefaultValue(FuelKeys::PricePerLiter->value),
      '#field_suffix' => ' / EUR',
    ];

    $form['results'] = [
      '#type' => 'fieldset',
    ];

    $form['results'][FuelKeys::FuelSpent->value] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fuel Spent'),
      '#attributes' => [
        'readonly' => 'readonly',
      ],
      '#value' => $form_state->getValue(FuelKeys::FuelSpent->value),
      '#field_suffix' => ' liters',
    ];

    $form['results'][FuelKeys::FuelCost->value] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fuel Cost'),
      '#attributes' => [
        'readonly' => 'readonly',
      ],
      '#value' => $form_state->getValue(FuelKeys::FuelCost->value),
      '#field_suffix' => ' EUR',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['calculate'] = [
      '#type' => 'submit',
      '#value' => $this->t('Calculate'),
      '#button_type' => 'primary',
    ];

    $form['actions']['reset'] = [
      '#type' => 'button',
      '#value' => $this->t('Reset'),
      '#button_type' => 'secondary',
      '#attributes' => [
        'id' => 'fuel-calculator-reset',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $distance = (float) $form_state->getValue(FuelKeys::DistanceTravelled->value);
    $consumption = (float) $form_state->getValue(FuelKeys::FuelConsumption->value);
    $price = (float) $form_state->getValue(FuelKeys::PricePerLiter->value);
    $fuel_spent = $this->fuelCalculator->getFuelSpent($distance, $consumption);
    $fuel_cost = $this->fuelCalculator->getFuelCost($distance, $consumption, $price);

    $form_state->setValue(FuelKeys::FuelSpent->value, $fuel_spent);
    $form_state->setValue(FuelKeys::FuelCost->value, $fuel_cost);
    $form_state->setRebuild(TRUE);

    $this->logSubmission([
      FuelKeys::DistanceTravelled->value => $distance,
      FuelKeys::FuelConsumption->value => $consumption,
      FuelKeys::PricePerLiter->value => $price,
      FuelKeys::FuelSpent->value => $fuel_spent,
      FuelKeys::FuelCost->value => $fuel_cost,
    ]);
  }

  /**
   * Get the default value for a form element.
   *
   * @param string $key
   *   The configuration key to retrieve the default value for.
   *
   * @return float|null
   *   The default value for the form element.
   */
  protected function getInputDefaultValue(string $key): ?float {
    $config = $this->config(SettingsForm::CONFIG_NAME);
    $param = $this->requestStack->getCurrentRequest()?->get($key);

    if ($param !== NULL && is_numeric($param)) {
      return (float) $param;
    }

    $config_value = $config->get($key);
    if ($config_value && is_numeric($config_value)) {
      return (float) $config_value;
    }

    return NULL;
  }

  /**
   * Log the submission data.
   *
   * @param array $data
   *   The data submitted in the form.
   */
  protected function logSubmission(array $data): void {
    $user_name = $this->currentUser()->getAccountName() ?: 'anonymous';
    $ip = $this->requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown';

    $this->logger->info('
      Fuel Calculator submission,
      Data: @data,
      Ip: @ip,
      User: @user',
      [
        '@data' => json_encode($data),
        '@ip' => $ip,
        '@user' => $user_name,
      ]
    );
  }

}
