<?php
namespace Vellozzi\UrlShortenerBundle\Model;
class BaseModel
{
    /**
     * @var object logging for traces/debug.The object must implement \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    private $logger;
    /**
     * doLog  is for debugging
     * @param string $message  string to log
     * @param string $loglevel level of the log. It should be one of thi enum :emerg,alert,crit,err,warn,notice,info,debug
     */
    protected function doLog($message,$loglevel='debug')
    {
        $allowedLogLevels = array('emerg','alert','crit','err','warn','notice','info','debug');
        if (in_array($loglevel,$allowedLogLevels)
            && $this->logger instanceof \Symfony\Component\HttpKernel\Log\LoggerInterface) {
            $this->logger->$loglevel(__CLASS__.' - '.$message);
        }
    }
    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}
