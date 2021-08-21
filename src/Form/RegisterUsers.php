<?php
namespace Drupal\example_module\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
 
 
class RegisterUsers extends FormBase {

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
     * {@inheritdoc}
     */
    public function getFormId() {
        // Form name
        return 'register_users_form';
    }
 
    /**
     * All form fields are defined 
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['container'] = [
            '#prefix' => '<div id="my_form_wrapper" class="messages__wrapper layout-container">',
            '#suffix' => '</div>',
        ];
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
            '#prefix' => '<div class="register-button">',
            '#suffix' => '</div>'
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

    /**
     * 
     * Method to insert the values obtained from the form into the database 
     * 
     */
    public function insertValues($name, $identification, $date, $charge, $status){

        $connection = $this->database;
        
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

    /**
     * We process our form through ajax 
     */
    public function ajaxCallback(array &$form, FormStateInterface $form_state){

        // It checks if there are errors resulting from the validation 
        if($form_state->hasAnyErrors()){
            $form_state->setRebuild();
            $errors = $form_state->getErrors();
            return($form);
        }else{
            // The form is not rebuilt 
            $form_state->setRebuild(FALSE);

            // We get the values of the fields 
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
            // ajaxResponse object
            $ajax_response = new AjaxResponse();
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
 
       //As the sending is done through ajax we do not use this method 
    }
}
