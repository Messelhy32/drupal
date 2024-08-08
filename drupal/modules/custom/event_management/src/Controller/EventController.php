<?php
namespace Drupal\event_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends ControllerBase
{

    /**
     * Display a list of events.
     */
    public function listEvents()
    {
        $config = $this->config('event_management.settings');
        $show_past_events = $config->get('show_past_events');
        $events_per_page = $config->get('events_per_page');

        // Debugging: Check what value is retrieved
        \Drupal::messenger()->addMessage($this->t('Events per page: @value', ['@value' => $events_per_page]));

        // Query the database for event IDs.
        $query = \Drupal::database()->select('event', 'e')
            ->fields('e', ['id'])
            ->orderBy('start_time', 'ASC')
            ->range(0, $events_per_page);

        if (!$show_past_events) {
            $query->condition('end_time', time(), '>=');
        }

        $result = $query->execute()->fetchAll();

        $items = [];
        foreach ($result as $record) {
            // Load the full event entity.
            $event = \Drupal::entityTypeManager()->getStorage('event')->load($record->id);

            if ($event) {
                // Create edit and delete links.
                $edit_link = \Drupal\Core\Link::createFromRoute($this->t('Edit'), 'entity.event.edit_form', ['event' => $event->id()])->toString();
                $delete_link = \Drupal\Core\Link::createFromRoute($this->t('Delete'), 'entity.event.delete_form', ['event' => $event->id()])->toString();

                // Render each event.
                $items[] = [
                    '#markup' => $this->t('<h3>@title</h3><p>@description</p><p><strong>Category:</strong> @category</p><p><strong>Start:</strong> @start_time</p><p><strong>End:</strong> @end_time</p><img src="@image_url" alt="Event Image"><p>@edit_link | @delete_link</p>', [
                        '@title' => $event->get('title')->value,
                        '@description' => $event->get('description')->value,
                        '@category' => $event->get('category')->entity ? $event->get('category')->entity->getName() : '',
                        '@start_time' => \Drupal::service('date.formatter')->format(strtotime($event->get('start_time')->value), 'custom', 'Y-m-d H:i'),
                        '@end_time' => \Drupal::service('date.formatter')->format(strtotime($event->get('end_time')->value), 'custom', 'Y-m-d H:i'),
                        '@image_url' => $event->get('image')->entity ? \Drupal::service('file_url_generator')->generateAbsoluteString($event->get('image')->entity->getFileUri()) : '',
                        '@edit_link' => $edit_link,
                        '@delete_link' => $delete_link,
                    ]),
                ];
            }
        }

        return [
            '#theme' => 'item_list',
            '#items' => $items,
        ];
    }






    /**
     * Display a specific event.
     */
    public function viewEvent($event)
    {
        // Load the event entity.
        $event = \Drupal::entityTypeManager()->getStorage('event')->load($event);

        if (!$event) {
            throw new NotFoundHttpException();
        }

        // Load the category term entity and get its name.
        $category_name = '';
        if ($event->get('category')->target_id) {
            $term = \Drupal\taxonomy\Entity\Term::load($event->get('category')->target_id);
            if ($term) {
                $category_name = $term->getName();
            }
        }

        // Load the image file entity and generate the URL if available.
        $image_url = '';
        if ($event->get('image')->entity) {
            $image_field = $event->get('image')->entity;
            if ($image_field) {
                $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_field->getFileUri());
            }
        }

        // Ensure description and format are not null.
        $description = $event->get('description')->value ?? '';
        $format = $event->get('description')->format ?? 'basic_html';

        // Create edit and delete links.
        $edit_link = \Drupal\Core\Link::createFromRoute($this->t('Edit'), 'entity.event.edit_form', ['event' => $event->id()])->toString();
        $delete_link = \Drupal\Core\Link::createFromRoute($this->t('Delete'), 'entity.event.delete_form', ['event' => $event->id()])->toString();

        // Prepare the render array for the event.
        return [
            '#theme' => 'event_display',
            '#title' => $event->get('title')->value,
            '#description' => [
                '#type' => 'processed_text',
                '#text' => $description,
                '#format' => $format,
            ],
            '#category' => $category_name,
            '#start_time' => \Drupal::service('date.formatter')->format(strtotime($event->get('start_time')->value), 'custom', 'Y-m-d H:i'),
            '#end_time' => \Drupal::service('date.formatter')->format(strtotime($event->get('end_time')->value), 'custom', 'Y-m-d H:i'),
            '#image_url' => $image_url,
            '#edit_link' => $edit_link,
            '#delete_link' => $delete_link,
        ];
    }





    public function getTitle($event)
    {
        $event = \Drupal::entityTypeManager()->getStorage('event')->load($event);
        return $event ? $event->get('title')->value : '';
    }
}
