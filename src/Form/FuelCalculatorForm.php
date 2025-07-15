<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for the Fuel Calculator.
 */
class FuelCalculatorForm extends FormBase {

  const string FORM_ID = 'fuel_calculator_form';

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return self::FORM_ID;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(SettingsForm::CONFIG_NAME);

    $form['#attached']['library'][] = 'fuel_calculator/fuel_calculator';

    $form['distance_travelled'] = [
      '#type' => 'number',
      '#title' => $this->t('Distance Travelled'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the distance travelled in kilometers.'),
      '#default_value' => $config->get('distance_travelled') ?? '',
    ];

    $form['fuel_consumption'] = [
      '#type' => 'number',
      '#step' => '0.1',
      '#title' => $this->t('Fuel Consumption'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the fuel consumption in liters per 100 kilometers.'),
      '#default_value' => $config->get('fuel_consumption') ?? '',
    ];

    $form['price_per_liter'] = [
      '#type' => 'number',
      '#step' => '0.01',
      '#title' => $this->t('Fuel Price'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the fuel price in your local currency per liter.'),
      '#default_value' => $config->get('price_per_liter') ?? '',
    ];

    $form['results'] = [
      '#type' => 'fieldset',
    ];

    $form['results']['fuel_spent'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fuel Spent'),
      '#attributes' => [
        'readonly' => 'readonly',
      ],
    ];

    $form['results']['fuel_cost'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fuel Costs'),
      '#attributes' => [
        'readonly' => 'readonly',
      ],
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
    // Handle form submission logic here.
    $this->messenger()->addMessage($this->t('Fuel calculation submitted successfully.'));
  }

}
