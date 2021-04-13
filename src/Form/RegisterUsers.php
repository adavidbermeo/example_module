<?php
namespace Drupal\example_module\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
 
 
class RegisterUsers extends FormBase {
 
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        // Nombre del formulario
        return 'register_users_form';
    }
 
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['container'] = [
            '#prefix' => '<div id="my_form_wrapper" class="messages__wrapper layout-container">',
            '#suffix' => '</div>',
        ];
     
       // Definimos los campos

        $form['container']['name'] = [
            '#type'     => 'textfield',
            '#title'    => $this->t('Nombre completo'),
            '#description' => 'Nombres y apellidos completos. Ej: Angel David Bermeo',
            '#required' => TRUE,
            '#attributes' => [
                'placeholder' => 'Tu nombre va aqui'
            ]
        ];
         
        $form['container']['identification'] = [
            '#type'     => 'textfield',
            '#title'    => $this->t('Idetificacion'),
            '#description' => 'Cedula de ciudadania, Tarjeta de identidad o Pasaporte',
            '#required' => TRUE,
            '#attributes' => [
                'placeholder' => 'Tu numero de identificación'
            ]
        ];


        $form['container']['date'] = [
            '#type'     => 'date',
            '#title'    => $this->t('Fecha de nacimiento'),
            '#description' => 'Eliga su fecha de nacimiento acorde al formato: mes/dia/año',
            '#date_date_format' => 'Y/m/d',
        ];


        $charge_options = [ '--- Elegir ---', 'Administrador', 'Webmaster','Desarrollador' ];



        $form['container']['roles'] = [
            '#type'     => 'select',
            '#title'    => $this->t('Cargos'),
            '#options' =>  $charge_options,
            '#description' => 'Eliga su cargo actual'
            //'#required' => TRUE,
        ];
        
        
        $form['container']['submit'] = [
            '#type'  => 'submit',
            '#value' => $this->t('Registrarme'),
            '#ajax' => [
                'wrapper' => 'my_form_wrapper',
                'callback' => '::ajaxCallback',
            ],
        ];


        return $form;

    }
 
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {


        if (!ctype_alnum(str_replace(' ','', $form_state->getValue('name')))) {
            $form_state->setErrorByName('name', $this->t('Solo se aceptan caracteres alfanumericos'));
        }


        if (!is_numeric($form_state->getValue('identification'))) {
            $form_state->setErrorByName('identification', $this->t('Solo se aceptan numeros para la identificacion'));
        }
       

        if (empty($form_state->getValue('date'))) {
            $form_state->setErrorByName('date', $this->t('Es necesario elegir una fecha de nacimiento'));
        }
    
    
        if (empty($form_state->getValue('roles'))) {
            $form_state->setErrorByName('roles', $this->t('Es necesario elegir un cargo'));
        }
 
    }

    public function ajaxCallback(array &$form, FormStateInterface $form_state){


    //Se verifica si existen errores resultantes de la validacion

    if($form_state->hasAnyErrors()){

        $form_state->setRebuild();

        $errors = $form_state->getErrors();

        return($form);

    }else{

        // No se reconstruye el formulario
        $form_state->setRebuild(FALSE);
        $ajax_response = new AjaxResponse();


        // Get fields values
        $name = $form_state->getValue('name');
        $identification = $form_state->getValue('identification');
        $date = $form_state->getValue('date');
        $charge_id = $form_state->getValue('roles');
        $charge_label = $form['container']['roles']['#options'][$charge_id];


        if($charge_id == 1){
            $status = $charge_id;
        }else{
            $status = 0;
        }
        
        // Query database
        $msql_query = $this->insertValues($name,$identification, $date, $charge_label, $status);

      
        if($msql_query){
            $success_message = '

                <div id="response_mssg_wrapper" style="text-align: center; padding:20px;"">
                    Sus datos han sido insertados correctamente <br><br>
                    <a style="margin-right: 11%;" href="/example-module/form">Añadir nuevo registro </a>
                    <a href="/example-module/users-table"> Ver tabla de usuarios registrados </a>
                </div>
                
            ';

            $ajax_response->addCommand(new HtmlCommand('#my_form_wrapper', $success_message));
        }else{
            $error_message = 'Sus datos no han podido ser insertados debido a un error de mysql';
            $ajax_response->addCommand(new HtmlCommand('#my_form_wrapper', $error_message));
        }

        return $ajax_response;
        
    }

  }
 
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
 
       //Como se realiza el envio mediante ajax, no utilizamos este metodo
    }

    /**
     * 
     * Metodo encargado de insertar los valores obtenidos del formulario en la
     * base de datos
     * 
     */
    public function insertValues($name, $identification, $date, $charge, $status){

          /** @var \Drupal\Core\Database\Connection $connection */
          $connection = \Drupal::service('database');
        
          //$formatted_date = $date->format('Y-m-d H:i:s');
  
          $result = $connection->insert('example_users')
          ->fields([
              'register_id' => null,
              'name' => $name,
              'identification' => $identification,
              'date' => $date,
              'charge' => $charge,
              'status' => $status
          ])
          ->execute();


          if(!empty($result)){

            return true;

          }else{

            return false;

          }

    }
}
