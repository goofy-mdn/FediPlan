<?php
/**
 * Created by fediplan.
 * User: tom79
 * Date: 08/08/19
 * Time: 14:57
 */

namespace App\Form;


use App\SocialEntity\Compose;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;

class ComposeType extends AbstractType {


    private $securityContext;

    public function __construct(Security $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('content_warning');
        $builder->add('content');
        $builder->add('visibility', ChoiceType::class,
            [
                'choices' => [
                    'status.visibility.public' => 'public',
                    'status.visibility.unlisted' => 'unlisted',
                    'status.visibility.private' => 'private',
                    'status.visibility.direct' => 'direct',
                ]
            ]);
        $builder->add('scheduled_at', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class,[
            'widget' => 'single_text',
        ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Compose::class,
            'translation_domain' => 'fediplan'
        ]);
    }

}