<?php

declare(strict_types=1);

namespace Drupal\fuel_calculator\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for zerobounce.
 */
class SettingsForm extends ConfigFormBase {

  const string FORM_ID = 'fuel_calculator_settings';

  const string CONFIG_NAME = 'fuel_calculator.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      self::CONFIG_NAME,
    ];
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
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config(self::CONFIG_NAME);

    $form['distance_travelled'] = [
      '#type' => 'number',
      '#title' => $this->t('Distance Travelled'),
      '#default_value' => $config->get('distance_travelled') ?? '',
      '#description' => $this->t('Enter the distance travelled in kilometers.'),
    ];

    $form['fuel_consumption'] = [
      '#type' => 'number',
      '#step' => '0.1',
      '#title' => $this->t('Fuel Consumption'),
      '#default_value' => $config->get('fuel_consumption') ?? '',
      '#description' => $this->t('Enter the fuel consumption in liters per 100 kilometers.'),
    ];

    $form['price_per_liter'] = [
      '#type' => 'number',
      '#step' => '0.01',
      '#title' => $this->t('Fuel Price'),
      '#default_value' => $config->get('price_per_liter') ?? '',
      '#description' => $this->t('Enter the fuel price in your local currency per liter.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Settings'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config(self::CONFIG_NAME);

    $values = $form_state->cleanValues()->getValues();
    foreach ($values as $name => $value) {
      $config->set($name, $value);
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
