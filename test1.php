<?php
namespace Drupal\form_test\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

//test
use Drupal\node\Entity\Node;



class test1 extends ControllerBase {
    public function ShowPage() {

        $form = \Drupal::formBuilder()->getForm( new Form() );
    
        $markup = [
            '#theme' => 'test1', // theme name that will be matched in *.module
            '#title' => 'This is a title coming from test1::showpage',
            '#form' => $form,
        ];

        return $markup;
    }
}

class Form extends FormBase {

    public function getFormId()
    {
        return 'form4';
    }

    public function buildForm( array $form, FormStateInterface $form_state ) {
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t( 'Input Title' ),
            '#default_value' => \Drupal::state()->get('third.title'),
        ];

        $form['content'] = [
            '#type' => 'textfield',
            '#title' => $this->t( 'Input Content' ),
            '#default_value' => \Drupal::state()->get('third.content'),
        ];

        $form['image'] = [
            '#type' => 'file',
            '#title' => $this->t( 'Input Image' ),
            '#default_value' => \Drupal::state()->get('third.image'),
        ];        

        $form['action']['submit'] = [
            '#type' => 'submit',
            '#value' => 'OKAY. SUBMIT NOW',
            '#prefix' => '<div class="submit-button">',
            '#suffix' => '</div>',
        ];

        return $form;
    }

    public function submitForm( array &$form, FormStateInterface $form_state ) {
        $title = $form_state->getValue( 'title' );
        \Drupal::state()->set( 'third.title', $title );

        $content = $form_state->getValue( 'content' );
        \Drupal::state()->set( 'third.content', $content );

        $image = $form_state->getValue( 'image' );
        \Drupal::state()->set( 'third.image', $image );
            /*test*/
                $p = [];
                $p['type'] = 'page';
                $p['title'] = $title;

                $node = Node::create( $p );
                $node->save();

// how to image
// save image into drupal
            $a = 'C:\Users\Teacher\Downloads';
            $b = $image;
            $c = $a.'\ '.$b;
            $str = preg_replace('/\s+/', '', $c);

    $file = file_save_data(file_get_contents($str));
// make it usable
    \Drupal::service('file.usage')->add($file, 'editor', 'node', $node->id());

// get URL of the image ( which saved in Drupal )
    $src = $file->url();
// Make the URL work. ( @todo Find out better way )
    $src = str_replace('http://default', 'http://localhost', $src);

// Put Image code ( You must do it so, when the node is deleted, the image will be automatically deleted )
    $uuid = $file->uuid();
    $img = "<img src='$src' data-entity-type='file' data-entity-uuid='$uuid'>";
    $node->body->format = "full_html";
    $node->body->value = "<h1>".$content."</h1>Image :".$img;

    $node->save();                
            /*test*/        
    }
}
