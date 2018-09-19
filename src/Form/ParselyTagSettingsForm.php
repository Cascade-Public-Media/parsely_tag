<?php

namespace Drupal\parsely_tag\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ParselyTagSettingsForm.
 *
 * @ingroup parsely_tag
 */
class ParselyTagSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'parsely_tag_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'parsely_tag.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('parsely_tag.settings');

    $form['site_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Parse.ly Site ID'),
      '#description' => $this->t('You can find your Site ID on your your 
        <a target="_blank" href="@url">API settings page</a>.',
        ['@url' => 'http://dash.parsely.com/to/settings/api?highlight=apikey']),
      '#default_value' => $config->get('site_id'),
      '#required' => TRUE,
    ];

    $form['property_defaults'] = [
      '#type' => 'details',
      '#title' => $this->t('Content Type defaults'),
      '#description' => $this->t('These values wil be used as the defaults
        for all content types. Visit <a href="@url">Content types</a> to
        override these defaults for specific content types.', [
          '@url' => '/admin/structure/types',
        ]),
      '#open' => TRUE,
    ];

    $form['property_defaults']['enable'] = [
      '#title' => $this->t('Enable the Parse.ly tag.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('default_enable'),
    ];

    foreach (_parsely_tag_properties() as $property => $info) {
      $form['property_defaults'][$property] = [
        '#title' => $info['title'],
        '#description' => $info['description'],
        '#type' => 'textfield',
        '#default_value' => $config->get('default_' . $property),
        '#element_validate' => ['token_element_validate'],
        '#token_types' => ['node'],
      ];
    }

    $form['property_defaults']['token_tree'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['node'],
      '#show_restricted' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('parsely_tag.settings')
      ->set('site_id', $values['site_id']);
    $this->config('parsely_tag.settings')
      ->set('default_enable', $values['enable']);
    foreach (_parsely_tag_properties() as $property => $info) {
      $this->config('parsely_tag.settings')
        ->set('default_' . $property, $values[$property]);
    }
    $this->config('parsely_tag.settings')->save();
    parent::submitForm($form, $form_state);
  }

}
