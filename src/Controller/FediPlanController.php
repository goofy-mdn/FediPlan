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
use App\SocialEntity\MastodonAccount;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
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
                $host = $client->getHost();
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
                        $Account->setInstance($host);
                        $Account->setToken($token_type ." ".$access_token);
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
    public function schedule(Request $request, Mastodon_api $mastodon_api)
    {

        $compose = new Compose();
        $form = $this->createForm(ComposeType::class, $compose);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $data Compose */
            $data = $form->getData();
            /* @var $user MastodonAccount */
            $user = $this->getUser();
            $mastodon_api->set_url("https://" . $user->getInstance());

            $token = explode(" " ,$user->getToken())[1];
            $type = explode(" ", $user->getToken())[0];
            $mastodon_api->set_token($token, $type);
            $params = [];
            //Update media description and store their id
            foreach ($_POST as $key => $value){
                if( $key != "compose"){

                    if (strpos($key, 'media_id_') !== false){

                        $mediaId = $value;
                        $description = $_POST['media_description_'.$mediaId];
                        //update description if needed
                        if( $description != null && trim($description) != ""){
                            try {
                                $res = $mastodon_api->update_media($mediaId, ['description' => $description]);
                            } catch (\ErrorException $e) {}
                        }
                        $params['media_ids'][] = $mediaId;
                    }
                }
            }
            //Schedule status
            if( $data->getContentWarning() ){
                $params['spoiler_text'] = $data->getContentWarning();
            }
            if( $data->getContent() ){
                $params['status'] = $data->getContent();
            }
            if( $data->getVisibility() ){
                $params['visibility'] = $data->getVisibility();
            }
            $params['sensitive'] = ($data->getSensitive() == null || !$data->getSensitive())?false:true;

            try {
                $date = new DateTime( $data->getScheduledAt()->format("Y-m-d H:i:s"), new DateTimeZone($data->getTimeZone()) );
                $date->setTimezone(  new DateTimeZone("UTC"));
                $params['scheduled_at'] = $date->format('c');
            } catch (\Exception $e) {}
            try {
                $response = $mastodon_api->post_statuses($params);
            } catch (\ErrorException $e) {}
            $session = $request->getSession();
            if( isset($response['error']) ){
                $session->getFlashBag()->add(
                    'Error',
                    $response['error_message']
                );
                $form->get('content')->addError(new FormError( $response['error_message']));
            }else{
                unset($compose);
                unset($form);
                $compose = new Compose();
                $session->getFlashBag()->add(
                    'Success',
                    'The message has been scheduled'
                );
                $form = $this->createForm(ComposeType::class, $compose);
            }
        }
        $user = $this->getUser();
        /** @var $user MastodonAccount */

        return $this->render("fediplan/schedule.html.twig",[
            'form' => $form->createView(),
            'instance' => $user->getInstance(),
            'token' => $user->getToken(),
            ]);

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