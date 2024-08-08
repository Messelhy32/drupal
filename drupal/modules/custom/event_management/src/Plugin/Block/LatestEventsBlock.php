<?php

namespace Drupal\event_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Database;

/**
 * Provides a 'Latest Events' Block.
 *
 * @Block(
 *   id = "latest_events_block",
 *   admin_label = @Translation("Latest Events Block"),
 *   category = @Translation("Custom"),
 * )
 */
class LatestEventsBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $query = Database::getConnection()->select('event', 'e')
            ->fields('e', ['id', 'title', 'start_time'])
            ->orderBy('id', 'DESC')
            ->range(0, 5);
        $results = $query->execute()->fetchAll();

        $items = [];
        foreach ($results as $event) {
            $url = \Drupal\Core\Url::fromRoute('event_management.event_view', ['event' => $event->id])->toString();
            $items[] = [
                '#markup' => $this->t('<a href="@url">@title</a> (Starts: @start_time)', [
                    '@url' => $url,
                    '@title' => $event->title,
                    '@start_time' => \Drupal::service('date.formatter')->format(strtotime($event->start_time), 'custom', 'Y-m-d H:i'),
                ]),
            ];
        }

        return [
            '#theme' => 'item_list',
            '#items' => $items,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags()
    {
        // Return an empty array to avoid caching issues when content changes.
        return [];
    }
}
