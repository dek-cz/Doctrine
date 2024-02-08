<?php

namespace KdybyTests\Doctrine;

use Doctrine;

class OldListener implements Doctrine\Common\EventSubscriber
{

    public $calls = [];

    public function getSubscribedEvents()
    {
        return ['onFlush'];
    }

    public function onFlush()
    {
        $this->calls[] = func_get_args();
    }

}
