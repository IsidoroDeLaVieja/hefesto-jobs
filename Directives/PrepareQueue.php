<?php /*dlv-code-engine***/

$defaultSecondsDelay = 10;
$attempts = 10;
$delayFactor = 6;

$state->groups()->enable('WITH_QUEUE');
$state->groups()->disable('NORMAL_FLOW');

$httpList = $state->memory()->get('httpList');

$delay = isset( $httpList[0]['delay']['seconds'] ) 
    ? $httpList[0]['delay']['seconds']
    : $defaultSecondsDelay;
$state->memory()->set('delay',$delay);

if( !$state->groups()->isEnabled('PROCESS_TASK') ) {
    return;
}

$initialHeaders = $state->memory()->get('initialHeaders');
$retryCounter = isset($initialHeaders['X-RETRY-COUNTER']) 
    ? (int)$initialHeaders['X-RETRY-COUNTER'] 
    : 0;

if ($state->message()->getStatus() >= 500) {
    $retryCounter++;
    if ( $retryCounter < $attempts ) {
        $state->memory()->set('delay',$retryCounter * $delayFactor * $delay);
    }
} else {
    
    array_shift($httpList);
    if (count($httpList) === 0) {
        $state->groups()->disable('WITH_QUEUE');
        return;
    }

    $delay = isset( $httpList[0]['delay']['seconds'] ) 
        ? $httpList[0]['delay']['seconds']
        : $defaultSecondsDelay;
    $state->memory()->set('delay',$delay);

    $retryCounter = 0;
    $state->memory()->set('httpList',$httpList);
}

$initialHeaders['X-RETRY-COUNTER'] = (string)$retryCounter;
$state->memory()->set('initialHeaders',$initialHeaders);