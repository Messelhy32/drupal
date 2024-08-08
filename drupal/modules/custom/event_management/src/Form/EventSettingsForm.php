<?php
namespace Drupal\event_management\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EventSettingsForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['event_management.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'event_management_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('event_management.settings');

        $form['show_past_events'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show past events'),
            '#description' => $this->t('Check this box to display past events in the event list.'),
            '#default_value' => $config->get('show_past_events'),
        ];

        $form['events_per_page'] = [
            '#type' => 'number',
            '#title' => $this->t('Number of events to list per page'),
            '#description' => $this->t('Set the number of events to display per page on the event listing.'),
            '#default_value' => $config->get('events_per_page'),
            '#min' => 1,
            '#max' => 100,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->config('event_management.settings')
            ->set('show_past_events', $form_state->getValue('show_past_events'))
            ->set('events_per_page', $form_state->getValue('events_per_page'))
            ->save();

        // Log the configuration change
        $connection = \Drupal::database();
        $connection->insert('event_management_config_log')
            ->fields([
                'changed_time' => \Drupal::time()->getRequestTime(),
                'config_name' => 'event_management.settings',
                'config_values' => serialize($form_state->getValues()),
            ])
            ->execute();

        parent::submitForm($form, $form_state);
    }
}
