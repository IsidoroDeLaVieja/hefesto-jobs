<?php /*dlv-code-engine***/

$state->message()->deleteHeader('host');
$headers = $state->message()->getHeaders();
$headers['x-first-time'] = 'no-first-time';
$state->memory()->set( 'initialHeaders' , $headers );
