<?php
class Upload extends BLH_Controller
{
    protected $_root;
    public function __construct(){
        parent::__construct(FALSE);//TRUE, TRUE
        $this->_root= dirname(dirname(dirname(__FILE__)));
        $this->load->helper(array('form', 'url'));
    }

    function index()
    {
        $this->load->view('upload_form', array('error' => ' ' ));
    }


    function do_upload()
    {
        $ret = array('status'=>false);
        $fileBaseName = uniqid();
        $childFolder = crc32($fileBaseName)%10;
        $filepath = "{$childFolder}/$fileBaseName";
        $realfolder = "{$this->_root}/uploads/{$childFolder}";
        $config['upload_path'] = $realfolder;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = '5120';
        //$config['max_width']  = '1024';
        //$config['max_height']  = '768';
        $config['file_name'] = $fileBaseName;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
             $ret['error'] = $error = $this->upload->display_errors('','');

             //$this->load->view('upload_form', $error);
        }else{
             $data = $this->upload->data();
             $file_ext = $data['file_ext'];
             $this->load->model("Picresize");
             $this->Picresize->makeThumb($data["full_path"], "$realfolder/{$fileBaseName}_m{$file_ext}", 200, 200);
             $this->Picresize->makeThumb($data["full_path"], "$realfolder/{$fileBaseName}_s{$file_ext}", 100, 100);
             //$this->Picresize->makeThumb($data["full_path"], "$realfolder/{$fileBaseName}_50{$file_ext}", 50, 50);

             $finalPath = $realfolder."/".$fileBaseName.$file_ext;
             rename($data["full_path"], $finalPath);

             $ret['status'] = true;
             $ret['path'] = "/pics/p/{$filepath}{$file_ext}";
        }
        echo json_encode($ret);
    }

}
