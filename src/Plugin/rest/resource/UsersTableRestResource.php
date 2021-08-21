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
 *   id = "users_table_resource",
 *   label = @Translation("Users table resource"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/example-crud/data/insert",
 *     "canonical" = "/example-crud/data"
 *   }
 * )
 */
class UsersTableRestResource extends ResourceBase {

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
  * Responds to entity GET requests.
  * @return \Drupal\rest\ResourceResponse
  */
  public function get(){

    $query = $this->database->query("SELECT * FROM {example_users}");
    $results_query = $query->fetchAll(); 
    $data = [];

    foreach($results_query as $result){
      $register_id = $result->register_id;
      $name = $result->name;
      $identification = $result->identification;
      $date = $result->date;
      $charge = $result->charge;
      $status =  $result->status;

      $data[] = [
        'register_id' => $register_id,
        'name' => $name,
        'identification' => $identification,
        'date' => $date,
        'charge' => $charge,
        'status' => $status
      ];

    }

    if(!empty($results_query)){
      return new ModifiedResourceResponse($data,200);
    }else{
      $empty_mssg = 'No se entontraron usuarios registrados en el sistema';
      return new ModifiedResourceResponse($empty_mssg,200);
    }
  }

  /**
  * Responds to entity POST requests.
  * @return \Drupal\rest\ResourceResponse
  */
  public function post($data){
    
      $register_id = null;
      $name = $data['name'];
      $identification = $data['identification'];
      $date = $data['date'];
      $charge = $data['charge'];

      if($charge == '1'){
        $status =  1;
      }else{
        $status = 0;
      }


      $query = $this->database->insert('example_users')
        ->fields([
        'register_id' => $register_id,
        'name' => $name,
        'identification' => $identification,
        'date' => $date,
        'charge' => $charge,
        'status' => $status
      ])
      ->execute();

      if(!empty($query)){
        $success_query = 'Se ha creado correctamente el registro';
        return new ModifiedResourceResponse($success_query,200);
      }else{
        $empty_mssg = 'No se entontraron usuarios registrados en el sistema';
        return new ModifiedResourceResponse($empty_mssg,200);
      }
  }
}

