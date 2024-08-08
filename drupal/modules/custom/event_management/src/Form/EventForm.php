<?php

namespace Drupal\event_management\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class EventForm extends ContentEntityForm
{

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        // Extract start_time and end_time from form state.
        $start_time_string = $form_state->getValue('start_time')[0]['value'];
        $end_time_string = $form_state->getValue('end_time')[0]['value'];

        // Debugging: Add a message to check the values.
        \Drupal::messenger()->addMessage($this->t('Start time: @start, End time: @end', [
            '@start' => $start_time_string,
            '@end' => $end_time_string,
        ]));

        // Perform a simple string comparison for now.
        if ($start_time_string > $end_time_string) {
            $form_state->setErrorByName('end_time', $this->t('The end time must be after the start time.'));
        }
    }

    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->getEntity();
        \Drupal::messenger()->addMessage($this->t('Category: @category', ['@category' => $entity->get('category')->value]));

        $status = parent::save($form, $form_state);

        if ($status == SAVED_UPDATED) {
            $this->messenger()->addMessage($this->t('Updated the %label Event.', [
                '%label' => $entity->label(),
            ]));
        }

        $form_state->setRedirect('event_management.event_view', ['event' => $entity->id()]);
    }


}
