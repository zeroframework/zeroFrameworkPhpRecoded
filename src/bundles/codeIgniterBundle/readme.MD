connect Hook To Event
=====================

function eventCodeIgniter($eventName)
{
    $eventManager = app::getInstance()->getEventManager();

    $eventManager->notify("codeigniter.$eventName");
}

function createHookZF($name)
{
    return array(
        'class'    => '',
        'function' => 'eventCodeIgniter',
        'filename' => 'hooks.php',
        'filepath' => 'config',
        'params'   => array($name)
    );
}

$hook['pre_system'] = createHookZF("pre_system");
$hook['pre_controller'] = createHookZF("pre_controller");
$hook['post_controller_constructor'] = createHookZF("post_controller_constructor");
$hook['post_controller'] = createHookZF("post_controller");
$hook['display_override'] = createHookZF("display_override");
$hook['cache_override'] = createHookZF("cache_override");
$hook['post_system'] = createHookZF("post_system");