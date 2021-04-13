<?php
namespace Drupal\example_module\Controller;
 
use Drupal;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
 
class UsersTableController extends ControllerBase {
     
  public function content() {

    
    $user_table = $this->queryRegisters();

    //echo "<pre>";
    //var_dump($user_table);
    
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
   
  public function queryRegisters() {
    
    //Consultamos a la base de datos
    /** @var \Drupal\Core\Database\Connection $connection */
    $database = \Drupal::service('database');

    $query = $database->select('example_users', 'u')
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


        /*$data[] = [
            'register_id' => $register_id,
            'name' => $name,
            'identification' => $identification,
            'date' => $date,
            'charge' => $charge,
            'status' => $status
        ];*/
    }

    return $data;
   
  }
   
}
 