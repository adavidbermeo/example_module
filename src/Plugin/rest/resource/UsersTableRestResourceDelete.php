<?php 

namespace Drupal\example_module\Plugin\rest\resource;

use Drupal\tigo_activation\Orders;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

/**
 * @RestResource(
 *   id = "users_table_resource_delete",
 *   label = @Translation("Users table resource Delete"),
 *   uri_paths = {
 *     "canonical" = "/example-crud/data/delete/{id}"
 *   }
 * )
 */
class UsersTableRestResourceDelete extends ResourceBase {

  /**
   * Connection to the database. 
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $connection, $serializer_formats, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->database = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest')
    );
  }

  /**
  * Responds to entity DELETE requests.
  * @return \Drupal\rest\ResourceResponse
  */
  public function delete($identification){

    $query = $this->database->delete('example_users')
    ->condition('identification', $identification)
    ->execute();

    if($query){
        $success_query = 'Se ha creado correctamente el registro';
        return new ModifiedResourceResponse($success_query,200);
    }else{
        $empty_mssg = 'No se encontro el registro a eliminar';
        return new ModifiedResourceResponse($empty_mssg,500);
    }

  }
}

