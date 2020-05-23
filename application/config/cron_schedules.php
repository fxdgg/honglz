<?php
//示例
//$cron_schedule['clear_log'] = array(
//    'schedule' => array(
//        'config_path' => '', // cron表达式的标识 用于在配置文件或数据库中获取表达式 直接指定时为空
//        'cron_expr' => '*/5 * * * *' // 直接指定cron表达式 在配置文件或数据库中获取表达式为空
//    ),
//    'run' => array(
//        'filepath' => 'cron', // 文件所在的目录 相对于APPPATH
//        'filename' => 'Myclass.php', // 文件名
//        'class' => 'MyClass', // 类名 如果只是简单函数 可为空
//        'function' => 'clear_log', // 要执行的函数
//        'params' => array() // 需要传递的参数
//    )
//);

//每天定时把JD的关键词、描述导入到对应的表
$cron_schedule['jd_keyword_describe_import'] = array(
    'schedule' => array(
        'config_path' => '', // cron表达式的标识 用于在配置文件或数据库中获取表达式 直接指定时为空
        'cron_expr' => '* * */1 * *' // 直接指定cron表达式 在配置文件或数据库中获取表达式为空
    ),
    'run' => array(
        'filepath' => 'cron', // 文件所在的目录 相对于APPPATH
        'filename' => 'CronManage.php', // 文件名
        'class' => 'CronManage', // 类名 如果只是简单函数 可为空
        'function' => 'import_keyword_describe', // 要执行的函数
        'params' => array() // 需要传递的参数
    )
);
