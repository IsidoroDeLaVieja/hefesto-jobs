<?php /*dlv-code-engine***/

if ($state->message()->getHeader('x-first-time') !== 'no-first-time') {
    return;
}

$httpList = $state->memory()->get('httpList');
$httpMessage = array_shift($httpList);

if( isset( $httpMessage['delay']['key'] ) && isset( $httpMessage['delay']['seconds'] ) ) {
    $delaySeconds = $httpMessage['delay']['seconds'];
    $now = microtime(true);
    $key = 'tasks:delay:'.$httpMessage['delay']['key'];

    DatabaseName::run($state,[]);
    RedisGet::run($state,[
        'key' => $key,
        'target' => 'tasksDelay'
    ]);
    $tasksDelay = $state->memory()->get('tasksDelay');

    if ( $tasksDelay && 
        ($now - $tasksDelay['lastTaskExecuted']) < $delaySeconds) {
        return;
    }

    RedisSet::run($state,[
        'key' => $key,
        'expire' => 86400,//1 day
        'value' => [ 
            'lastTaskExecuted' =>  $now 
        ]
    ]);
}

$state->groups()->enable('PROCESS_TASK');
$state->memory()->set('httpRequest',$httpMessage);

