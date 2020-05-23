<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of weibotauth
 *
 * @author suncjs
 */

define("COMMENT_NONE", 0);
define("COMMENT_CURRENT", 1);
define("COMMENT_ORIGINAL", 2);
define("GROUPS_PUBLIC", 'public');
define("GROUPS_PRIVATE", 'private');
define("GROUPS_ALL", 'all');

class WeiboTAuth {

    private $token;
    private $host = "http://api.weibo.com";//"http://i2.api.weibo.com";
    private static $redisr = NULL;
    private static $redisw = NULL;

    public function __construct($atoken) {
        $this->token = $atoken;
    }
    /**
     *  get redis read
     * @return Redis
     */
    protected static function getRedisReader() {
        /*$redisr = new Redis();
        $redisr->open(REDIS_HOST_R, REDIS_PORT_R);
        return $redisr;*/
        return Utilities::getCacheReader('redis');
    }
    /**
     * get redis writer
     * @return Redis
     */
    protected static function getRedisWriter() {
        /*$redisr = new Redis();
        $redisr->open(REDIS_HOST_W, REDIS_PORT_W);
        return $redisr;*/
        return Utilities::getCacheWriter('redis');
    }
    /**
     * 计算授权标识串
     */
    private function calcAuthorizeString($uid) {
        $authString = $uid . ":" . md5($uid . $this->token);
        return sprintf("Token %s", base64_encode($authString));
    }

    /**
     * 执行服务请求
     * @param type $uid
     * @param type $url
     * @param type $paramsArray
     * @param type $post
     * @param type $returndirectly
     * @return type
     */
    private function request($uid, $url, $paramsArray, $post = FALSE, $returndirectly = FALSE) {
        $ch = curl_init();
        if (!$ch) {
            //Logger::writeLog('CURL_ERROR_INIT', sprintf("curl init fail with url: %s", $url));
            //BLH_Utilities::writeLog('CURL_ERROR_INIT | '.sprintf("curl init fail with url: %s", $url), 'ab+');
            return NULL;
        }
        foreach ($paramsArray as $k => $v) {
            $paramsArray[$k] = sprintf("%s=%s", $k, urlencode($v));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $authorizationValue = $this->calcAuthorizeString($uid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: " . $authorizationValue));
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            $data = implode("&", $paramsArray);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $data = implode("&", $paramsArray);
            $url = sprintf("%s?%s", $url, $data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            /*$ret = NULL;
            $clientIp = isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'';
            $serverIp =  isset($_SERVER["SERVER_ADDR"])?$_SERVER["SERVER_ADDR"]:'';
            $remoteIp = gethostbyname("i2.api.weibo.com");
            $msg = sprintf("uid:%s,url:%s,type=%s,service communication fail, code:%d, AuthToken:%s, Token:%s, message:%s, clientip=%s, serverip=%s, remoteip=%s",$uid, $url,($post?'post_content='.$data:'get_content='.$data),curl_errno($ch), $authorizationValue, $this->token, curl_error($ch),$clientIp,$serverIp,$remoteIp);
            $msg = 'SNG|weibotauth|WBAPI|ERROR|CURL|'.$msg;
            monitor::push('SNG_WBAPI_ERROR_CURL',$msg);*/
        }
        curl_close($ch);
        if ($returndirectly) {
            return $json;
        } else {
            $arr = json_decode($json, TRUE);
            if (($arr === NULL) || array_key_exists('error_code', $arr)) {
                //Logger::writeLog('CURL_ERROR_JSON', 'CLASS:'.__METHOD__.' URL:'.$url.' JSON:'.$json . " UID:" . $uid);
                //BLH_Utilities::writeLog('CURL_ERROR_DATA_ERROR | '.'CLASS:'.__METHOD__.' URL:'.$url.' JSON:'.$json . " UID:" . $uid . ' AuthToken:'.$authorizationValue . ' Token:'.$this->token, 'ab+');
                return $arr;
            } else {
                return $arr;
            }
        }
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo1($uid, $redirect = TRUE) {
        $url = $this->host . '/2/users/show.json';
        $paramsArray = array("source" => APP_KEY, "uid" => $uid);
        $ret = $this->request($uid, $url, $paramsArray);
        if (!$ret && $redirect) {
            $ret = $this->request($uid, $url, $paramsArray, FALSE, TRUE);
            $ret = json_decode($ret, TRUE);
            if ($ret["error_code"] == 20003) {
                $gsid = $_COOKIE[LoginUtils::$gsid_cookie_name];
                header("Location: http://weibo.cn/guide/?vt=4&wm=3346_0001&gsid=" . $gsid . "&wm=3346_0001");
                exit;
            }
        }
        return $ret;
    }
    /**
     * SSO获取用户信息
     */
    public function getUserInfo($uid, $redirect = TRUE) {
        $url = $this->host . "/2/users/show.json";
        $paramsArray = array("source" => APP_KEY, "uid" => $uid);
        $ret = $this->request($uid, $url, $paramsArray);
        if (!$ret && $redirect) {
            $ret = $this->request($uid, $url, $paramsArray, FALSE, TRUE);
            $ret = json_decode($ret, TRUE);
        }
        return $ret;
    }
    /**
     * 批量获取用户信息
     * @param type $uidstr
     * @return type
     */
    public function getBatchUserInfo($uid, $uidstr) {
        $uidstr = array_slice($uidstr, 0, 20);
        $uidstr = implode(',', $uidstr);
        $url = $this->host . '/2/users/show_batch.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'uids' => $uidstr
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }
    /**
     * 批量获取用户信息
     * @param array $uids 用户ID列表,以逗号隔开
     * @param int $page 当前页数,默认1
     * @param int $pagesize 每页显示数,默认20
     * @see http://open.weibo.com/wiki/2/users/show_batch
     * @return uid为键值的所有用户的信息数组
     */
    function getShowBatch($uid, $friends, $page=1, $pagesize=20)
    {
        if (!$friends || !is_array($friends))
        {
            return array();
        }
        $friends = array_unique($friends);
        $friends_count = count($friends);
        $friends_all_info = array();
        $totalPage = ceil($friends_count / $pagesize);
        do
        {
            $friends_loop = ($friends_count <= $pagesize) ? $friends : array_slice($friends, ($page-1)*$pagesize, $pagesize);
            $friends_all_list = $this->getBatchUserInfo($uid, $friends_loop);
            if (isset($friends_all_list['users']) && is_array($friends_all_list['users']) && !empty($friends_all_list['users']))
            {
                foreach($friends_all_list['users'] as $user_item)
                {
                    $friends_all_info[$user_item['id']] = $user_item;
                }
            }
            ++ $page;
        }
        while ($page <= $totalPage);
        
        return $friends_all_info;
    }

    /**
     * 获取好友列表
     * @param type $uid
     * @param type $page
     * @param type $psize
     * @return type
     */
    public function getFriends($uid, $page, $psize) {
        $url = $this->host . "/2/friendships/friends/bilateral.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "page" => $page,
            "count" => $psize
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     *
     * @param type $uid
     * @param type $page
     * @param type $psize
     * @return type
     */
    public function getFriendIds($uid, $page, $psize) {
        $url = $this->host . "/2/friendships/friends/bilateral/ids.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "page" => $page,
            "count" => $psize
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取全部好友
     * @param type $uid
     * @return type
     */
    /* public function getAllFriends($uid) {
      $page = 1;
      $pagesize = 50;
      $fs = $this->getFriends($uid, $page, $pagesize);
      $count = $fs["total_number"];
      while (count($fs["users"]) < $count) {
      $page += 1;
      $friends = $this->getFriends($uid, $page, $pagesize);
      if (count($friends["users"]) == 0) {
      break;
      }
      $fs["users"] = array_merge($friends["users"], $fs["users"]);
      }
      return $fs;
      } */

    /**
     *
     * @param type $uid
     * @return type
     */
    public function getAllFriendIds($uid) {
        $page = 1;
        $pagesize = 2000;
        $fs = $this->getFriendIds($uid, $page, $pagesize);
        $count = $fs["total_number"];
        while (count($fs["ids"]) < $count) {
            $page += 1;
            $friends = $this->getFriendIds($uid, $page, $pagesize);
            if (count($friends["ids"]) == 0) {
                break;
            }
            $fs["ids"] = array_merge($friends["ids"], $fs["ids"]);
        }
        return $fs;
    }

    /**
     * 获取全部关注人id
     * @param type $uid
     * @return type
     */
    public function getAllFollowingIds($uid) {
        $pagesize = 5000;
        $fs = $this->getFollowingIds($uid, $pagesize);
        $nextcursor = $fs["next_cursor"];
        while ($nextcursor != 0) {
            $friends = $this->getFollowingIds($uid, $pagesize, $nextcursor);
            $nextcursor = $friends["next_cursor"];
            $fs["ids"] = array_merge($friends["ids"], $fs["ids"]);
        }

        return $fs;
    }

    /**
     * 获取全部fans id
     * @param type $uid
     * @return type
     */
    public function getAllFollowerIds($uid) {
        $pagesize = 5000;
        $fs = $this->getFollowerIds($uid, $pagesize);
        $nextcursor = $fs["next_cursor"];
        while ($nextcursor != 0) {
            $friends = $this->getFollowingIds($uid, $pagesize, $nextcursor);
            $nextcursor = $friends["next_cursor"];
            $fs["ids"] = array_merge($friends["ids"], $fs["ids"]);
        }

        return $fs;
    }

    /**
     * just to retrieve exactly same friends count with PC version, nonsense design!
     * @param type $uid
     */
    public function getFriendCount($uid) {
        $url = $this->host . "/2/friendships/friends/bilateral/ids.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret["total_number"];
    }

    /**
     * 获取推荐好友
     * @param type $uid
     * @param string $url
     * @param type $paramsArray
     * @return type
     */
    public function getInterested($uid, $page, $psize) {
        $url = $this->host . "/2/suggestions/users/may_interested.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "page" => $page,
            "count" => $psize
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }
    /**
     * 获取系统推荐的热门用户列表
     * http://open.weibo.com/wiki/2/suggestions/users/hot
     * @param type $uid
     * @param string $category
     * @return array
     * 推荐分类，返回某一类别的推荐用户，默认为default，如果不在以下分类中，返回空列表，
     * default：人气关注、ent：影视名星、music：音乐、sports：体育、fashion：时尚、art：艺术、
     * cartoon：动漫、games：游戏、trip：旅行、food：美食、health：健康、literature：文学、
     * stock：炒股、business：商界、tech：科技、house：房产、auto：汽车、fate：命理、govern：政府、
     * medium：媒体、marketer：营销专家。
     */
    public function getSuggestUsersHot($uid, $category='default') {
        $url = $this->host . "/2/suggestions/users/hot.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "category" => $category
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取粉丝列表
     * @param type $uid
     * @param type $count
     * @param int cursor
     * @return type
     */
    public function getFollowerIds($uid, $count, $cursor = -1) {
        $url = $this->host . "/2/friendships/followers/ids.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "cursor" => $cursor,
            "count" => $count
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取关注列表
     * @param type $uid
     * @param type $count
     * @param type $cursor
     * @return type
     */
    public function getFollowingIds($uid, $count, $cursor = -1) {
        $url = $this->host . "/2/friendships/friends/ids.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "cursor" => $cursor,
            "count" => $count,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取两个用户关系
     * @param type $source_id
     * @param type $target_id
     * @return type
     */
    public function getFriendship($uid, $source_id, $target_id) {
        $url = $this->host . "/2/friendships/show.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "source_id" => $source_id,
            "target_id" => $target_id,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 关注
     * @param type $uid
     * @param type $targetid
     */
    public function follow($uid, $targetid) {
        $url = $this->host . "/2/friendships/create.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $targetid,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * 取消关注
     * @param type $uid
     * @param type $targetid
     */
    public function unFollow($uid, $targetid) {
        $url = $this->host . "/2/friendships/destroy.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $targetid,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }
    /**
     * 获取用户版本
     * @param type $uid
     * @return type
     */
    public function getUserVersion($uid) {
        $url = $this->host . "/2/users/get_version.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取我发的评论
     * @param type $uid
     * @param type $page
     * @param type $psize
     * @return type
     */
    public function getCommentsByMe($uid, $page, $psize) {
        $url = $this->host . "/2/comments/by_me.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "page" => $page,
            "count" => $psize,
            "filter_by_source" => 1
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取对我的评论
     * @param type $uid
     * @param type $page
     * @param type $psize
     * @return type
     */
    public function getCommentsToMe($uid, $page, $psize) {
        $url = $this->host . "/2/comments/to_me.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "page" => $page,
            "count" => $psize,
            "filter_by_source" => 1
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取好友动态
     * @param type $uid
     * @param type $page
     * @param type $psize
     * @param type $sinceid
     * @return type
     */
    public function getFriendsTimeline($uid, $page, $psize, $sinceid = 0, $baseapp = 1) {
        $url = $this->host . "/2/statuses/friends_timeline.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "page" => $page,
            "count" => $psize,
            "base_app" => $baseapp,
            "since_id" => $sinceid,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取应用feed
     * @param type $uid
     * @param type $appid
     * @param type $page
     * @param type $psize
     * @param type $sinceid
     * @return type
     */
    public function getAppTimeline($uid, $appid, $page, $psize, $sinceid = 0) {
        $url = $this->host . "/2/statuses/public_timeline.json";
        $paramsArray = array(
            "source" => $appid,
            "appkey" => $appid,
            "page" => $page,
            "count" => $psize,
            "base_app" => 1,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 获取单条微博消息
     * @param type $uid
     * @param type $id
     */
    public function getWeiboItem($uid, $id) {
        $url = $this->host . "/2/statuses/show.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "id" => $id,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 发布评论
     * @param type $uid
     * @param type $commenttext
     * @param type $feedid
     * @return type
     */
    public function comment($uid, $commenttext, $feedid, $retwit = FALSE) {
        $url = $this->host . "/2/comments/create.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "comment" => $commenttext,
            "id" => $feedid
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        if (($ret != NULL) && $retwit) {
            $this->rePost($uid, $commenttext, $feedid);
        }
        return $ret;
    }

    /**
     * 发微博
     * @param type $uid
     * @param type $commenttext
     * @return type
     */
    public function publishTwit($uid, $commenttext, $baseapp = 0, $appid = APP_KEY) {
        $url = $this->host . "/2/statuses/update.json";
        $paramsArray = array(
            "source" => $appid,
            "status" => $commenttext,
            "base_app" => $baseapp,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    public function publishTwitWithPic($uid, $commenttext, $picurl, $appid = APP_KEY) {
        $url = $this->host . "/2/statuses/upload_url_text.json";
        $paramsArray = array(
            "source" => $appid,
            "status" => $commenttext,
            "url" => $picurl,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * 获取feed评论列表
     * @param type $uid
     * @param type $feedid
     * @param type $page
     * @param type $count
     * @return type
     */
    public function getCommentsOfFeed($uid, $feedid, $page, $count, $maxid = "0") {
        $url = $this->host . "/2/comments/show.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "id" => $feedid,
            "page" => $page,
            "count" => $count,
            "max_id" => $maxid,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 通过关键词搜索用户
     * http://open.weibo.com/wiki/2/search/users
     * @param type $uid
     * @param type $q 搜索的关键字，必须进行URLencode。
     * @param type $snick 搜索范围是否包含昵称，0：不包含、1：包含。
     * @param type $stag 搜索范围是否包含标签，0：不包含、1：包含。 
     * @param type $page
     * @param type $count
     * @return type
     */
    public function searchUser($uid, $q, $snick = TRUE, $stag = TRUE, $comorsch = TRUE, $page = 1, $count = 20) {
        $url = $this->host . "/2/search/users.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "q" => urlencode($q),
            "snick" => $snick ? 1 : 0,
            'stag' => $stag ? 1 : 0,
            'comorsch' => $comorsch ? 1 : 0,
            'sid' => 'tech',//'m_game',
            "page" => $page,
            "count" => $count,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }
    /**
     * 搜索公司时的联想搜索建议
     * http://open.weibo.com/wiki/2/search/suggestions/companies
     * @param type $uid
     * @param string $q 搜索的关键字，必须做URLencoding。
     */
    public function getSearchSuggestCompany($uid, $q, $count = 100) {
        $url = $this->host . "/2/search/suggestions/companies.json";
        $paramsArray = array(
            'source' => APP_KEY,
            'uid' => $uid,
            'q' => urlencode($q),
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }
    /**
     * 返回指定用户的标签列表
     *
     * 对应API：{@link http://open.weibo.com/wiki/2/tags tags}
     * 
     * @param int $uid 查询用户的ID。默认为当前用户。可选。
     * @param int $page 指定返回结果的页码。可选。
     * @param int $count 单页大小。缺省值20，最大值200。可选。
     * @return array
     */
    function get_tags( $uid = NULL, $page = 1, $count = 20 )
    {
        $url = $this->host . '/2/tags.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'uid' => $uid,
            'page' => $page,
            'count' => $count
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }
    /**
     *
     * @param int64 $uid
     * @param string $keyword
     * @param int $count
     * @return type
     */
    public function searchKeyWord($uid, $keyword, $page = 1, $count = 10) {
        $url = "http://i2.api.weibo.com/2/search/statuses.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "q" => $keyword,
            "sid" => "m_game",
            "page" => $page,
            "count" => $count,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }

    /**
     * 获取sendinvite需要的gameid
     * @param type $uid
     * @param type $appid
     * @return type
     */
    public function getGameIdStr($uid, $appid) {
        $url = "http://api.t.sina.com.cn/invite/get_app_parse.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "appkeys" => $appid,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 发送邀请
     * @param type $uid
     * @param type $appid
     * @param type $inviteearray
     * @return type
     */
    public function sendInvitation($uid, $invitees, $content, $platform = HTML5_GAME) {
        $to_uids = is_array($invitees) ? implode(",", $invitees) : $invitees;
        $url = $this->host . "/2/invitation/send.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "type" => "game",
            "uids" => $to_uids,
            "content" => $content,
            "platforms" => $platform,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * 获取邀请列表
     * @param type $uid
     * @param type $appid
     * @param type $page
     * @param type $count
     */
    public function getInvitations($uid, $appid, $page, $count, $platform = HTML5_GAME) {
        $paramsArray = array(
            "source" => $appid,
            "type" => "game",
            "page" => $page,
            "count" => $count,
            "platform" => $platform,
        );
        $url = "http://api.t.sina.com.cn/invite/get_receive_list.json";
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     * 发送私信
     * @param type $uid
     * @param type $target
     * @param type $text
     * @return type
     */
    public function sendMessage($uid, $target, $text) {
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $target,
            "text" => $text,
        );
        $url = $this->host . "/2/direct_messages/new.json";
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * send notification, 100 uids once at most
     * @param type $uid
     * @param type $appid
     * @param type $uidstr
     * @param type $title
     * @param type $content
     * @return type
     */
    public function sendNotification($uid, $appid, $uidstr, $title, $content) {
        $paramsArray = array(
            "source" => $appid,
            "uids" => $uidstr,
            "title" => $title,
            "content" => $content,
        );
        $url = "http://api.t.sina.com.cn/notice/app/send.json";
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * 发送通知
     * @param $uid
     * @param $paramsArray
     */
    public function sendNotice($uid, $paramsArray) {
        $url = $this->host . "/2/notification/send.json";
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * 查询微币余额
     * @param type $uid
     * @return type
     */
    public function queryWeiMoneyBalance($uid) {
        $url = "http://i.pay.api.weibo.com/ipay/s/get_balance.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "user_id" => $uid
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret == NULL ? 0 : $ret["balance"];
        // 微币余额暂时不用
        //return 0;
    }

    /**
     * get weibos published by the user
     * @param int64 $uid
     * @param int $page
     * @param int $count
     * @return array
     */
    public function getUserTimeLine($uid, $page = 1, $count = 20) {
        $url = "http://i2.api.weibo.com/2/statuses/user_timeline.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
            "trim_user" => 0,
            "page" => $page,
            "count" => $count,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }

    /**
     * 转发一条微博信息。
     * @param int64 $uid
     * @param string $text do NOT url encode here
     * @param int64 $weiboid
     * @param bitflag $iscomment
     * @return array
     * @example
     * rePost(1, "abc", 123, COMMENT_NONE)
     * rePost(1, "abc", 123, COMMENT_CURRENT)
     * rePost(1, "abc", 123, COMMENT_ORIGINAL)
     * rePost(1, "abc", 123, COMMENT_CURRENT + COMMENT_ORIGINAL);
     */
    public function rePost($uid, $text, $weiboid, $comment = COMMENT_NONE,$returndirectly = FALSE) {
        $url = "http://i2.api.weibo.com/2/statuses/repost.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "status" => $text,
            "id" => $weiboid,
            "is_comment" => $comment,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE,$returndirectly);
        return $ret;
    }

    /**
     * 添加收藏
     * @param int64 $uid
     * @param int64 $weiboid
     * @return array
     */
    public function createFavourite($uid, $weiboid) {
        $url = "http://i2.api.weibo.com/2/favorites/create.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "id" => $weiboid,
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     * 获取用户加入的微群列表。
     * @param int64 $uid
     * @return array
     */
    public function getUserGroups($uid) {
        $url = "http://i2.api.weibo.com/2/groups/joined.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "uid" => $uid,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }

    /**
     * 发布一条群微博信息。
     * @param int64 $uid
     * @param int64 $groupid
     * @param string $text do NOT url encode it
     * @param int64 $weiboid
     * @return array
     */
    public function sendMsg2Group($uid, $groupid, $text, $weiboid) {
        $url = "http://i2.api.weibo.com/2/groups/statuses/update.json";
        $paramsArray = array(
            "source" => APP_KEY,
            "gid" => $groupid,
            "status_id" => $weiboid,
            "status" => urlencode($text),
        );
        $ret = $this->request($uid, $url, $paramsArray, TRUE);
        return $ret;
    }

    /**
     *  获取按最近联系排序的粉丝列表。
     */
    public function sortfollowers($uid, $page, $count = 50) {
        $url = $this->host . '/2/friendships/followers/sort_interactive.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'uid' => $uid,
            'count' => $count,
            'page' => $page,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    /**
     *  获取按最近联系排序的粉丝列表的id
     */
    public function sortFollowersIds($uid, $page, $count = 50) {
        $url = $this->host . '/2/friendships/followers/sort_interactive/ids.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'uid' => $uid,
            'count' => $count,
            'page' => $page,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }

    /**
     *  获取当前登陆用户好友分组列表
     */
    public function getGroupListOfFriends($uid, $mode = GROUPS_ALL) {
        $url = $this->host . '/2/friendships/groups.json';
        $paramsArray = array(
            "source" => APP_KEY,
            "mode" => $mode,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    /**
     *  获取某一好友分组下的成员列表
     */
    public function getUsersByGroup($uid, $list_id, $cursor = 0, $count = 200) {
        $url = $this->host . '/2/friendships/groups/members.json';
        $paramsArray = array(
            "source" => APP_KEY,
            "cursor" => $cursor,
            "list_id" => $list_id,
            "count" => $count,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    /**
     *  批量获取好友分组（目前只能为当前登录用户的分组）的详细信息
     */
    public function getUsersByGroupShowBatch($uid, $uids, $list_ids) {
        $url = $this->host . '/2/friendships/groups/show_batch.json';
        $paramsArray = array(
            "source" => APP_KEY,
            "list_ids" => $list_ids,
            "uids" => $uids,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    //批量获取指定的多个分组成员列-解除微博每次5个list_id的限制
    public function getUsersByGroupMembersShowBatchBackend($uid, $list_ids, $count = 0, $page=1, $pagesize=5)
    {
        if (!$list_ids || !is_array($list_ids))
        {
            return array();
        }
        $list_ids = array_unique($list_ids);
        $list_count = count($list_ids);
        $list_all_info = array();
        $totalPage = ceil($list_count / $pagesize);
        do
        {
            $list_loop = ($list_count <= $pagesize) ? $list_ids : array_slice($list_ids, ($page-1)*$pagesize, $pagesize);
            $list_all_list = $this->getUsersByGroupMembersShowBatch($uid, $list_loop, $count);
            if (isset($list_all_list['result']) && is_array($list_all_list['result']) && !empty($list_all_list['result']))
            {
                foreach($list_all_list['result'] as $list_key => $list_item)
                {
                    $list_all_info['result'][$list_key] = $list_item;
                }
            }
            ++ $page;
        }
        while ($page <= $totalPage);
        
        return $list_all_info;
    }
    /**
     *  批量获取指定的多个分组成员列
     *  http://wiki.intra.weibo.com/2/friendships/groups/members/show_batch
     */
    public function getUsersByGroupMembersShowBatch($uid, $list_ids, $count = 0) {
    	$list_ids = join(',', $list_ids);
        $url = $this->host . '/2/friendships/groups/members/show_batch.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'list_ids' => $list_ids,
            'sort' => 1,
        );
        if ($count > 0)
        {
        	$paramsArray['count'] = $count;
        }
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    /**
     *  批量获取指定用户作为成员的指定用户的好友分组信息
     */
    public function getUsersByGroupListed($uid, $uids) {
        $url = $this->host . '/2/friendships/groups/listed.json';
        $paramsArray = array(
            "source" => APP_KEY,
            "owner_uid" => $uid,
            "uids" => $uids,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }

    /**
     * get repost ids of a tweet
     * @param bigint $uid
     * @param bigint $postid
     * @param int $page
     * @param int $count
     * @return array
     */
    public function getRepostIds($uid, $postid, $page = 1, $count = 10) {
        $url = $this->host . '/2/statuses/repost_timeline/ids.json';
        $paramsArray = array(
            "source" => APP_KEY,
            "id" => $postid,
            "count" => $count,
            "page" => $page,
        );
        $ret = $this->request($uid, $url, $paramsArray);
        return $ret;
    }

    /**
     *  获取按最近联系排序的关注列表
     *  返回给定用户所关注人的排序，排序依据当前用户与其关注人之间的互动频率
     *  $status:user中的status信息开关， 
     *      1：user中的status字段仅返回status_id， 
     *      0：返回完整status信息。默认trim_status为1。
     */
    public function sortFriends($uid, $page = 1, $count = 200, $status = 1) {
        $url = $this->host . '/2/friendships/friends/sort_interactive.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'uid' => $uid,
            'count' => $count,
            'page' => $page,
            'trim_status' => $status,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    /**
     *  获取按最近联系排序的关注列表的id
     *  返回给定用户所关注人的排序的id，排序依据当前用户与其关注人之间的互动频率
     */
    public function sortFriendsIds($uid, $page = 1, $count = 200) {
        $url = $this->host . '/2/friendships/friends/sort_interactive/ids.json';
        $paramsArray = array(
            'source' => APP_KEY,
            'uid' => $uid,
            'count' => $count,
            'page' => $page,
        );
        $ret = $this->request($uid, $url, $paramsArray, FALSE);
        return $ret;
    }
    /**
     * @ignore
     */
    protected function id_format(&$id) {
        if ( is_float($id) ) {
            $id = number_format($id, 0, '', '');
        } elseif ( is_string($id) ) {
            $id = trim($id);
        }
    }
    /**
     * 获取用户基本信息
     *
     * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/basic account/profile/basic}
     *
     * @param int $uid  需要获取基本信息的用户UID，默认为当前登录用户。
     * @return array
     */
    function account_profile_basic( $uid = NULL  )
    {
        $url = $this->host . '/2/account/profile/basic.json';
        $params = array();
        $params['source'] = APP_KEY;
        if ($uid) {
            $this->id_format($uid);
            $params['uid'] = $uid;
        }
        $ret = $this->request($uid, $url, $params, FALSE);
        return $ret;
    }

    /**
     * 获取用户的教育信息
     *
     * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/education account/profile/education}
     *
     * @param int $uid  需要获取教育信息的用户UID，默认为当前登录用户。
     * @return array
     */
    function account_education( $uid = NULL )
    {
        $url = $this->host . '/2/account/profile/education.json';
        $params = array();
        $params['source'] = APP_KEY;
        if ($uid) {
            $this->id_format($uid);
            $params['uid'] = $uid;
        }
        $ret = $this->request($uid, $url, $params, FALSE);
        return $ret;
    }
    /**
     * 获取用户的职业信息
     *
     * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/career account/profile/career}
     *
     * @param int $uid  需要获取教育信息的用户UID，默认为当前登录用户。
     * @return array
     */
    function account_career( $uid = NULL )
    {
        $url = $this->host . '/2/account/profile/career.json';
        $params = array();
        $params['source'] = APP_KEY;
        if ($uid) {
            $params['uid'] = $uid;
        }
        $ret = $this->request($uid, $url, $params, FALSE);
        return $ret;
    }
    /**
     * 获取所有的学校列表
     *
     * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/school_list account/profile/school_list}
     *
     * @param array $query 搜索选项。格式：array('key0'=>'value0', 'key1'=>'value1', ....)。支持的key:
     *  - province  int     省份范围，省份ID。
     *  - city      int     城市范围，城市ID。
     *  - area      int     区域范围，区ID。
     *  - type      int     学校类型，1：大学、2：高中、3：中专技校、4：初中、5：小学，默认为1。
     *  - capital   string  学校首字母，默认为A。
     *  - keyword   string  学校名称关键字。
     *  - count     int     返回的记录条数，默认为10。
     * 参数keyword与capital二者必选其一，且只能选其一。按首字母capital查询时，必须提供province参数。
     * @access public
     * @return array
     */
    function school_list( $query, $uid = NULL )
    {
        $url = $this->host . '/2/account/profile/school_list.json';
        $params = array();
        $params = $query;
        $params['source'] = APP_KEY;
        if ($uid) {
            $params['uid'] = $uid;
        }
        $ret = $this->request($uid, $url, $params, FALSE);
        return $ret;
    }

    /**
     * 获取当前登录用户的API访问频率限制情况
     *
     * 对应API：{@link http://open.weibo.com/wiki/2/account/rate_limit_status account/rate_limit_status}
     * 
     * @access public
     * @return array
     */
    function rate_limit_status( $uid = NULL )
    {
        $url = $this->host . '/2/account/rate_limit_status.json';
        $params = array();
        $params['source'] = APP_KEY;
        if ($uid) {
            $params['uid'] = $uid;
        }
        $ret = $this->request($uid, $url, $params, FALSE);
        return $ret;
    }
}