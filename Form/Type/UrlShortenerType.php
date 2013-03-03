<?php
namespace Vellozzi\UrlShortenerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
/**
 * Description of UrlShortenerType
 *
 * @author seb
 */
class UrlShortenerType extends AbstractType 
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $urlOptions =  array(
      'max_length' => 255,
      'required' => false,
      'label' => 'Site web',
      'trim' => true, 
      'read_only' => false,
      'error_bubbling' => false
    );      
    $builder->add('url', 'url', $urlOptions);
    $builder->add('shortTag', 'text', array('max_length' =>  4,));
    $builder->add('maxAllowedUse', 'integer');
    $builder->add('expireAt','datetime', array('widget' => 'single_text', 'format'=> 'yyyy-MM-dd mm:kk', 'required'=> true));
  }

  public function getName()
  {
    return 'urlshortener';
  }
}
?>