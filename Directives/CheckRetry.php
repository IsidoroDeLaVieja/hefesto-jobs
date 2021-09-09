<?php /*dlv-code-engine***/

$attempts = 10;
$delayFactor = 60;

$initialHeaders = $state->memory()->get('initial-headers');
$retryCounter = isset($initialHeaders['X-RETRY-COUNTER']) 
    ? (int)$initialHeaders['X-RETRY-COUNTER'] 
    : 0;

if ($state->message()->getStatus() >= 500) {
    $retryCounter++;
    if ( $retryCounter < $attempts ) {
        $oldList = $state->memory()->get('http-list-old');
        $state->memory()->set('http-list',$oldList);
        $state->memory()->set('delay',$retryCounter * $delayFactor);
    }
} else {
    $retryCounter = 0;
}

$initialHeaders['X-RETRY-COUNTER'] = (string)$retryCounter;
$state->memory()->set('initial-headers',$initialHeaders);