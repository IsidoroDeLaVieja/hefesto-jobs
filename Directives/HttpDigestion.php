<?php /*dlv-code-engine***/

$state->groups()->disable('NORMAL_FLOW');
$list = $state->memory()->get('http-list');

$noFirstTime = 'no-first-time';
if ($state->message()->getHeader('x-first-time') === $noFirstTime) {

    $state->groups()->enable('WITH_REQUEST');
    
    $state->memory()->set('http-list-old',$list);
    $httpMessage = array_shift($list);//extract and delete first element
    $state->memory()->set('http-list',$list);
    $state->memory()->set('http-request',$httpMessage);
}

if ( count($list) > 0) {
    
    $state->groups()->enable('WITH_QUEUE');

    $state->message()->deleteHeader('host');
    $headers = $state->message()->getHeaders();
    $headers['x-first-time'] = $noFirstTime;
    $state->memory()->set('initial-headers',$headers);
    $state->memory()->set('delay',10);
} 
