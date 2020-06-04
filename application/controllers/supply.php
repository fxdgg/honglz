<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Supply extends BLH_Controller{

    public function __construct()
    {
        parent::__construct(false);
    }

    // 资源对接页面
    public function add() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $id = (int)$this->input->get('id');
        if (empty($id)) {
            BLH_Utilities::outputError(-1, '参数不能为空或不合法');
        }

        $this->load->view('default/supply_add', ['title'=>'发布信息', 'jobId'=>$id]);
    }

    // 资源对接提交信息
    public function doAdd() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));
        }

        $needId = $this->input->post('nid');
        $content = $this->input->post('content');
        $contact = $this->input->post('contact');
        if (empty($needId) || empty($content) || empty($contact)) {
            BLH_Utilities::outputError(-1, '参数不能为空或不合法');
        }

        // 检查该提问记录是否存在
        $this->load->model('Jdjobbase');
        $needInfo = $this->Jdjobbase->fetchInfoById($needId);
        if (empty($needInfo)) {
            BLH_Utilities::outputError(-2, '暂无对应的供应信息，请联系管理员');
        }
        $answerUid = isset($this->_userid) ? (int)$this->_userid : 0;
        if ((int)$needInfo['creatorUid'] === $answerUid) {
            BLH_Utilities::outputError(-2, '不能回答自己的问题哦~');
        }

        $params = [
            'need_id'    => (int)$needId,
            'asker_uid'  => (int)$needInfo['creatorUid'],
            'answer_uid' => $answerUid,
            'resource'   => $content,
            'contact'    => $contact,
        ];
        $this->load->model('Jdsupply');
        $needRet = $this->Jdsupply->createBaseInfo($params, true);
        BLH_Utilities::outputSuccess(['status'=>1, 'id'=>$needId]);
    }
}
