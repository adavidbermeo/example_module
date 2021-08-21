<?php
namespace Drupal\example_module\Controller;
 
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
 
class UsersTableController extends ControllerBase {

  /**
   * Database connection
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct(Connection $connection){
    $this->database = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
  * Function to get registered users 
  */
  public function queryRegisters(){
    
    $query = $this->database->select('example_users', 'u')
      ->fields('u');
    $results = $query->execute()->fetchAll();

    $data = [];

    foreach($results as $result){
      $data['register_id'][] = $result->register_id;
      $data['name'][] = $result->name;
      $data['identification'][] = $result->identification;
      $data['date'][] = $result->date;
      $data['charge'][] = $result->charge;
      $data['status'][] =  $result->status;
    }

    return $data;
   
  }
  
  /**
   * {@inheritdoc}
   */
  public function content(){

    $user_table = $this->queryRegisters();
    
    return [
      '#theme' => 'users_table_template',
      '#users_data' => $user_table,
      '#attached' =>[
        'library' => [
          'example_module/drupal.custom-libraries',
          'example_module/bootstrap-cdn',
        ],
      ],
    ];
  } 
}
 