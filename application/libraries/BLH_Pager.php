<?php
class BLH_Pager{
    //每页的显示的条数
    var $pagesize;
    //总共的记录数
    var $count;
    //额外的参数
    var $parameter;
    /**
     * 每页显示条数配置
     * @var array
     */
    public static $pageDisplayConfig = array(
       'default_display_count' => 20,
       'system_display_config' => array(10,20,30,50,100),//,200,500,1000
    );
    /**
     * 构造函数
     * @param int $pagesize 每页的显示的条数
     * @param int $count  总共的记录数
     * @param string $parameter  额外的参数
     */
    function __construct($params=array(),$parameter=''){
        $pagesize = $params['pagesize'];
        $count = $params['count'];
        if(!in_array($pagesize, self::$pageDisplayConfig['system_display_config'])) $pagesize = self::$pageDisplayConfig['default_display_count'];
        $this->init($pagesize, $count,$parameter);
    }
    /**
     * 关键参数初始化
     * @param unknown_type $pagesize
     * @param unknown_type $count
     * @param unknown_type $parameter
     */
    public function init($pagesize,$count,$parameter=''){
        if ($pagesize==0){
            $pagesize=30;
        }
        $this->pagesize=$pagesize;
        if ($this->pagesize==NULL){
            $this->pagesize=0;
        }
        $this->count=$count;
        if ($this->count==NULL){
            $this->count=0;
        }
        $this->parameter=$parameter;
    }
    /**
     * 返回当前的url和参数
     * @return string $url 当前页的url
     */
    public function getUrl(){
        $url='';
        $url= $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        //分解这个残的url
        $parse = parse_url($url);
        //获取query的参数部分
        if(isset($parse['query'])){
            //抽出参数，组建成数组
            parse_str($parse['query'],$params);
            unset($params['page']);
            unset($params['pagenum']);
            unset($params['A']);
            $url= $parse['path'].'?'.http_build_query($params)."&";
        }else{
            $url='?';
        }
        return $url;
    }
    /**
     * 根据url判断出
     * @return int $page 获取当前的页码
     */
    public function getPage(){
        $page=0;
        $page = isset($_GET['page'])?ceil($_GET['page']):'';
        if ($page < 1){
            $page = 1;
        }
        return $page;
    }
    /**
     * 获取每页显示条数
     * @return int
     */
    public function getPageSize(){
        return (isset($_GET['pagenum']) && in_array($_GET['pagenum'],self::$pageDisplayConfig['system_display_config'])) ? ceil($_GET['pagenum']) : $this->pagesize;//self::$pageDisplayConfig['default_display_count'];
    }
    /**
     * 获取分页的参数，传递参数为true时，返回数组,否则sql limit语句,默认为返回limit语句。
     * @param bool $arr 是否返回limit sql语句
     * @return array/sql
     */
    public function getLimit($page=NULL,$arr=FALSE){
        if ($page==NULL){
            //获取当前页码
            $page=$this->getPage();
        }
        $this->pagesize = $this->getPageSize();
        //防止非数据库查询分页出错
        if ($this->pagesize>$this->count){
            $this->pagesize=$this->count;
        }
        $offset=($page-1)*$this->pagesize;
        //返回
        if($arr){
            return array("offset"=>$offset,"pagesize"=>$this->pagesize,$offset,$this->pagesize);
        }else{
            return " limit $offset,$this->pagesize";
        }
    }
    /**
     * 主要是返回html的分页值
     * @return string $pagestr  显示分页的html
     */
    public function show(){
        //$LangReplace = Models_Common_Translate::factory();
        //获取当前url
        $url=$this->getUrl();
        $pagesize=$this->getPageSize();//$this->pagesize;
        $count=$this->count;
        $page=$this->getPage();
        if ($page == ""){
            $page = 1;
        }
        if($count == 0) {
            $pagestr="<font color='red'>没有搜索到符合的记录！</font>";
            return $pagestr;
        }
        $pagenum = ceil($count/$pagesize);
        $pagestr="总计<font color=\"#FF0000\">".$count."</font>记录&nbsp;&nbsp;第<font color=\"#FF0000\">$page</font>/".$pagenum."页&nbsp;&nbsp;";
        if($page==1){
            $pagestr.="首页&nbsp;&nbsp;"."上一页&nbsp;&nbsp;";
        }else{
            $pagestr.="<a href=\"".$url."\">"."首页</a>&nbsp;&nbsp;<a href=\"".$url."page=".($page-1)."&pagenum=".$pagesize."\">"."上一页</a>&nbsp;&nbsp;";
        }
        if($page<$pagenum){
            $pagestr.="<a href=\"".$url."page=".($page+1)."&pagenum=".$pagesize."\">"."下一页</a>&nbsp;&nbsp;<a href=\"".$url."page=".$pagenum."&pagenum=".$pagesize."\">"."尾页</a>";
        }
        else{
            $pagestr.="下一页&nbsp;&nbsp;".'尾页';
        }
        $pagestr.="&nbsp;&nbsp;<select name=\"pageno\" id=\"pageno\" onchange=\"javascript:location='".$url."page='+this.value\">";
        $pages=$this->getLookPages($page,$pagenum);
        foreach ($pages as $i){
            $pagestr.="<option value=\"$i\" ";
            if ($page == $i){
                $pagestr .= "selected=\"selected\"";
            }
            $pagestr .= ">$i</option>";
        }
        $pagestr.="</select>";
        $pagestr .= "&nbsp;每页显示数<select name=\"pagenum\" id=\"pagenum\" onchange=\"javascript:location='".$url."pagenum='+this.value\">";
        foreach (self::$pageDisplayConfig['system_display_config'] as $pagenum) {
            $pagestr.="<option value=\"$pagenum\" ";
            if ($pagesize == $pagenum){
                $pagestr .= "selected=\"selected\"";
            }
            $pagestr .= ">$pagenum</option>";
        }
        return $pagestr;
    }
    /**
     * 获取要显示的页码列表
     * @param 当前页码   $pageNow
     * @param 总页码   $nbTotalPage
     * @param 全部显示的上限   $showAll
     * @param 最前面要留的页码数  $sliceStart
     * @param 最后面要留得页码数   $sliceEnd
     * @param 按比例显示   $percent
     * @param 当前页的范围    $range
     * @return 页码数据列表
     */
    private function getLookPages($pageNow=1,$nbTotalPage=1,$showAll=200,$sliceStart=5,$sliceEnd=5,$percent=20,$range=10){
        if ($nbTotalPage < $showAll) {
            $pages = range(1, $nbTotalPage);
        } else {
            $pages = array();
            // Always show first X pages
            for ($i = 1; $i <= $sliceStart; $i++) {
                $pages[] = $i;
            }
            // Always show last X pages
            for ($i = $nbTotalPage - $sliceEnd; $i <= $nbTotalPage; $i++) {
                $pages[] = $i;
            }
            $i = $sliceStart;
            $x = $nbTotalPage - $sliceEnd;
            $met_boundary = false;
            while ($i <= $x) {
                if ($i >= ($pageNow - $range) && $i <= ($pageNow + $range)){
                    //页码周围要密集    前range个和后range个都要有
                    $i++;
                    $met_boundary = true;
                }else{
                    //不在双range之间
                    $i = $i+floor($nbTotalPage/$percent);
                    if ($i > ($pageNow - $range) && !$met_boundary){
                        $i = $pageNow - $range;
                    }
                }
                if ($i > 0 && $i <= $x){
                    $pages[] = $i;
                }
            }
            //排序去重
            sort($pages);
            $pages = array_unique($pages);
        }
        return $pages;
    }
}
?>