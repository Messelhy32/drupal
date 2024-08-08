<?php
namespace Drupal\event_management\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the Event entity.
 *
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   base_table = "event",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\event_management\Form\EventForm",
 *       "edit" = "Drupal\event_management\Form\EventForm",
 *       "delete" = "Drupal\event_management\Form\EventDeleteForm",
 *     },
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *   },
 *   admin_permission = "administer event entities",
 *   links = {
 *     "canonical" = "/events/{event}",
 *     "add-form" = "/admin/content/event/add",
 *     "edit-form" = "/admin/content/event/{event}/edit",
 *     "delete-form" = "/admin/content/event/{event}/delete",
 *     "collection" = "/admin/content/event"
 *   },
 * )
 */
class Event extends ContentEntityBase
{

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        // Title field.
        $fields['title'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Title'))
            ->setRequired(TRUE)
            ->setSettings(['max_length' => 255])
            ->setDisplayOptions('form', [
                'type' => 'string_textfield',
                'weight' => -5,
            ]);

        // Description field.
        $fields['description'] = BaseFieldDefinition::create('text_long')
            ->setLabel(t('Description'))
            ->setRequired(TRUE)
            ->setDisplayOptions('form', [
                'type' => 'text_textarea',
                'weight' => 0,
            ])
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'text_long',
                'weight' => 0,
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        // Image field.
        $fields['image'] = BaseFieldDefinition::create('image')
            ->setLabel(t('Image'))
            ->setDescription(t('An image for the event.'))
            ->setSettings([
                'file_extensions' => 'png jpg jpeg',
                'file_directory' => 'images/events',
                'max_filesize' => '5 MB',
            ])
            ->setDisplayOptions('form', [
                'type' => 'image_image',
                'weight' => 2,
            ])
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'image',
                'weight' => 2,
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        // Start time field.
        $fields['start_time'] = BaseFieldDefinition::create('datetime')
            ->setLabel(t('Start Time'))
            ->setRequired(TRUE)
            ->setDisplayOptions('form', [
                'type' => 'datetime_default',
                'weight' => 2,
            ]);

        // End time field.
        $fields['end_time'] = BaseFieldDefinition::create('datetime')
            ->setLabel(t('End Time'))
            ->setRequired(TRUE)
            ->setDisplayOptions('form', [
                'type' => 'datetime_default',
                'weight' => 3,
            ]);

        // Category field.
        $fields['category'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Category'))
            ->setDescription(t('The category of the event.'))
            ->setRequired(TRUE)
            ->setSetting('target_type', 'taxonomy_term')
            ->setSetting('handler', 'default')
            ->setSetting('handler_settings', [
                'target_bundles' => ['event_category'],
            ])
            ->setDisplayOptions('form', [
                'type' => 'entity_reference_autocomplete',
                'weight' => 5,
            ])
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'entity_reference_label',
                'weight' => 5,
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public static function deleteFormSubmit($form, FormStateInterface $form_state)
    {
        $entity = $form_state->getFormObject()->getEntity();
        $entity->delete();

        // Redirect after deletion
        $form_state->setRedirect('event_management.event_list');
    }
}
