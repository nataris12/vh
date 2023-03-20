<?php

namespace Drupal\vh_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * VH Activity Log report
 */
class EntityLogController extends ControllerBase {

  /**
   * The Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   *
   * @param \Drupal\Core\Database\Connection $database
   *   A database connection object.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Log page.
   */
  public function report() {

    $header = [
      ['data' => t('ID')],
      ['data' => t('Timestamp')],
      ['data' => t('Content Type')],
      ['data' => t('Entity ID')],
      ['data' => t('Event')],
      ['data' => t('User ID')],
    ];
    
    $query = $this->database->select('vh_activity', 'vh')->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $query->fields('vh', ['id', 'timestamp', 'bundle', 'entity_id','event', 'uid']);
    $query->orderBy('vh.id', 'ASC');
    $result = $query->execute();

    $rows = [];
    foreach ($result as $row) {
      
      $rows[] = [
        'data' => [
          "id" => $row->id,
          "timestamp" => date('F d, Y g:ia', $row->timestamp),
          "bundle" => $row->bundle,
          "entityid" => $row->entity_id,
          "event" => $row->event,
          "userid" => $row->uid,
        ],

      ];
    }
    
    
    $build['tablesort_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    // Attach the pager theme.
    $build[] = ['#type' => 'pager'];

    return $build;

  }

}
