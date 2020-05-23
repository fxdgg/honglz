<?php

class Pics extends BLH_Controller
{
    protected $_picRoot;
    public function __construct()
    {
        parent::__construct(FALSE);
        $this->_picRoot = dirname(dirname(dirname(__FILE__)))."/uploads/";
    }

    function test()
    {
        echo 'test ok';
    }

    function p($folder, $name=null)
    {
        $filename = $folder.'/'.$name;
        if($filename && file_exists($this->_picRoot.$filename) && false == strpos($filename,"..") )
        {
        	header("Content-type:image/jpeg");
            echo file_get_contents($this->_picRoot.$filename);
        }
        exit('pics is not found.');
    }
}
