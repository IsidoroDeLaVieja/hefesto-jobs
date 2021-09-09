<?php /*dlv-code-engine***/

DatabaseName::run($state,[
    'global' => true
]);
$dbName = $state->memory()->get('db-name');

$value = \Illuminate\Support\Facades\Redis::get($dbName.':jobs:'.$state->message()->getPathParam('id'));
if ( is_null($value) ) {
    $state->memory()->set('error.status', '404');
    $state->memory()->set('error.message', 'Job Not Found');
    throw new \Exception();
}

$body = json_decode($value,true);
ModifyMessage::run($state,[
    'body' => $body,
    'headers' => [
        'Content-Type' => 'application/json'
    ]
]);