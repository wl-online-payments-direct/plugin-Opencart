<?php
spl_autoload_register(function ($className) {
    if (strpos($className, 'OnlinePayments') !== 0) {
        return;
    }
	
    $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
	
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
    if (is_file($fileName)) {
        require_once $fileName;
    }
});

class OnlinePayments {
    public static function requireDependencies() {
        $requiredExtensions = ['xmlwriter', 'openssl', 'dom', 'hash', 'curl'];
		
        foreach ($requiredExtensions AS $ext) {
            if (!extension_loaded($ext)) {
                throw new Exception('The OnlinePayments library requires the ' . $ext . ' extension.');
            }
        }
    }
}

OnlinePayments::requireDependencies();