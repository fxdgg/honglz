<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cronprocess extends BLH_Controller {

    public function __construct()
    {
        parent::__construct(FALSE);
    }
    /**
     * 定时任务处理程序
     * @param $action
     */
	public function exec($action = '')
	{
		if (!$this->input->is_cli_request())
		{
			exit('No permission to access');
		}
		switch($action)
		{
			case 'checkTmpUnion':
				//@TODO /usr/bin/php hlztest.php cronprocess exec checkTmpUnion
				//定时检查临时社团的逻辑
				#$this->load->model('UnionManage');
				#$this->UnionManage->checkTmpUnionCron();
				exit('ok');
				break;
			default:
				exit('Unknown action to access');
				break;
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
