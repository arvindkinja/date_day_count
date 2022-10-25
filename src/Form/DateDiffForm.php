<?php

namespace Drupal\date_day_count\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\date_day_count\DateDiffDayCount;

/**
 * The DateDiffForm class.
 */
class DateDiffForm extends FormBase {

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The DateDiffDayCount service.
   *
   * @var \Drupal\date_day_count\DateDiffDayCount
   */
  protected $dateDiffDayCount;

  /**
   * DateDiffForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\date_day_count\DateDiffDayCount $dateDiffDayCount
   *   The DateDiffDayCount service.
   */
  public function __construct(MessengerInterface $messenger, DateDiffDayCount $dateDiffDayCount) {
    $this->messenger = $messenger;
    $this->dateDiffDayCount = $dateDiffDayCount;
  }

  /**
   * The create method.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container service.
   *
   * @return object
   *   The container object.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('date_day_count.day_count'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'date_diff_count_number_of_days';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['start_date'] = [
      '#type' => 'date',
      '#title' => t('Start Date'),
      '#required' => TRUE,
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#title' => t('End Date'),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('end_date') < $form_state->getValue('start_date')) {
      $form_state->setErrorByName('end_date', $this->t('End Date should be grater than Start Date.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');
    // Get the number of days.
    $days = $this->dateDiffDayCount->dayCount($start_date, $end_date);
    $this->messenger->addMessage(t("The number of days between :start_date and 
    :end_date is :days", [
      ':start_date' => $start_date,
      ':end_date' => $end_date,
      ':days' => $days,
    ]));
  }

}
