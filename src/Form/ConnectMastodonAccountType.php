<?php
/**
 * Created by fediplan.
 * User: tom79
 * Date: 08/08/19
 * Time: 14:57
 */

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectMastodonAccountType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $builder->add('host', TextType::class, [
                    'label' =>  'Your instance',
                ]);
                break;
            case 2:
                // This form type is not defined in the example.
                $builder->add('code', TextType::class, [
                    'label' => 'Your authorization code',
                ]);
                $builder->add('client_id', HiddenType::class);
                $builder->add('client_secret', HiddenType::class);
                break;
        }
    }

    public function getBlockPrefix() {
        return 'addMastodonAccount';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['registration'],
        ]);
    }
}