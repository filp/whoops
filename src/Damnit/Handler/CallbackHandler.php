<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Handler;
use Damnit\Handler\Handler;
use InvalidArgumentException;

/**
 * Wrapper for Closures passed as handlers. Can be used
 * directly, or will be instantiated automagically by Damnit\Run
 * if passed to Run::pushHandler
 */
class Handler implements HandlerInterface
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        if(!is_callable($callable)) {
            throw new InvalidArgumentException(
                'Argument to ' . __METHOD__ . ' must be valid callable'
            );
        }

        $this->callable = $callable;
    }

    /**
     * @return int|null
     */
    public function handle()
    {
        return call_user_func($this->callable, $this);     
    }
}
