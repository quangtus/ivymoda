<?php
/**
 * Environment Helper - Load .env file configuration
 */

class EnvHelper {
    
    private static $loaded = false;
    private static $env = [];
    
    /**
     * Load environment variables from .env file
     */
    public static function load($envPath = null) {
        if (self::$loaded) {
            return;
        }
        
        // Default path to .env file
        if ($envPath === null) {
            $envPath = dirname(dirname(__DIR__)) . '/.env';
        }
        
        if (!file_exists($envPath)) {
            return;
        }
        
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
                continue;
            }
            
            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                self::$env[$key] = $value;
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        
        return isset(self::$env[$key]) ? self::$env[$key] : $default;
    }
    
    /**
     * Get all environment variables
     */
    public static function all() {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$env;
    }
}
