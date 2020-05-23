<?php
class Blocks extends BLH_Controller{
    protected $_tableName;
    /**
     * 是否是回复帖子/消息
     * @var boolean
     */
    private $isReply = FALSE;
    /**
     * 发布/回复消息、帖子，校验pid、rootid时的帖子数据
     * @var array
     */
    private $newPostList = array();
    /**
     * 上传图片后的返回值
     * @var array
     */
    private $ret_pics = array();
    
    public function __construct(){
        parent::__construct(true);
    }


    protected function _setTableName($blockName){
        $this->_tableName = "{$blockName}blockpost";
    }

    public function types(){
        $this->load->model('Block');
        $ret = $this->Block->getTypeList($this->_userid);
        echo json_encode($ret);
    }
    
    /**
     * 查询帖子
     * @param string $blockname
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function posts($blockname, $page=1, $pagesize=10, $timestamp = 0)
    {
        //$pagesize = 20;
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName：' . $blockname);
        }
        
        $this->_setTableName($blockname);
        
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
        if($pagesize<0) $pagesize = 10;

        $ret = array('status'=>FALSE);
        if($page>=1 && $pagesize>=1)
        {
            $idArr = array();
            $this->load->model('Block');
            $this->Block->init($this->_tableName);
            $ret['status'] = TRUE;
            $ret['timestamp'] = SYS_TIME;
            list($ret['posts'], $userIds) = $this->Block->postsNew($page, $pagesize, $timestamp, $this->_userid, $blockname);
            if(count($userIds) > 0)
            {
                $this->load->model('Userinfo');
                $ret['users'] = array_values($this->Userinfo->batchUser($userIds));
            }
        }
        echo json_encode($ret);
    }
    /**
     * 查询帖子-New
     * @param string $blockname
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function postslist($blockname, $page=1, $pagesize=10, $timestamp = 0)
    {
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName：' . $blockname);
        }
        
        $this->_setTableName($blockname);
        
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
        if($pagesize<0) $pagesize = 10;

        $ret = array('status'=>FALSE);
        if($page>=1 && $pagesize>=1)
        {
            $idArr = array();
            $this->load->model('Block');
            $this->Block->init($this->_tableName);
            $ret['status'] = TRUE;
            $ret['timestamp'] = SYS_TIME;
            $ret['posts'] = $this->Block->postsListNew($page, $pagesize, $timestamp, $this->_userid, $blockname);
        }
        exit(json_encode($ret));
    }
    /**
     * 根据rootid获取所有回复的帖子集合
     * Add At 2014-08-06 12:15
     * @param $blockname
     * @param $rootid
     */
    public function rootlist($blockname, $rootid=0)
    {
        if ($rootid <= 0 OR !is_numeric($rootid))
        {
            BLH_Utilities::outputError(-1, 'Invalid rootid：' . $rootid);
        }
        $rootid = intval($rootid);
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName：' . $blockname);
        }
        $this->_setTableName($blockname);
        $this->load->model('Block');
        $this->Block->init($this->_tableName);
        
        $ret = array();
        $ret['status'] = TRUE;
        $ret['timestamp'] = SYS_TIME;
        list($ret['posts'], $userIds) = $this->Block->postsRootList($this->_userid, $blockname, $rootid);
        if(count($userIds) > 0)
        {
            $this->load->model('Userinfo');
            $ret['users'] = array_values($this->Userinfo->batchUser($userIds));
        }
        echo json_encode($ret);
        exit;
    }
    /**
     * 获取【最新的招聘信息】列表
     * @param $blockname
     * @param $page
     * @param $pagesize
     */
    public function recruitlist($blockname, $page=1, $pagesize=10)
    {
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockname);
        }
        $page = max(1, intval($page));
        $pagesize = max(1, intval($pagesize));
        
        $ret = array('status'=>FALSE, 'lists'=>array());
        if($page>=1 && $pagesize>=1)
        {
            $ret['status'] = TRUE;
            //获取【最新的招聘信息】列表
            $this->load->model('Block');
            $this->_setTableName($blockname);
            $this->Block->init($this->_tableName);
            $ret['lists'] = $this->Block->fetch_recruit_list($this->_userid, $blockname, $page, $pagesize);
            BLH_Utilities::outputSuccess($ret);
        }
        BLH_Utilities::outputError(27100, 'params is error');
    }
    /**
     * 获取【感兴趣】的列表
     * @param $blockname
     * @param $page
     * @param $pagesize
     */
    public function likelist($blockname, $page=1, $pagesize=10)
    {
        $page = max(1, intval($page));
        $pagesize = max(1, intval($pagesize));
        
        $ret = array('status'=>FALSE, 'lists'=>array());
        if($page>=1 && $pagesize>=1)
        {
            $ret['status'] = TRUE;
            $this->load->model('RecruitLog');
            $ret['lists'] = $this->RecruitLog->fetch_like_list($this->_userid, $page, $pagesize);
            BLH_Utilities::outputSuccess($ret);
        }
        BLH_Utilities::outputError(27100, 'params is error');
    }
    /**
     * 记录【感兴趣/应聘】的操作
     * @param $blockname
     * @param $page
     * @param $pagesize
     */
    public function choose($blockname)
    {
        $data = $this->input->post(NULL, TRUE);
        $type = !empty($data['type']) ? $data['type'] : '';
        $id = !empty($data['id']) ? (int)$data['id'] : 0;
        $this->load->model('RecruitLog');
        if ($blockname == 'recruit' && is_numeric($id) && !empty($type) && in_array($type, RecruitLog::$category_type, TRUE))
        {
            if (!$this->_userid OR !is_numeric($this->_userid))
            {
                $this->login_error_data['errmsg'] = 'params uid is error';
                BLH_Utilities::outputError($this->login_error_data);
            }
            //获取招聘信息详细内容
            $this->load->model('Block');
            $this->_setTableName($blockname);
            $this->Block->init($this->_tableName);
            $recruitData = $this->Block->fetchSimpleById($id);
            if (empty($recruitData) OR $recruitData['state'] != 'new')
            {
                BLH_Utilities::outputError(27101, 'recruit data is error');
            }
            $this->load->model('Userinfo');
            switch ($type)
            {
                case 'apply': //应聘
                    //获取用户当前的职位分类
                    $userData = $this->Userinfo->fetch_user_by_id($this->_userid);
                    //记录用户应聘或感兴趣的操作
                    $params = array(
                        'type' => $type,
                        'userId' => $this->_userid,
                        'jobId' => (int)$id, //职位ID
                        'categoryLast' => !empty($userData['category']) ? $userData['category'] : '', //上一次的职位分类
                        'category' => !empty($recruitData['category']) ? $recruitData['category'] : '', //新的职位分类
                    );
                    unset($userData);
                    $msg = '收到应聘，专人通过QQ与您联系';
                    break;
                case 'like': //感兴趣
                    //记录用户应聘或感兴趣的操作
                    $params = array(
                        'type' => $type,
                        'userId' => $this->_userid,
                        'jobId' => (int)$id, //职位ID
                    );
                    $msg = '谢谢您感兴趣哦，我们将继续努力';
                    break;
            }
            if (!empty($params))
            {
                $newId = $this->RecruitLog->record_operation_log($params);
                //更新用户职业分类属性
                if (isset($recruitData['category']) && !empty($recruitData['category']))
                {
                    $user_post = array();
                    $user_post['category'] = $recruitData['category'];
                    !empty($user_post) && $this->Userinfo->edit($this->_userid, $user_post);
                }
            }
            BLH_Utilities::outputSuccess(array('id'=>$newId, 'msg'=>$msg));
        }
        BLH_Utilities::outputError(27100, 'params is error');
    }
    /**
     * 回复消息/帖子
     * @param $blockname
     */
    public function reply($blockname)
    {
        $this->isReply = TRUE;
        $this->add($blockname);
    }
    /**
     * 发布消息/帖子
     * @param string $blockname
     * @param boolean $isReply
     */
    public function add($blockname)
    {
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockname);
        }
        $this->_setTableName($blockname);
        
        $ret = $post_ids = array();
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run('blocks/create') == false)
        {
            $ret['errcode'] = 1;
            $ret['errmsg'] = validation_errors();
            BLH_Utilities::outputError($ret['errcode'], $ret['errmsg']);
        }else{
            $data = $this->input->post(NULL, true);
            $data['userid'] = $this->_userid;
            //是否是回复消息/帖子
            $data['isReply'] = $this->isReply;
            $isNewPost = $blockRet = FALSE;
            $unionIdArr = array();
            //检查是否同时上传图片
            if (isset($this->ret_pics) && !empty($this->ret_pics))
            {
                if ($this->ret_pics['status'] == TRUE)
                {
                    //图片上传路径
                    $data['imgUrl'] = $this->ret_pics['path'];
                }else{
                    //图片上传失败$this->ret_pics['error'];
                    $ret['status'] = FALSE;
                    $ret['errcode'] = 2;
                    $ret['errmsg'] = $this->ret_pics['error'];
                    BLH_Utilities::outputError($ret);
                }
            }
            //发布时
            if (FALSE == $data['isReply'])
            {
                //用户所在社团的ID或用户所在的所有社团的ID
                if (isset($data['unionId']) && !empty($data['unionId']))
                {
                    $unionIdArr = explode(',', $data['unionId']);
                    $existsUnion = $userJoinUnions = array();
                    //检查该用户目前已加入的社团信息
                    $this->load->model('UserUnion');
                    $userUnionList = $this->UserUnion->searchUnionListByUserId($this->_userid);
                    if (empty($userUnionList))
                    {
                        BLH_Utilities::outputError(27007, 'you are not in union');
                    }
                    foreach ($userUnionList as $userUnionItem)
                    {
                        !isset($userJoinUnions[$userUnionItem['unionId']]) && $userJoinUnions[$userUnionItem['unionId']] = 0;
                        $userJoinUnions[$userUnionItem['unionId']] += 1;
                    }
                }
                //是否是私聊
                if (isset($data['privateChat']) && !empty($data['privateChat']))
                {
                    foreach ($unionIdArr as $unionIdItem)
                    {
                        if (isset($existsUnion[$unionIdItem])) continue;
                        if (!isset($userJoinUnions[$unionIdItem]) OR $userJoinUnions[$unionIdItem] <= 0)
                        {
                            BLH_Utilities::outputError(27007, 'you are not in the union['.$unionIdItem.']');
                        }
                        !isset($existsUnion[$unionIdItem]) && $existsUnion[$unionIdItem] = 1;
                        $data['unionId'] = $unionIdItem;
                        list($blockRet, $error_message_tips) = $this->Block->privateChatModel($this->_userid, $data, $this->newPostList);
                        if(is_array($blockRet) && !empty($blockRet['id']))
                        {
                            $post_ids['data'][] = $blockRet;
                            $isNewPost = TRUE;
                            break;
                        }
                    }
                }
                else
                {
                    //用户所在社团的ID或用户所在的所有社团的ID
                    if (isset($data['unionId']) && !empty($data['unionId']))
                    {
                        $isNewPost = TRUE;
                        foreach ($unionIdArr as $unionIdItem)
                        {
                            if (isset($existsUnion[$unionIdItem])) continue;
                            if (!isset($userJoinUnions[$unionIdItem]) OR $userJoinUnions[$unionIdItem] <= 0)
                            {
                                BLH_Utilities::outputError(27007, 'you are not in the union['.$unionIdItem.']');
                            }
                            !isset($existsUnion[$unionIdItem]) && $existsUnion[$unionIdItem] = 1;
                            $data['unionId'] = $unionIdItem;
                            $blockRet = $this->Block->create($data, $this->newPostList);
                            if(FALSE == $blockRet OR !is_array($blockRet) OR !isset($blockRet['id']) OR $blockRet['id'] <= 0)
                            {
                                break;
                            }
                            $post_ids['data'][] = $blockRet;
                        }
                    }
                }
            }
            if (FALSE == $isNewPost)
            {
                //是否是私聊
                if (isset($data['privateChat']) && !empty($data['privateChat']))
                {
                    list($blockRet, $error_message_tips) = $this->Block->privateChatModel($this->_userid, $data, $this->newPostList);
                    if(is_array($blockRet) && !empty($blockRet['id']))
                    {
                        $post_ids['data'][] = $blockRet;
                    }
                }
                else
                {
                    $blockRet = $this->Block->create($data, $this->newPostList);
                    if(is_array($blockRet) && !empty($blockRet['id']))
                    {
                        $post_ids['data'][] = $blockRet;
                    }
                }
            }
            if(FALSE == $blockRet OR !is_array($blockRet) OR empty($post_ids))
            {
                if (isset($error_message_tips) && !empty($error_message_tips))
                {
                    $ret =& $error_message_tips;
                }else{
                    $ret['status'] = FALSE;
                    $ret['errcode'] = 2;
                    $ret['errmsg'] = '操作失败，请重试';
                }
                BLH_Utilities::outputError($ret);
            }else{
                //$ret['status'] = true;
                //$ret['id'] = $id;
                $ret =& $post_ids;
                BLH_Utilities::outputSuccess($ret);
            }
        }
        //echo json_encode($ret);
    }
    /**
     * 发布消息/帖子-New
     * @param string $blockname
     * @param boolean $isReply
     */
    public function addposts($blockname)
    {
        $data = $this->input->post(NULL, true);
        $input_data = file_get_contents('php://input');
        //上传图片
        if ( isset($_FILES['userfile']) && !empty($_FILES['userfile']))
        {
            $this->ret_pics = $this->_do_upload();
        }
        //$ret = array();
        //$ret[] = array('id'=>1, 'unionId'=>2, 'unionName'=>'test社团');
        log_message('debug', 'METHOD=>'.__METHOD__.'@@classname=>'.__CLASS__.'@@$data=>'.var_export($data, TRUE).'@@$input_data=>'.var_export($input_data, TRUE).'@@FILES=>'.var_export($_FILES,TRUE).'@@$this->ret_pics=>'.var_export($this->ret_pics, TRUE));
        //发表帖子
        $this->add($blockname);
        exit;
    }
    private function _do_upload()
    {
        $this->_root= dirname(dirname(dirname(__FILE__)));
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
        return $ret;
    }
    public function del($blockname, $id)
    {
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockname);
        }
        $this->_setTableName($blockname);
        $ret = array('status'=>false);
        if($id && is_numeric($id))
        {
            $this->load->model('Block');
            $this->Block->init($this->_tableName);
            $ret['status'] = $this->Block->del($id, $this->_userid);
            $ret['id'] = $id;
        }
        echo json_encode($ret);
    }
    //获取某帖子的数据
    public function id($blockname, $id)
    {
        $ret = array('status'=>false, 'posts'=>array());
        if( $id && is_numeric($id))
        {
            $this->load->model('Userposts');
            if (!in_array($blockname, $this->Userposts->blockLists))
            {
                BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockname);
            }
            $this->load->model('Block');
            $this->_setTableName($blockname);
            $this->Block->init($this->_tableName);
            $ret['status'] = true;
            $ret['posts'] = $this->Block->fetchById($id, $this->_userid, TRUE, TRUE);
        }
        echo json_encode($ret);
    }
    public function detail($blockname, $rid ,$page=1, $pagesize=10)
    {
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockname);
        }
        $this->_setTableName($blockname);
        $ret = array('status'=>true);
        $page = intval($page);
        $pagesize=intval($pagesize);

        if($rid && $page>=1 && $pagesize>=1){
            $this->load->model('Block');
            $this->Block->init($this->_tableName);
            $ret['posts'] = $this->Block->detailof($rid, $page, $pagesize);

            $idArr = array();
            if (is_array($ret['posts']) && !empty($ret['posts']))
            {
                foreach($ret['posts'] as $postItem)
                {
                    if (isset($postItem['userid']))
                    {
                        $idArr[$postItem['userid']] = 1;
                    }
                }
            }
            $this->load->model('Userinfo');
            if(count($idArr) > 0)
            {
                $ret['users'] = array_values($this->Userinfo->batchUser($idArr));
            }
            $ret['status'] = true;
        }
        echo json_encode($ret);
    }
    function pids_check($pid=null)
    {
        $rootid = $this->input->post('rootid');
        $pid = $pid ? $pid : 0;
        $rootid = $rootid ? $rootid : $pid;
        $this->load->model('Block');
        $this->Block->init($this->_tableName);
        list($ret, $this->newPostList) = $this->Block->checkIds($pid, $rootid, TRUE);
        if(!$ret)
        {
            $this->form_validation->set_message('pids_check', 'invalid pid or rootid');
        }
        return $ret;
    }
    /**
     * 转到闲聊
     * @param $blockName    帖子类型
     * @param $blockId    帖子ID
     */
    public function copyPosts($blockName, $blockId = 0)
    {
        $this->load->model('Userposts');
        if (!in_array($blockname, $this->Userposts->blockLists))
        {
            $this->load->library('BLH_Utilities');
            BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockName);
        }
        $ret = array();
        $this->_setTableName($blockName);
        
        $this->load->model('Block');
        $this->Block->init($this->_tableName);
        $welcomePosts = $this->Block->fetchSimpleById($blockId);
        $welcomeCopyPost = array(
            'title' => $welcomePosts['title'],
            'content' => $welcomePosts['content'],
            'userid' => $this->_userid,
            'unionId' => 1,
        );
        $blockRet = $this->Block->create($welcomeCopyPost);
        if(FALSE == $blockRet OR !is_array($blockRet) OR !isset($blockRet['id']) OR $blockRet['id'] <= 0)
        {
            BLH_Utilities::outputError(-2, '转载失败，请重试');
        }
        $ret['data'][] = $blockRet;
        BLH_Utilities::outputSuccess($ret);
    }
}
