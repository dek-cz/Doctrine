<?php

namespace Kdyby\Doctrine\DI;

use ArrayIterator;

interface IteratorAggregate
{

    /**
     * @param ?int $hydrationMode
     * @throws QueryException
     * @return ArrayIterator
     */
    public function getIterator(?int $hydrationMode = ORM\AbstractQuery::HYDRATE_OBJECT): ArrayIterator;

}
