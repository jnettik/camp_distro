<?php

namespace Drupal\camp\Form;

use Drupal\camp\CampInstallerPluginManager;
use Drupal\Core\Extension\InfoParserInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\camp\CampInstallerInterface;

/**
 * Defines form for selecting extra components for the assembler to install.
 */
class AssemblerForm extends FormBase {

  /**
   * The Camp Installer Feature manager
   * @var \Drupal\camp\CampInstallerPluginManager
   */
  protected $campFeatureManager;

  /**
   * Constructor.
   *
   * @param \Drupal\camp\CampInstallerPluginManager $campFeatureManager
   */
  public function __construct(CampInstallerPluginManager $campFeatureManager) {
    $this->campFeatureManager = $campFeatureManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'camp_extra_components';
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Extra compoments modules.
   */
  public function buildForm(array $form, FormStateInterface $form_state, array &$install_state = NULL) {
    $form = array();

    $form['#title'] = $this->t('Configure Features');

    $features = $this->campFeatureManager->getDefinitions();
    foreach ($features AS $feature => $feature_info){
      /** @var \Drupal\camp\CampInstallerBase $plugin */
      $plugin = $this->campFeatureManager->createInstance($feature);
      $feature_form = $plugin->buildForm(array(), $form_state) ?: array();
      if(count($feature_form) > 0) {
        $form[$feature] = [
          '#type' => 'fieldset',
          '#title' => $feature_info['title'],
          '#tree' => TRUE,
          '#description' => $feature_info['description']
        ];
        $form[$feature] = array_merge($form[$feature], $feature_form);
      }
    }

    $form['actions'] = [
      'continue' => [
        '#type' => 'submit',
        '#value' => $this->t('Assemble and install'),
        '#button_type' => 'primary',
      ],
      '#type' => 'actions',
      '#weight' => 5,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.camp_installer')
    );
  }
}