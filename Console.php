<?php
/*
 *
 * @author Animesh Chakraborty <darraghenright@gmail.com>
 */
class Console
{
    
    private static $output = null;
    
    
    private static $isOn = true;
    private static $displayType = 'log';
    
    private static $traceData;
    
    public static function log($data, $message = null)
    {   
        self::renderConsole($data,$message,$type='log');   
    }
  
    public static function info($data, $message = null)
    {   
        self::renderConsole($data,$message,$type='info');   
    }
  
    public static function table($data, $message = null)
    {   
        self::renderConsole($data,$message,$type='table');   
    }
  
    public static function error($data, $message = null)
    {   
        self::renderConsole($data,$message,$type='error');   
    }
  
    public static function debug($data, $message = null)
    {   
        self::renderConsole($data,$message,$type='debug');   
    }

  
    public static function renderConsole($data, $message,$type)
    {   
        self::setTraceData(debug_backtrace());
        if (self::$isOn) {
            self::addMessage($message);
            self::addData($data,$type);
            self::output();
        }    
    }
       
    public static function on()
    {
        self::$isOn = true;
    }
    
    public static function off()
    {
        self::$isOn = false;
    }
    
    public static function type($type)
    {
        self::$displayType = checkType($type);
    }

    public static function checkType($type)
    {
        $type=strtoupper($type);
        switch ($type) {
            case LOG:
                return 'log';
            case INFO:
                return 'info';
            case TABLE:
                return 'table';
            case ERROR:
                return 'error';
            case DEBUG:
                return 'debug';   
            
            default:
                return 'log';
                
        }
    }
    
    public static function setTraceData(array $trace)
    {
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        self::$traceData = sprintf('[%s:~%d]', basename($file), $line);
    }
    
    protected static function formatData($data)
    {
        return is_scalar($data) || is_null($data) ? var_export($data, true) : self::formatComposite($data);
    }
    
    protected static function formatComposite($data)
    {
        return !is_resource($data) ? print_r($data, true) : self::formatResource($data);
    }
    
    
    protected static function formatResource($data)
    {
        return sprintf('%s: %s', print_r($data, true), get_resource_type($data));
    }
    
     
    protected static function addMessage($message)
    {
        self::$output = $message ? sprintf('console.log("%s %s:");', self::$traceData, $message) : null;
    }
     
    protected static function addData($data,$type)
    {
        if($type!='table')
            self::$output .= sprintf('console.'.$type.'(%s);', json_encode(self::formatData($data), JSON_NUMERIC_CHECK));
        else
            self::$output .= sprintf('console.table(%s);', json_encode(self::formatData($data), JSON_NUMERIC_CHECK));
    }
    
    protected static function output()
    {
        printf('<script type="text/javascript">%s</script>', self::$output);
    }
}
