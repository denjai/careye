<?php

namespace App\Services;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginClient
{
    private HttpClientInterface $client;
    private HttpBrowser $browser;

    public function __construct(
        HttpClientInterface  $client,
        HttpBrowser $browser
    ) {
        $this->client = $client;
        $this->browser = $browser;
    }

    public function logIn(): ?Crawler
    {
        $crawler = $this->browser
            ->request('POST', 'https://www.mobile.bg/pcgi/mobile.cgi')
            ->filter('div.loginLinks')
        ;

        if ($crawler->text('empty') !== 'empty') {
            $crawlerLogin = $this->browser->clickLink('Вход');
            //$this->browser->clickLink('[name=logtype] [value=2]');

            //var_dump($crawlerLogin->filter('[name="login"]')->form());
            $form = $crawlerLogin->filter('[name="login"]')->form([
                    'usr' => 'doncho.t@gmail.com',
                    'pwd' => 'doncho91',
                    'logtype' => 2,
             ]);
            var_dump($form->getPhpValues());
            var_dump($form->getUri());

            return $this->browser->submit($form);
//            return $this->browser->submitForm(
//                'a.loginButton',
//                [
//                    'usr' => 'doncho.t@gmail.com',
//                    'pwd' => 'doncho91'
//                ]
//            );
        } else {
            var_dump('already logged in');
            //TODO return default crawler
        }

        return null;
    }

}