<?php
/**
 * 用户发布消息的数据模型
 *
 */
class Userposts extends CI_Model
{
    protected $_table = "userposts";
    public $_originTables = array('activityblockpost','communicationblockpost','houseblockpost','loveblockpost','recruitblockpost','secondhandblockpost','welcomeblockpost');
	public $blockLists = array('activity','communication','house','love','recruit','secondhand','welcome');
    
    public function total($id)
    {
        $this->db->where('userid', $id);
        $count = $this->db->count_all_results($this->_table);
        //echo $this->db->last_query();
        return $count;
    }
	/**
	 * 用户发帖列表
	 * @param int $id
	 * @param int $page
	 * @param int $pagesize
	 */
    public function itemlist($id, $page, $pagesize)
    {
        $this->db->where('userid', $id);
        $this->db->order_by('posttime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query= $this->db->get($this->_table);
        //echo $this->db->last_query();
        $result = $query->result_array();
        //根据userposts里的数据组成root的内容
		$this->process_block_root($result);
		return $result;
    }

    /**
     * 是否有新消息
     * @param int $userid
     * @param datetime/timestamp $fromTime
     */
    public function hasNew($userid, $fromTime)
    {
    	$count = 0;
        $viewIdList = $this->_postRootCare($userid);
        if(!empty($viewIdList['root']) OR !empty($viewIdList['posts']))
        {
        	//查询最新帖子的内容
        	if (isset($viewIdList['posts']) && !empty($viewIdList['posts']))
        	{
	        	$posts_viewIds = array_values($viewIdList['posts']);
        		$count = $this->fetch_sum_by_viewid($posts_viewIds, $userid, $fromTime);
        	}
        }
        return $count > 0;
    }

    /**
     * 我的消息列表
     * @param int $userid
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function mine($userid, $page = 1, $pagesize = 20, $timestamp = 0)
    {
        $viewIdList = $this->_postRootCare($userid);
        $ret = array('posts'=>array(), 'root'=>array());
        if(!empty($viewIdList['root']) OR !empty($viewIdList['posts']))
        {
        	//查询最新帖子的内容
        	if (isset($viewIdList['posts']) && !empty($viewIdList['posts']))
        	{
	        	$posts_viewIds = array_values($viewIdList['posts']);
        		$ret['posts'] = $this->fetch_data_by_viewid('posts', $posts_viewIds, $userid, $page, $pagesize);
        	}
            //查询root帖子的内容
            if (isset($viewIdList['root']) && !empty($viewIdList['root']))
            {
	        	$root_viewIds = array_values($viewIdList['root']);
        		$ret['root'] = $this->fetch_data_by_viewid('root', $root_viewIds, $userid, $page, $pagesize);
            }
        }
        return $ret;
    }

    /**
     * 先用userid=登录者，查到root帖，然后再查root帖的最新帖，如果这个最新帖是登录者回复的，就去掉
     * 如果不是登录者回复的，则把最新帖子放到列表里
     * @param $userid
     */
    protected function _postRootCare($userid)
    {
    	$this->db->select('viewId,block,id,pid,rootid,posttime,state,lastReplyTime,remark');
        $this->db->ar_where[] = "(`userid`='{$userid}' AND `block`!='welcome' AND `rootid`=0 AND `replycount` > 0)";
        $this->db->ar_where[] = " OR (`block`='welcome' AND `remark`='{$userid}')";
        $this->db->order_by('`posttime` DESC');
        $query = $this->db->get($this->_table);
        $data = $query->result();
        $ret = $rootWhere = array();
        if (!empty($data))
        {
	        foreach($data as $row)
	        {
	        	//查找最新回复的帖子信息
	            if (isset($row->remark) && ($row->block != 'welcome') && (int)$row->remark > 0)
	            {
	            	$ret['root'][$row->id] = $row->viewId;
	                $rootWhere[] = " (`id`='{$row->remark}' AND `block`='{$row->block}' AND `rootid`={$row->id}) ";
	            }else if (isset($row->block) && $row->block == 'welcome')
	            {
	            	$ret['posts'][$row->id] = $row->viewId;
	            }
	        }
	        if(count($rootWhere) > 0)
	        {
	            $wheresql = join('OR', $rootWhere);
	            $sql = "SELECT `viewId`,`id`,`rootid`,`userid` FROM `{$this->_table}` WHERE {$wheresql} ORDER BY `posttime` DESC"; 
	            $rQuery = $this->db->query($sql);
	            $rResult = $rQuery->result();
	            if (!empty($rResult))
	            {
		            foreach($rResult as $row)
		            {
		            	if (empty($row) OR !isset($row->userid))
		            	{
		            		continue;
		            	}
		            	//表示最新回复的帖子，是用户自己回复的，所以不能显示
		            	if (isset($row->userid) && $row->userid == $userid)
		            	{
		            		if (isset($ret['root'][$row->rootid])) unset($ret['root'][$row->rootid]);
		            		continue;
		            	}
		            	else
		            	{
		            		$ret['posts'][$row->id] = $row->viewId;
		            	}
		            }
	            }
	        }
        }
        return $ret;
    }
    /**
    * 获取我发的贴子或者我回过的帖子的viewId
    * 我发布的消息，但是必须是【被其他人回复过的】，需要查userid=我、rootid=0、lastReplyTime大于posttime的
    * 我回复过的消息，需要查userid=我、rootid != 0的
    */
    protected function _postRootCareBak($userid)
    {
        $this->db->select('viewId,block,id,rootid,posttime,state,replycount,lastReplyTime');
        //$query = $this->db->get_where($this->_table, array('userid'=>$userid));
        $this->db->ar_where[] = "(`userid`='{$userid}' AND `block`!='welcome')";
        $this->db->ar_where[] = " OR (`remark`='{$userid}' AND `block`='welcome')";
        $this->db->order_by('`posttime` DESC');
        $query = $this->db->get($this->_table);
        /*$sql = "SELECT `viewId`,`block`,`id`,`rootid`,`posttime`,`state`,`lastReplyTime`,`remark` 
        	FROM `{$this->_table}` WHERE (`userid`='{$userid}' AND `block`!='welcome') 
        	OR (`remark`='{$userid}' AND `block`='welcome')
        	ORDER BY `posttime` DESC"; 
        $query = $this->db->query($sql);*/
        $data = $query->result();
        //echo $this->db->last_query();
        $ret = array();

        $rootWhere = array();
        if (!empty($data))
        {
	        foreach($data as $row)
	        {
	        	//rootid == 0表示是我本人发布的帖子、replycount大于0的(lastReplyTime大于posttime的)表示被其他人回复过的
	            if ($row->rootid == 0 && $row->replycount > 0)
	            {
	                $ret[] = $row->viewId;
	            }
	            /*else if ($row->rootid > 0)
	            {
	            	//rootid != 0表示我回复的帖子
	                $rootWhere[] = " (`block`='{$row->block}' AND `rootid`={$row->rootid}) ";
	            }*/
	        }
        }

        if(count($rootWhere) > 0)
        {
            $wheresql = join('OR', $rootWhere);
            $sql = "SELECT `viewId` FROM `{$this->_table}` WHERE `userid`='{$userid}' AND ({$wheresql}) ORDER BY `posttime` DESC"; 
            $rQuery = $this->db->query($sql);
            //echo $this->db->last_query();
            $rResult = $rQuery->result();
            if (!empty($rResult))
            {
	            foreach($rResult as $row)
	            {
	                $ret[] = $row->viewId;
	            }
            }
        }
        return $ret;

    }
    
	/**
	 * 根据viewid获取相应的block内容
	 * @param array $result
	 */
    protected function fetch_data_by_viewid($tag = 'posts', &$viewIds, $userid, $page, $pagesize)
    {
    	$this->db->select('block,id,pid,rootid,title,content,imgUrl,userid,posttime,state,replycount,lastReplyTime,remark');
        $this->db->where_in('viewId', $viewIds);
        //$this->db->where('userid', $userid);
        //查询最新回复的帖子使用的条件
        if ($tag == 'posts')
        {
	        //$this->db->ar_where[] = " OR (`block`='welcome' AND `remark`='{$userid}')";
	        $this->db->limit($pagesize, ($page-1)*$pagesize);
	        $this->db->order_by('posttime DESC');
        }
        $query = $this->db->get($this->_table);
        $ret = $query->result_array();
        return $ret;
    }
	/**
	 * 根据viewid获取总的block内容
	 * @param array $result
	 */
    protected function fetch_sum_by_viewid(&$viewIds, $userid, $fromTime)
    {
    	$this->db->where_in('viewId', $viewIds);
        //$this->db->where('userid', $userid);
        //$this->db->ar_where[] = " OR (`block`='welcome' AND `remark`='{$userid}')";
        $this->db->where('posttime >=', $fromTime);
        $count = $this->db->count_all_results($this->_table);
        return $count;
    }
	/**
	 * 根据userposts里的数据组成root的内容
	 * @param array $result
	 */
    protected function process_block_root(&$result)
    {
    	if (is_array($result) && !empty($result))
    	{
    		$exists = $root_result = array();
    		foreach ($result as $post_key => $post_value)
    		{
    			if (isset($post_value['rootid']) && $post_value['rootid'] > 0)
    			{
    				if (isset($exists[$post_value['id']]))
    				{
    					continue;
    				}
    				$exists[$post_value['id']] = 1;
    				$root_result[] = array(
    					'id' => $post_value['id'],
		                'userid' => $post_value['userid'],
		                'pid' => $post_value['pid'],
		                'rootid' => $post_value['rootid'],
		                'title' => $post_value['title'],
		                'content' => $post_value['content'],
		                'posttime' => $post_value['posttime'],
		                'block' => $post_value['block'],
    				);
    			}
    		}
    		$result = array_merge($result, $root_result);
    	}
    }
}
