<?php
/**
 * Created by fediplan.
 * User: tom79
 * Date: 08/08/19
 * Time: 15:02
 */

namespace App\Services;



use App\SocialEntity\Application;
use App\SocialEntity\Attachment;
use App\SocialEntity\CustomField;
use App\SocialEntity\Emoji;
use App\SocialEntity\MastodonAccount;
use App\SocialEntity\Mention;
use App\SocialEntity\Notification;
use App\SocialEntity\Status;
use App\SocialEntity\Tag;
use App\Services\Curl as Curl;

/**
 * Class Mastodon_api
 *
 * PHP version 7.1
 *
 * Mastodon     https://mastodon.social/
 * API LIST     https://github.com/tootsuite/documentation/blob/master/Using-the-API/API.md
 *
 * @author      KwangSeon Yun   <middleyks@hanmail.net>
 * @copyright   KwangSeon Yun
 * @license     https://raw.githubusercontent.com/yks118/Mastodon-api-php/master/LICENSE     MIT License
 * @link        https://github.com/yks118/Mastodon-api-php
 */

class Mastodon_api {
    private $mastodon_url = '';
    private $client_id = '';
    private $client_secret = '';

    private $token = array();
    private $scopes = array();

    public function __construct () {}

    public function __destruct () {}

    /**
     * _post
     *
     * HTTP API post
     *
     * @param   string $url
     * @param   array $parameters
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    private function _post ($url, $parameters = array()) {

        $params["method"] = "POST";
        // set access_token
        if (isset($this->token['access_token'])) {
            $params['headers'] = array(
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token']
            );
        }
        $params['body'] = $parameters;
        $url = $this->mastodon_url.$url;
        $response = $this->get_content_remote($url,$params);
        return $response;
    }


    /**
     * _put
     *
     * HTTP API put
     *
     * @param   string $url
     * @param   array $parameters
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    private function _put ($url, $parameters = array()) {

        $params["method"] = "PUT";
        // set access_token
        if (isset($this->token['access_token'])) {
            $params['headers'] = array(
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token']
            );
        }
        $params['body'] = $parameters;
        $url = $this->mastodon_url.$url;
        $response = $this->get_content_remote($url,$params);
        return $response;
    }

    /**
     * _get
     *
     * @param   string $url
     * @param   array $parameters
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    private function _get ($url, $parameters = array()) {

        $params["method"] = "GET";
        // set authorization bearer
        if (isset($this->token['access_token'])) {
            $params['headers'] = array(
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token']
            );
        }
        $params['body'] = $parameters;
        $url = $this->mastodon_url.$url;

        $response = $this->get_content_remote($url,$params);
        return $response;
    }

    /**
     * _patch
     *
     * @param   string $url
     * @param   array $parameters
     *
     * @return  array       $parameters
     * @throws \ErrorException
     */
    private function _patch ($url,$parameters = array()) {

        $params["method"] = "PATCH";

        // set authorization bearer
        if (isset($this->token['access_token'])) {
            $params['headers'] = array(
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token']
            );
        }
        $params['body'] = $parameters;

        $url = $this->mastodon_url.$url;
        $response = $this->get_content_remote($url,$params);
        return $response;
    }

    /**
     * _delete
     *
     * @param   string $url
     *
     * @return  array       $response
     *
     * @throws \ErrorException
     */
    private function _delete ($url) {
        $parameters = array();
        $parameters["method"] = "DELETE";

        // set authorization bearer
        if (isset($this->token['access_token'])) {
            $parameters['headers'] = array(
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token']
            );
        }

        $url = $this->mastodon_url.$url;
        $response = $this->get_content_remote($url,$parameters);
        return $response;
    }

    /**
     * get_content_remote_get
     *
     * @param   string $url
     * @param   array $parameters
     *
     * @return  array       $data
     * @throws \ErrorException
     */
    public function get_content_remote ($url,$parameters = array()) {
        $data = array();

        // set USERAGENT
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $parameters['headers']['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
        } else {
            // default IE11
            $parameters['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko';
        }
        $curl = new Curl();
        $response = null;

        foreach ($parameters['headers'] as $key => $value){
            $curl->setHeader($key, $value);
        }

        //Special treatment for sending media when editing profile
        if(isset($parameters['body']['media'])) {
            $fields = [$parameters['body']['media']['name'] => new \CURLFile($parameters['body']['media']['path'], $parameters['body']['media']['mimetype'], $parameters['body']['media']['filename'])];
            $curl->setOpt(CURLOPT_POSTFIELDS, $fields);
        }

        // Since Curl does not let us upload media_ids as we wish, we had to pass it through the url, directly.
        if(isset($parameters['body']['media_ids'])) {
            $url .= strpos($url, '?')  !== false ? '' : '?';

            foreach ($parameters['body']['media_ids'] as $key => $value){
                $url .= 'media_ids[]=' . (int) $value . '&';
            }
            $url = substr($url, 0, -1);

            unset($parameters['body']['media_ids']);
        }

        //Special treatment for filtering notifications
        if( isset($parameters['body']['exclude_types[]']) && $parameters['method'] == "GET" ){
            $url .="?";
            foreach ($parameters['body']['exclude_types[]'] as $key => $value){
                $url .= "exclude_types[]=".$value."&";
            }
            if( isset($parameters['body']['max_id']))
                $url .= "&max_id=".$parameters['body']['max_id'];
            $parameters['body'] = [];
        }

        if( isset($parameters["method"]) && $parameters['method'] == "POST" )
            $response = $curl->post($url, $parameters['body'] );
        else if( isset($parameters["method"]) && $parameters['method'] == "GET" )
            $response = $curl->get($url, $parameters['body']  );
        else if( isset($parameters["method"]) && $parameters['method'] == "PUT" )
            $response = $curl->put($url, $parameters['body']  );
        else if( isset($parameters["method"]) && $parameters['method'] == "PATCH" )
            $response = $curl->patch($url, $parameters['body']  );
        else if( isset($parameters["method"]) && $parameters['method'] == "DELETE" )
            $response = $curl->delete($url, $parameters['body']  );
        $min_id = null;
        $max_id = null;
        if( $response->response_headers) {
            foreach ($response->response_headers as $value) {

                if (strpos($value, 'Link: ') !== false) {
                    preg_match(
                        "/min_id=([0-9a-zA-Z]{1,})/",
                        $value,
                        $matches
                    );
                    if ($matches) {
                        $min_id = $matches[1];
                    }
                }
                if (strpos($value, 'Link: ') !== false) {
                    preg_match(
                        "/max_id=([0-9a-zA-Z]{1,})/",
                        $value,
                        $matches
                    );
                    if ($matches) {
                        $max_id = $matches[1];
                    }
                }
            }
        }
        $data['min_id'] = $min_id;
        $data['max_id'] = $max_id;
        if( $response->error){
            $data['error'] = $response->error;
            $data['error_code'] = $response->error_code;
            $data['error_message'] =$response->error_message;
            if( $response->response)
                $data['error_message'] = \json_decode($response->response, true)['error'];
        }else{
            $data['response_headers'] = $response->response_headers;
            $data['response'] = \json_decode($response->response, true);
        }
        return $data;
    }

    /**
     * set_url
     *
     * @param   string      $path
     */
    public function set_url ($path) {
        $this->mastodon_url = $path;
    }

    /**
     * set_client
     *
     * @param   string      $id
     * @param   string      $secret
     */
    public function set_client ($id,$secret) {
        $this->client_id = $id;
        $this->client_secret = $secret;
    }

    /**
     * set_token
     *
     * @param   string      $token
     * @param   string      $type
     */
    public function set_token ($token,$type) {
        $this->token['access_token'] = $token;
        $this->token['token_type'] = $type;
    }

    /**
     * set_scopes
     *
     * @param   array       $scopes     read / write / follow
     */
    public function set_scopes ($scopes) {
        $this->scopes = $scopes;
    }

    /**
     * create_app
     *
     * @param   string $client_name
     * @param   array $scopes read / write / follow
     * @param   string $redirect_uris
     * @param   string $website
     *
     * @return  array       $response
     *          int         $response['id']
     *          string      $response['redirect_uri']
     *          string      $response['client_id']
     *          string      $response['client_secret']
     * @throws \ErrorException
     */
    public function create_app ($client_name,$scopes = array(),$redirect_uris = '',$website = '') {
        $parameters = array();

        if (count($scopes) == 0) {
            if (count($this->scopes) == 0) {
                $scopes = array('read','write','follow');
            } else {
                $scopes = $this->scopes;
            }
        }

        $parameters['client_name'] = $client_name;
        $parameters['scopes'] = implode(' ',$scopes);
        if (empty($redirect_uris)) {
            $parameters['redirect_uris'] = 'urn:ietf:wg:oauth:2.0:oob';
        } else {
            $parameters['redirect_uris'] = $redirect_uris;
        }

        if ($website) {
            $parameters['website'] = $website;
        }

        $response = $this->_post('/api/v1/apps',$parameters);
        if (isset($response['html']['client_id'])) {
            $this->client_id = $response['html']['client_id'];
            $this->client_secret = $response['html']['client_secret'];
        }

        return $response;
    }

    /**
     * login
     *
     * @param   string $id E-mail Address
     * @param   string $password Password
     *
     * @return  array       $response
     *          string      $response['access_token']
     *          string      $response['token_type']         bearer
     *          string      $response['scope']              read
     *          int         $response['created_at']         time
     * @throws \ErrorException
     */
    public function login ($id,$password) {
        $parameters = array();
        $parameters['client_id'] = $this->client_id;
        $parameters['client_secret'] = $this->client_secret;
        $parameters['grant_type'] = 'password';
        $parameters['username'] = $id;
        $parameters['password'] = $password;

        if (count($this->scopes) == 0) {
            $parameters['scope'] = implode(' ',array('read','write','follow'));
        } else {
            $parameters['scope'] = implode(' ',$this->scopes);
        }
        $response = $this->_post('/oauth/token',$parameters);

        if (isset($response['html']['access_token'])) {
            $this->token['access_token'] = $response['html']['access_token'];
            $this->token['token_type'] = $response['html']['token_type'];
        }

        return $response;
    }

    /**
     * login
     *
     * @param   string $code Authorization code
     * @param   string $redirect_uri
     *
     * @return  array       $response
     *          string      $response['access_token']
     *          string      $response['token_type']         bearer
     *          string      $response['scope']              read
     *          int         $response['created_at']         time
     * @throws \ErrorException
     */
    public function loginAuthorization ($code, $redirect_uri = '') {
        $parameters = array();
        $parameters['client_id'] = $this->client_id;
        $parameters['client_secret'] = $this->client_secret;
        if (empty($redirect_uri)) {
            $parameters['redirect_uri'] = 'urn:ietf:wg:oauth:2.0:oob';
        } else {
            $parameters['redirect_uri'] = $redirect_uri;
        }
        $parameters['grant_type'] = 'authorization_code';
        $parameters['code'] = $code;
        $response = $this->_post('/oauth/token',$parameters);
        if (isset($response['html']['access_token'])) {
            $this->token['access_token'] = $response['html']['access_token'];
            $this->token['token_type'] = $response['html']['token_type'];
        }
        return $response;
    }

    /**
     * getAuthorizationUrl
     *
     * @param   string      $redirect_uri
     *
     * @return  string       $response Authorization code
     */
    public function getAuthorizationUrl($redirect_uri = '')
    {
        if (empty($redirect_uri))
            $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
        if (count($this->scopes) == 0) {
            $scopes = array('read','write','follow');
        } else {
            $scopes = $this->scopes;
        }
        $scope_uri = "";
        foreach($scopes as $scope)
            $scope_uri .= $scope. " ";
        return $this->mastodon_url.'/oauth/authorize?'.
            "client_id=".$this->client_id."&redirect_uri=" . $redirect_uri.
            "&response_type=code&scope=".trim($scope_uri);
    }



    /**
     * accounts
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     *          int         $response['id']
     *          string      $response['username']
     *          string      $response['acct']
     *          string      $response['display_name']           The name to display in the user's profile
     *          bool        $response['locked']
     *          string      $response['created_at']
     *          int         $response['followers_count']
     *          int         $response['following_count']
     *          int         $response['statuses_count']
     *          string      $response['note']                   A new biography for the user
     *          string      $response['url']
     *          string      $response['avatar']                 A base64 encoded image to display as the user's avatar
     *          string      $response['avatar_static']
     *          string      $response['header']                 A base64 encoded image to display as the user's header image
     *          string      $response['header_static']
     * @throws \ErrorException
     */
    public function accounts ($id) {
        $response = $this->_get('/api/v1/accounts/'.$id);
        return $response;
    }

    /**
     * accounts_verify_credentials
     *
     * Getting the current user
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_verify_credentials () {
        $response = $this->_get('/api/v1/accounts/verify_credentials');
        return $response;
    }

    /**
     * accounts_update_credentials
     *
     * Updating the current user
     *
     * @param   array $parameters
     *          string      $parameters['display_name']     The name to display in the user's profile
     *          string      $parameters['note']             A new biography for the user
     *          string      $parameters['avatar']           A base64 encoded image to display as the user's avatar
     *          string      $parameters['header']           A base64 encoded image to display as the user's header image
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function accounts_update_credentials ($parameters) {
        $response = $this->_patch('/api/v1/accounts/update_credentials',$parameters);
        return $response;
    }

    /**
     * accounts_followers
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_followers ($id) {
        $response = $this->_get('/api/v1/accounts/'.$id.'/followers');
        return $response;
    }

    /**
     * accounts_following
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_following ($id) {
        $response = $this->_get('/api/v1/accounts/'.$id.'/following');
        return $response;
    }

    /**
     * accounts_statuses
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_statuses ($id) {
        $response = $this->_get('/api/v1/accounts/'.$id.'/statuses');
        return $response;
    }

    /**
     * accounts_own_statuses, only own statuses
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_own_statuses ($id) {
        $response = $this->_get('/api/v1/accounts/'.$id.'/statuses?exclude_replies=1');
        $result = [];
        foreach ($response['html'] as $r) {
            if(is_null($r['reblog'])) {
                $result[] = $r;
            }
        }

        $response['html'] = $result;

        return $response;
    }

    /**
     * accounts_follow
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_follow ($id) {
        $response = $this->_post('/api/v1/accounts/'.$id.'/follow');
        return $response;
    }

    /**
     * accounts_unfollow
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_unfollow ($id) {
        $response = $this->_post('/api/v1/accounts/'.$id.'/unfollow');
        return $response;
    }

    /**
     * accounts_block
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_block ($id) {
        $response = $this->_post('/api/v1/accounts/'.$id.'/block');
        return $response;
    }

    /**
     * accounts_unblock
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_unblock ($id) {
        $response = $this->_post('/api/v1/accounts/'.$id.'/unblock');
        return $response;
    }

    /**
     * accounts_mute
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_mute ($id) {
        $response = $this->_post('/api/v1/accounts/'.$id.'/mute');
        return $response;
    }

    /**
     * accounts_unmute
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_unmute ($id) {
        $response = $this->_post('/api/v1/accounts/'.$id.'/unmute');
        return $response;
    }

    /**
     * accounts_relationships
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   array $parameters
     *          int         $parameters['id']
     *
     * @return  array       $response
     *          int         $response['id']
     *          bool        $response['following']
     *          bool        $response['followed_by']
     *          bool        $response['blocking']
     *          bool        $response['muting']
     *          bool        $response['requested']
     * @throws \ErrorException
     */
    public function accounts_relationships ($parameters) {
        $response = $this->_get('/api/v1/accounts/relationships',$parameters);
        return $response;
    }

    /**
     * accounts_search
     *
     * @param   array $parameters
     *          string      $parameters['q']
     *          int         $parameters['limit']        default : 40
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function accounts_search ($parameters) {
        $response = $this->_get('/api/v1/accounts/search',$parameters);
        return $response;
    }

    /**
     * blocks
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function blocks () {
        $response = $this->_get('/api/v1/blocks');
        return $response;
    }

    /**
     * favourites
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function favourites () {
        $response = $this->_get('/api/v1/favourites');
        return $response;
    }

    /**
     * follow_requests
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function follow_requests () {
        $response = $this->_get('/api/v1/follow_requests');
        return $response;
    }

    /**
     * follow_requests_authorize
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function follow_requests_authorize ($id) {
        $response = $this->_post('/api/v1/follow_requests/authorize',array('id'=>$id));
        return $response;
    }

    /**
     * follow_requests_reject
     *
     * @see     https://your-domain/web/accounts/:id
     *
     * @param   int $id
     * @return  array       $response
     * @throws \ErrorException
     */
    public function follow_requests_reject ($id) {
        $response = $this->_post('/api/v1/follow_requests/reject',array('id'=>$id));
        return $response;
    }

    /**
     * follows
     *
     * Following a remote user
     *
     * @param   string $uri username@domain of the person you want to follow
     * @return  array       $response
     * @throws \ErrorException
     */
    public function follows ($uri) {
        $response = $this->_post('/api/v1/follows',array('uri'=>$uri));
        return $response;
    }

    /**
     * instance
     *
     * Getting instance information
     *
     * @return  array       $response
     *          string      $response['uri']
     *          string      $response['title']
     *          string      $response['description']
     *          string      $response['email']
     * @throws \ErrorException
     */
    public function instance () {
        $response = $this->_get('/api/v1/instance');
        return $response;
    }


    /**
     * mutes
     *
     * Fetching a user's mutes
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function mutes () {
        $response = $this->_get('/api/v1/mutes');
        return $response;
    }

    /**
     * notifications
     *
     *
     * @param $parameters
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function notifications ($parameters) {
        $url = '/api/v1/notifications';

        $response = $this->_get($url, $parameters);
        return $response;
    }

    /**
     * notifications_clear
     *
     * Clearing notifications
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function notifications_clear () {
        $response = $this->_post('/api/v1/notifications/clear');
        return $response;
    }

    /**
     * get_reports
     *
     * Fetching a user's reports
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function get_reports () {
        $response = $this->_get('/api/v1/reports');
        return $response;
    }

    /**
     * post_reports
     *
     * Reporting a user
     *
     * @param   array $parameters
     *          int     $parameters['account_id']       The ID of the account to report
     *          int     $parameters['status_ids']       The IDs of statuses to report (can be an array)
     *          string  $parameters['comment']          A comment to associate with the report.
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function post_reports ($parameters) {
        $response = $this->_post('/api/v1/reports',$parameters);
        return $response;
    }

    /**
     * search
     *
     * Searching for content
     *
     * @param   array $parameters
     *          string  $parameters['q']            The search query
     *          string  $parameters['resolve']      Whether to resolve non-local accounts
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function search ($parameters) {
        $response = $this->_get('/api/v1/search',$parameters);
        return $response;
    }

    /**
     * statuses
     *
     * Fetching a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses ($id) {
        $response = $this->_get('/api/v1/statuses/'.$id);
        return $response;
    }

    /**
     * statuses_context
     *
     * Getting status context
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_context ($id) {
        $response = $this->_get('/api/v1/statuses/'.$id.'/context');
        return $response;
    }

    /**
     * statuses_card
     *
     * Getting a card associated with a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_card ($id) {
        $response = $this->_get('/api/v1/statuses/'.$id.'/card');
        return $response;
    }

    /**
     * statuses_reblogged_by
     *
     * Getting who reblogged a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_reblogged_by ($id) {
        $response = $this->_get('/api/v1/statuses/'.$id.'/reblogged_by');
        return $response;
    }

    /**
     * statuses_favourited_by
     *
     * Getting who favourited a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_favourited_by ($id) {
        $response = $this->_get('/api/v1/statuses/'.$id.'/favourited_by');
        return $response;
    }


    /**
     * post_media
     *
     * @param   array $parameters
     *          file        $parameters['file']                 Media file encoded using multipart/form-data
     *          string      $parameters['description']          (optional): A plain-text description of the media for accessibility (max 420 chars)
     *          array       $parameters['focus']                (optional):Two floating points, comma-delimited. See focal points
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function post_media ($parameters) {
        $response = $this->_post('/api/v1/media',$parameters);
        return $response;
    }

    /**
     * post_media
     *
     * @param   string $id
     *          array $parameters
     *          string      $parameters['description']          (optional): A plain-text description of the media for accessibility (max 420 chars)
     *          array       $parameters['focus']                (optional):Two floating points, comma-delimited. See focal points
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function update_media ($id, $parameters) {
        $response = $this->_put('/api/v1/media/'.$id, $parameters);
        return $response;
    }

    /**
     * post_statuses
     *
     * @param   array $parameters
     *          string      $parameters['status']               The text of the status
     *          int         $parameters['in_reply_to_id']       (optional): local ID of the status you want to reply to
     *          int         $parameters['media_ids']            (optional): array of media IDs to attach to the status (maximum 4)
     *          string      $parameters['sensitive']            (optional): set this to mark the media of the status as NSFW
     *          string      $parameters['spoiler_text']         (optional): text to be shown as a warning before the actual content
     *          string      $parameters['visibility']           (optional): either "direct", "private", "unlisted" or "public"
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function post_statuses ($parameters) {
        $response = $this->_post('/api/v1/statuses',$parameters);
        return $response;
    }

    /**
     * delete_statuses
     *
     * Deleting a status
     *
     * @param   int $id
     *
     * @return  array   $response       empty
     * @throws \ErrorException
     */
    public function delete_statuses ($id) {
        $response = $this->_delete('/api/v1/statuses/'.$id);
        return $response;
    }

    /**
     * statuses_reblog
     *
     * Reblogging a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_reblog ($id) {
        $response = $this->_post('/api/v1/statuses/'.$id.'/reblog');
        return $response;
    }

    /**
     * statuses_unreblog
     *
     * Unreblogging a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_unreblog ($id) {
        $response = $this->_post('/api/v1/statuses/'.$id.'/unreblog');
        return $response;
    }

    /**
     * statuses_favourite
     *
     * Favouriting a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_favourite ($id) {
        $response = $this->_post('/api/v1/statuses/'.$id.'/favourite');
        return $response;
    }

    /**
     * statuses_unfavourite
     *
     * Unfavouriting a status
     *
     * @param   int $id
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function statuses_unfavourite ($id) {
        $response = $this->_post('/api/v1/statuses/'.$id.'/unfavourite');
        return $response;
    }

    /**
     * timelines_home
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function timelines_home () {
        $response = $this->_get('/api/v1/timelines/home');
        return $response;
    }

    /**
     * timelines_public
     *
     * @param   array $parameters
     *          bool    $parameters['local']    Only return statuses originating from this instance
     *
     * @return  array   $response
     * @throws \ErrorException
     */
    public function timelines_public ($parameters = array()) {
        $response = $this->_get('/api/v1/timelines/public',$parameters);
        return $response;
    }

    /**
     * timelines_tag
     *
     * @param   string $hashtag
     * @param   array $parameters
     *          bool        $parameters['local']    Only return statuses originating from this instance
     *
     * @return  array       $response
     * @throws \ErrorException
     */
    public function timelines_tag ($hashtag,$parameters = array()) {
        $response = $this->_get('/api/v1/timelines/tag/'.$hashtag,$parameters);
        return $response;
    }


    /***
     * Custom management added by @tom79 https://github.com/stom79/
     */


    /**
     * getInstanceNodeInfo returns the social network type depending of the hostname
     * @param $host
     * @return string|null
     */
    public function getInstanceNodeInfo($host){
        try {
            $curl = new Curl();
            $url = "https://" . $host . "/.well-known/nodeinfo";
            $reply = $curl->get($url);


            $responseArray = \json_decode($reply->response, true);
            if ( empty($responseArray)){
                $curl = new Curl();
                $url = "https://" . $host . "/api/v1/instance";
                $reply = $curl->get($url);
                $responseArray = \json_decode($reply->response, true);
                if ( empty($responseArray)){
                    return null;
                }else{
                    return "MASTODON";
                }
            }else{
                $url = $responseArray["links"][count($responseArray["links"])-1]["href"];
                $curl = new Curl();
                $reply = $curl->get($url);
                $responseArray = \json_decode($reply->response, true);
                return strtoupper($responseArray["software"]["name"]);
            }

        } catch (\ErrorException $e) {

        }
    }


    /**
     * getSingleAccount Hydrate a MastodonAccount from API reply
     * @param $accountParams
     * @return MastodonAccount
     */
    public function getSingleAccount($accountParams){

        $MastodonAccount = new MastodonAccount();
        $MastodonAccount->setAccountId($accountParams['id']);
        $MastodonAccount->setUsername($accountParams['username']);
        $MastodonAccount->setAcct($accountParams['acct']);
        $MastodonAccount->setDisplayName($accountParams['display_name']);
        $MastodonAccount->setLocked($accountParams['locked']);
        $MastodonAccount->setBot($accountParams['bot']);
        $MastodonAccount->setCreatedAt($this->stringToDate($accountParams['created_at']));
        $MastodonAccount->setNote(strip_tags($accountParams['note']));
        $MastodonAccount->setUrl($accountParams['url']);
        $MastodonAccount->setAvatar($accountParams['avatar']);
        $MastodonAccount->setAvatarStatic($accountParams['avatar_static']);
        $MastodonAccount->setHeader($accountParams['header']);
        $MastodonAccount->setHeaderStatic($accountParams['header_static']);
        $MastodonAccount->setFollowersCount($accountParams['followers_count']);
        $MastodonAccount->setFollowingCount($accountParams['following_count']);
        $MastodonAccount->setStatusesCount($accountParams['statuses_count']);

        if( $accountParams['emojis'] && count($accountParams['emojis']) > 0){
            foreach ($accountParams['emojis'] as $_e){
                $emoji = new Emoji();
                $emoji->setUrl($_e['url']);
                $emoji->setShortcode($_e['shortcode']);
                $emoji->setStaticUrl($_e['static_url']);
                $emoji->setVisibleInPicker($_e['visible_in_picker']);
                $emoji->setMastodonAccount($MastodonAccount);
                $MastodonAccount->addEmoji($emoji);
            }
        }
        if( $accountParams['fields'] && count($accountParams['fields']) > 0){
            foreach ($accountParams['fields'] as $_f){
                $field = new CustomField();
                $field->setName($_f['name']);
                $field->setValue(strip_tags($_f['value']));
                $field->setVerifiedAt($this->stringToDate($_f['verified_at']));
                $field->setMastodonAccount($MastodonAccount);
                $MastodonAccount->addField($field);
            }
        }
        return $MastodonAccount;
    }


    /**
     * Update a Mastodon account with new params
     * @param $MastodonAccount MastodonAccount
     * @param $accountParams array
     * @return MastodonAccount
     */
    public function updateAccount(MastodonAccount $MastodonAccount, $accountParams){

        $MastodonAccount->setUsername($accountParams['username']);
        $MastodonAccount->setAcct($accountParams['acct']);
        $MastodonAccount->setDisplayName($accountParams['display_name']);
        $MastodonAccount->setLocked($accountParams['locked']);
        $MastodonAccount->setBot($accountParams['bot']);
        $MastodonAccount->setCreatedAt($this->stringToDate($accountParams['created_at']));
        $MastodonAccount->setNote(strip_tags($accountParams['note']));
        $MastodonAccount->setUrl($accountParams['url']);
        $MastodonAccount->setAvatar($accountParams['avatar']);
        $MastodonAccount->setAvatarStatic($accountParams['avatar_static']);
        $MastodonAccount->setHeader($accountParams['header']);
        $MastodonAccount->setHeaderStatic($accountParams['header_static']);
        $MastodonAccount->setFollowersCount($accountParams['followers_count']);
        $MastodonAccount->setFollowingCount($accountParams['following_count']);
        $MastodonAccount->setStatusesCount($accountParams['statuses_count']);

        foreach ($MastodonAccount->getEmojis() as $emoji){
            $MastodonAccount->removeEmoji($emoji);
        }
        if( $accountParams['emojis'] && count($accountParams['emojis']) > 0){
            foreach ($accountParams['emojis'] as $_e){
                $emoji = new Emoji();
                $emoji->setUrl($_e['url']);
                $emoji->setShortcode($_e['shortcode']);
                $emoji->setStaticUrl($_e['static_url']);
                $emoji->setVisibleInPicker($_e['visible_in_picker']);
                $emoji->setMastodonAccount($MastodonAccount);
                $MastodonAccount->addEmoji($emoji);
            }
        }
        foreach ($MastodonAccount->getFields() as $field){
            $MastodonAccount->removeField($field);
        }
        if( $accountParams['fields'] && count($accountParams['fields']) > 0){
            foreach ($accountParams['fields'] as $_f){
                $field = new CustomField();
                $field->setName($_f['name']);
                $field->setValue(strip_tags($_f['value']));
                $field->setVerifiedAt($this->stringToDate($_f['verified_at']));
                $field->setMastodonAccount($MastodonAccount);
                $MastodonAccount->addField($field);
            }
        }
        return $MastodonAccount;
    }

    public function stringToDate($string_date){

        try {
           return new \DateTime($string_date);
        } catch (\Exception $e) {
        }
    }


    /**
     * getNotifications Hydrate an array of Notification from API reply
     * @param $notificationParams
     * @return array
     */
    public function getNotifications($notificationParams){
        $notifications = [];
        foreach ($notificationParams as $notificationParam)
            $notifications[] = $this->getSingleNotification($notificationParam);
        return $notifications;
    }

    /**
     * getStatuses Hydrate an array of Status from API reply
     * @param $statusParams
     * @return array
     */
    public function getStatuses($statusParams){
        $statuses = [];
        foreach ($statusParams as $statusParam)
            $statuses[] = $this->getSingleStatus($statusParam);
        return $statuses;
    }

    /**
     * getSingleNotification Hydrate a Notification from API reply
     * @param $notificationParams
     * @return Notification
     */
    public function getSingleNotification($notificationParams){
        $notification = new Notification();
        $notification->setId($notificationParams['id']);
        $notification->setType($notificationParams['type']);
        $notification->setCreatedAt($this->stringToDate($notificationParams['created_at']));
        $notification->setAccount($this->getSingleAccount($notificationParams['account']));
        if( isset($notificationParams['status']) )
            $notification->setStatus($this->getSingleStatus($notificationParams['status']));
        return $notification;
    }



    /**
     * getSingleStatus Hydrate a Status from API reply
     * @param $statusParams
     * @return Status
     */
    public function getSingleStatus($statusParams){

        $status = new Status();
        $status->setId($statusParams['id']);
        $status->setUrl($statusParams['url']);
        $status->setUri($statusParams['uri']);
        $status->setAccount($this->getSingleAccount($statusParams['account']));
        $status->setInReplyToId($statusParams['in_reply_to_id']);
        $status->setInReplyToAccountId($statusParams['in_reply_to_account_id']);
        $status->setContent($statusParams['content']);
        $status->setCreatedAt($this->stringToDate($statusParams['created_at']));

        if(  isset($statusParams['emojis']) && count($statusParams['emojis']) > 0){
            $emojis = [];
            foreach ($statusParams['emojis'] as $_e){
                $emoji = new Emoji();
                $emoji->setUrl($_e['url']);
                $emoji->setShortcode($_e['shortcode']);
                $emoji->setStaticUrl($_e['static_url']);
                $emoji->setVisibleInPicker($_e['visible_in_picker']);
                $emojis[] = $emoji;
            }
            $status->setEmojis($emojis);
        }
        $status->setRepliesCount($statusParams['replies_count']);
        $status->setReblogsCount($statusParams['reblogs_count']);
        $status->setFavouritesCount($statusParams['favourites_count']);
        $status->setReblogged($statusParams['reblogged']);
        $status->setFavourited($statusParams['favourited']);
        $status->setMuted($statusParams['muted']);
        $status->setSensitive($statusParams['sensitive']);
        $status->setSpoilerText($statusParams['spoiler_text']);;
        $status->setVisibility($statusParams['visibility']);
        if(  isset($statusParams['media_attachments']) && count($statusParams['media_attachments']) > 0){
            $media_attachments = [];
            foreach ($statusParams['media_attachments'] as $_m){
                $attachment = new Attachment();
                $attachment->setId($_m['id']);
                $attachment->setUrl($_m['url']);
                $attachment->setType($_m['type']);
                if($_m['remote_url'])
                    $attachment->setRemoteUrl($_m['remote_url']);
                $attachment->setPreviewUrl($_m['preview_url']);
                if($_m['text_url'])
                    $attachment->setTextUrl($_m['text_url']);
                $attachment->setMeta(serialize($_m['meta']));
                if($_m['description'])
                    $attachment->setDescription($_m['description']);
                $media_attachments[] = $attachment;
            }
            $status->setMediaAttachments($media_attachments);
        }
        if( isset($statusParams['mentions']) && count($statusParams['mentions']) > 0){
            $mentions = [];
            foreach ($statusParams['mentions'] as $_m){
                $mention = new Mention();
                $mention->setUrl($_m['url']);
                $mention->setAcct($_m['acct']);
                $mention->setUsername($_m['username']);
                $mention->setId($_m['id']);
                $mentions[] = $mention;
            }
            $status->setMentions($mentions);
        }

        if( isset($statusParams['tags']) && count($statusParams['tags']) > 0){
            $tags = [];
            foreach ($statusParams['tags'] as $_t){
                $tag = new Tag();
                $tag->setUrl($_t['url']);
                $tag->setName($_t['name']);
                $tag->setHistory(isset($_t['history'])?$_t['history']:[]);
                $tags[] = $tag;
            }
            $status->setTags($tags);
        }

        if( isset($statusParams['application']) && count($statusParams['application']) == 1){
            $application = new Application();
            $application->setName($statusParams['application']['name']);
            $application->setWebsite($statusParams['application']['website']);
            $status->setApplication($application);
        }
        $status->setLanguage($statusParams['language']);
        $status->setPinned(isset($statusParams['pinned'])?true:false);
        if( $statusParams['reblog'] )
            $status->setReblog($this->getSingleStatus($statusParams['reblog']));
        return $status;
    }


    /**
     * getSingleAttachment Hydrate an Attachment from API reply
     * @param $mediaParams
     * @return Attachment
     */
    public function getSingleAttachment($mediaParams){

        $attachment = new Attachment();
        $attachment->setId($mediaParams['id']);
        $attachment->setUrl($mediaParams['url']);
        $attachment->setType($mediaParams['type']);
        if($mediaParams['remote_url'])
            $attachment->setRemoteUrl($mediaParams['remote_url']);
        $attachment->setPreviewUrl($mediaParams['preview_url']);
        if($mediaParams['text_url'])
            $attachment->setTextUrl($mediaParams['text_url']);
        $attachment->setMeta(serialize($mediaParams['meta']));
        if($mediaParams['description'])
            $attachment->setDescription($mediaParams['description']);
        return $attachment;
    }

}
