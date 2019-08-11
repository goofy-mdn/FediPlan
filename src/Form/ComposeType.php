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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
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
        /**@var $user \App\SocialEntity\MastodonAccount**/
        $user = $options['user'];

        if( $user && $user->getDefaultSensitivity()) {
            $checkbox = [
                'required' => false,
                'attr' => ['checked' => 'checked'],
            ];
        }else {
            $checkbox = ['required' => false];
        }

        $builder->add('content_warning', TextType::class, ['required' => false]);
        $builder->add('content', TextareaType::class, ['required' => false]);
        $builder->add('visibility', ChoiceType::class,
            [
                'choices' => [
                    'status.visibility.public' => 'public',
                    'status.visibility.unlisted' => 'unlisted',
                    'status.visibility.private' => 'private',
                    'status.visibility.direct' => 'direct',
                ],
                'data' => $user?$user->getDefaultVisibility():'public',
            ]);
        $builder->add('timeZone', TimezoneType::class);
        $builder->add('sensitive', CheckboxType::class, $checkbox);
        $builder->add('scheduled_at', \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class,[
            'widget' => 'single_text',
            "data" => new \DateTime()
        ]);
        $builder->add('Send', SubmitType::class, ['attr' => ['class' => "btn btn-primary "]]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Compose::class,
            'translation_domain' => 'fediplan',
            'user' => null
        ]);
    }

}