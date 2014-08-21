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
    $response->headers->setCookie(new Cookie('ucinetid_auth', 'no_key', $expires, '/', '.uci.edu', false, false));

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

        $response->headers->setCookie(new Cookie('ucinetid_auth', $ucinetid, $expires, '/', '.uci.edu', false, false));

        return $response;
    } else {

        return $app['twig']->render('login.html.twig');
    }
});
$app->get('/check', function(Request $request) use ($app) {
    $ucinetid_auth = $request->query->get('ucinetid_auth');
    $ds = ldap_connect('ldap.service.uci.edu');
    $r = ldap_bind($ds);
    $sr = ldap_search($ds, 'ou=University of California Irvine,o=University of California, c=US', 'ucinetid='.$ucinetid_auth, array('campusid'));

    $template_vars = array(
        'ucinetid' => $ucinetid_auth,
        'current_time' => time(),
        'auth_host' => $request->server->get('REMOTE_ADDR'),
        'campus_id' => ldap_get_entries($ds, $sr)[0]['campusid'][0],
    );

    return $app['twig']->render('index.html.twig', $template_vars);
});

$app->run();
