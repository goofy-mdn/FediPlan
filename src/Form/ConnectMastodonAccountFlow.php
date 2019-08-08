<?php
/**
 * Created by fediplan.
 * User: tom79
 * Date: 08/08/19
 * Time: 14:58
 */

namespace App\Form;

use Craue\FormFlowBundle\Form\FormFlow;


class ConnectMastodonAccountFlow extends FormFlow {

    protected function loadStepsConfig() {
        return [
            [
                'form_type' => ConnectMastodonAccountType::class,
            ],
            [
                'form_type' => ConnectMastodonAccountType::class,
            ],
            [
                'label' => 'confirmation',
            ],
        ];
    }
}