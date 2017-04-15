<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Handler;
use Whoops\Handler\Handler;

/**
 * Catches an exception and sends it to an installation
 * of Sentry (getsentry.com).
 */
class RavenHandler extends Handler
{
    /**
     * @var bool
     */
    private $ravenDsn;

    /**
     * @var array
     */
    private $ravenOptions = array();

    /**
     * @var array
     */
    private $extraData = array();

    /**
     * @param  bool|null $ravenDsn
     * @return null|bool
     */
    public function ravenDsn($ravenDsn = null)
    {
        if(func_num_args() == 0) {
            return $this->ravenDsn;
        }

        $this->ravenDsn = (string) $ravenDsn;
    }

    /**
     * @param  bool|null $ravenOptions
     * @return null|bool
     */
    public function ravenOptions($ravenOptions = null)
    {
        if(func_num_args() == 0) {
            return $this->ravenOptions;
        }

        $this->ravenOptions = (array) $ravenOptions;
    }

    /**
     * @param  bool|null $extraData
     * @return null|bool
     */
    public function extraData($extraData = null)
    {
        if(func_num_args() == 0) {
            return $this->extraData;
        }

        $this->extraData = (array) $extraData;
    }

    /**
     * @return int
     */
    public function handle()
    {
        // Check that Raven is available. If it's not then just fail silently,
        // there is no point throwing an error since this is the library that
        // deals with them. Best way is to list the Composer package
        // "raven/raven" as a requirement.
        if(!class_exists('Raven_Client')) {
            return Handler::DONE;
        }
        // Attempt to make a new instance of the Raven_Client class, using the
        // DSN and options array provided.
        try {
            $raven = new Raven_Client($this->ravenDsn(), $this->ravenOptions());
        }
        // Again, if there is an error, fail silently and move onto the next
        // Whoops! handler.
        catch(InvalidArgumentException $e) {
            return Handler::DONE;
        }

        // Now that we seem to have a functional client, log the exception.
        $client->captureException(
            $this->getException(),
            $this->extraData()
        );
        return Handler::DONE;
    }
}
