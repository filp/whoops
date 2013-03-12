<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Exception;

class Frame
{
    /**
     * @var array
     */
    private $frame;

    /**
     * @param array[]
     */
    public function __construct(array $frame)
    {
        $this->frame = $frame;
    }
}
