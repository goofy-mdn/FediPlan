<?php
/**
 * Created by fediplan.
 * User: tom79
 * Date: 09/08/19
 * Time: 17:37
 */

namespace App\Twig;

use App\SocialEntity\MastodonAccount;
use App\SocialEntity\Status;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {

        return [
            new TwigFunction('convertAccountEmoji', [$this, 'accountEmoji']),
            new TwigFunction('convertStatusEmoji', [$this, 'statusEmoji']),
        ];
    }

    public function accountEmoji($account, $content)
    {
       if( $account instanceof MastodonAccount){
            foreach( $account->getEmojis() as $emoji){
                $content = preg_replace("(:" . $emoji->getShortcode() .":)", "<img src='". $emoji->getUrl() . "' alt='".$emoji->getShortcode()."'  title='".$emoji->getShortcode()."' width='20'/>", $content);
            }
        }
       return $content;
    }

    public function statusEmoji($status, $content)
    {
        if( $status instanceof Status){
            foreach( $status->getEmojis() as $emoji){
                $content = preg_replace("(:" . $emoji->getShortcode() . ":)", "<img src='". $emoji->getUrl() . "' alt='".$emoji->getShortcode()."' title='".$emoji->getShortcode()."' width='20'/>", $content);
            }
        }
        return $content;
    }
}