<?php

namespace KdybyTests\Doctrine;

use Doctrine;
use Doctrine\ORM\Events;

class NewListener implements Doctrine\Common\EventSubscriber
{

    public $calls = [];

    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }

    public function onFlush()
    {
        $this->calls[] = func_get_args();
    }

}
