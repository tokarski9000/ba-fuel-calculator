(function (Drupal, once) {
  'use strict';

  function FuelCalculator($element) {
    this.$form = $element;
    this.$resetButton = this.$form.querySelector('#fuel-calculator-reset');
    this.reset();
  }

  FuelCalculator.prototype.reset = function () {
    this.$resetButton.addEventListener('click', (e) => {
      e.preventDefault();
      const $inputs = this.$form.querySelectorAll('input[type="number"]');
      $inputs.forEach((input) => {
        input.value = '';
      });
    });

  }


  Drupal.behaviors.fuel_calculator = {
    attach: function (context) {
      once('fuel-calculator', '.fuel-calculator-form', context).forEach(function (element, context) {
        new FuelCalculator(element);
      });
    }
  }
})(Drupal, once);
console.log('wokjdosakdo')
