<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->get('/logout', function(Request $request) {
    if (!$return_url = $request->query->get('return_url')) {
        $return_url = '/';
    }
    $response = new RedirectResponse($return_url);
    $expires = new \DateTime();
    $expires->add(new \DateInterval('PT1H'));
    $response->headers->setCookie(new Cookie('ucinetid_auth', 'no_key', $expires, '/', '.uci.edu'));

    return $response;
});
$app->match('/login', function(Request $request) use ($app) {
    if (!$return_url = $request->query->get('return_url')) {
        $return_url = '/';
    }
    if ($ucinetid = $request->get('ucinetid')) {
        $response = new RedirectResponse($return_url);
        $expires = new \DateTime();
        $expires->add(new \DateInterval('PT1H'));

        $response->headers->setCookie(new Cookie('ucinetid_auth', $ucinetid, $expires, '/', '.uci.edu'));

        return $response;
    } else {
        return $app['twig']->render('login.html.twig');
    }
});
$app->get('/check', function(Request $request) use ($app) {
    $ucinetid_auth = $request->query->get('ucinetid_auth');
    $template_vars = array(
        'ucinetid' => $ucinetid_auth,
        'current_time' => time(),
        'auth_host' => $request->server->get('REMOTE_ADDR')
    );

    return $app['twig']->render('index.html.twig', $template_vars);
});

$app->run();
