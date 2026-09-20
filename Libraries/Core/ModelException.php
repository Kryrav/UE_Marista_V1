<?php
// exceptions/ModelException.php
class ModelException extends Exception {
    private $errorData;
    
    public function __construct($message, $code = 0, $errorData = [], Exception $previous = null) {
        $this->errorData = $errorData;
        parent::__construct($message, $code, $previous);
    }
    
    public function getErrorData() {
        return $this->errorData;
    }
}