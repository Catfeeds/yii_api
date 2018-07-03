<?php
namespace common\extensions\wxpay\lib;
class Exception  {
    protected $message;
    private $string;
    protected $code;
    protected $file;
    protected $line;
    private $trace;
    private $previous;


    final private function __clone () {}

    /**
     * @param message[optional]
     * @param code[optional]
     * @param previous[optional]
     */
    public function __construct ($message = null, $code = null, $previous = null) {}

    final public function getMessage () {}

    final public function getCode () {}

    final public function getFile () {}

    final public function getLine () {}

    final public function getTrace () {}

    final public function getPrevious () {}

    final public function getTraceAsString () {}

    public function __toString () {}

}