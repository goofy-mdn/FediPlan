<?php
/**
 * Created by fediplan.
 * User: tom79
 * Date: 08/08/19
 * Time: 10:16
 */

namespace App\Controller;

use App\Form\ComposeType;
use App\Form\ConnectMastodonAccountFlow;
use App\Services\Mastodon_api;
use App\SocialEntity\Client;
use App\SocialEntity\Compose;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FediPlanController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request, AuthorizationCheckerInterface $authorizationChecker, ConnectMastodonAccountFlow $flow, Mastodon_api $mastodon_api, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher)
    {

        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('schedule'));
        }
        $client = new Client();
        $flow->bind($client);
        $form = $flow->createForm();
        $urlToMastodon = null;
        $client_id = null;
        $client_secret = null;
        if ($flow->isValid($form)) {
            if( $flow->getCurrentStep() == 1){
                $host = $client->getHost();
                $result = $mastodon_api->getInstanceNodeInfo($host);
                //We currently only support Mastodon accounts
                if( $result != "MASTODON"  && $result != "PLEROMA"){
                    $form->get('host')->addError(new FormError($translator->trans('error.instance.mastodon_only',[],'fediplan','en')));
                }else{
                    $mastodon_api->set_url("https://" . $host);
                    $mastodon_api->set_scopes([]);
                    $createApp = $mastodon_api->create_app("FediPlan", [], '', "https://fediplan.fedilab.app");
                    if( isset($createApp['error']) ){
                        $form->get('host')->addError(new FormError($translator->trans('error.instance.mastodon_client_id',[],'fediplan','en')));
                    }else{
                        // form for the next step
                        $mastodon_api->set_client($createApp['response']['client_id'], $createApp['response']['client_secret']);
                        $urlToMastodon = $mastodon_api->getAuthorizationUrl();
                        if( isset($createApp['error']) ){
                            $form->get('host')->addError(new FormError($translator->trans('error.instance.mastodon_oauth_url',[],'fediplan','en')));
                        }else{
                            $flow->saveCurrentStepData($form);
                            $client_id = $createApp['response']['client_id'];
                            $client_secret = $createApp['response']['client_secret'];
                            $flow->nextStep();
                            $form = $flow->createForm();
                        }
                    }

                }
            } else if ($flow->getCurrentStep() == 2) {

                $code = $client->getCode();
                $mastodon_api->set_url("https://" . $client->getHost());
                $mastodon_api->set_scopes([]);

                $mastodon_api->set_client($client->getClientId(), $client->getClientSecret());
                $reply = $mastodon_api->loginAuthorization($code);
                if( isset($reply['error']) ){
                    $form->get('code')->addError(new FormError($translator->trans('error.instance.mastodon_token',[],'fediplan','en')));
                }else{
                    $access_token = $reply['response']['access_token'];
                    $token_type = $reply['response']['token_type'];
                    $mastodon_api->set_url("https://" . $client->getHost());
                    $mastodon_api->set_token($access_token, $token_type);

                    $accountReply = $mastodon_api->accounts_verify_credentials();

                    if( isset($accountReply['error']) ){
                        $form->get('code')->addError(new FormError($translator->trans('error.instance.mastodon_account',[],'fediplan','en')));
                    }else{

                        $Account =  $mastodon_api->getSingleAccount($accountReply['response']);
                        $token = new UsernamePasswordToken($Account, null, 'main', array('ROLE_USER'));
                        $this->get('security.token_storage')->setToken($token);
                        $event = new InteractiveLoginEvent($request, $token);
                        $eventDispatcher->dispatch("security.interactive_login", $event);
                        return $this->redirectToRoute('schedule');
                    }
                }
            }

        }

        return $this->render('fediplan/index.html.twig', [
            'form' => $form->createView(),
            'flow' => $flow,
            'urlToMastodon' => $urlToMastodon,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ]);


    }


    /**
     * @Route("/schedule", name="schedule")
     */
    public function schedule()
    {

        $compose = new Compose();
        $form = $this->createForm(ComposeType::class, $compose);
        if ($form->isSubmitted() && $form->isValid($form)) {


        }
        return $this->render("fediplan/schedule.html.twig",['form' => $form->createView()]);

    }


    /**
     * @Route("/scheduled", name="scheduled")
     */
    public function scheduled()
    {

        if ($this->get("security.authorization_checker")->isGranted('IS_AUTHENTICATED_FULLY')){
            $number = random_int(0, 100);
        }else{
            $number = 0;
        }
        return $this->render("fediplan/index.html.twig");
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

        if ($this->get("security.authorization_checker")->isGranted('IS_AUTHENTICATED_FULLY')){
            $number = random_int(0, 100);
        }else{
            $number = 0;
        }
        return $this->render("fediplan/index.html.twig");
    }

}