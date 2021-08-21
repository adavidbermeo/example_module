<?php
namespace Drupal\example_module\Controller;
 
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

/**
 * 
 * Controller used to submit the form structure 
 * to twig template
 * 
 */
 
class RegisterController extends ControllerBase {
     
  public function addNewUser() {

  // We use the Register users form
  $form = $this->formBuilder()->getForm('Drupal\example_module\Form\RegisterUsers');
         
  // We pass the form to the configured theme 
    return [
      '#theme' => 'register_template',
      '#register_form' => $form, 
      '#attached' =>[
        'library' => [
          'example_module/drupal.custom-libraries'
        ],
      ],    
    ];
  }
   
}
 