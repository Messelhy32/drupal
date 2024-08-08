<?php

namespace Drupal\event_management\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

class EventDeleteForm extends ContentEntityDeleteForm
{

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Perform the deletion
        parent::submitForm($form, $form_state);

        // Redirect to the desired route after deletion
        $form_state->setRedirect('event_management.event_list');
    }

}
