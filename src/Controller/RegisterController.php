<?php
namespace Drupal\example_module\Controller;
 
use Drupal;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

/**
 * 
 * Controlador usado para enviar la estructura del formulario 
 * a la plantilla twig
 * 
 */
 
class RegisterController extends ControllerBase {
     
  public function content() {
    return array(
        '#type' => 'markup',
        '#markup' => $this->t('Custom Response text'),
    );
  }
   
  public function addNewUser() {

  // Utilizamos el formulario
  $form = $this->formBuilder()->getForm('Drupal\example_module\Form\RegisterUsers');
         
  // Le pasamos el formulario y demás a la vista (tema configurado en el module)
    return [
      '#theme' => 'register_template',
      '#register_form' => $form,     
    ];
  }
   
}
 