<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>{title}</title>
        <meta charset="utf-8">
        <meta name="Keywords" content="{keywords}" />
        <meta name="Description" content="{description}" />
        <meta name="format-detection" content="telephone=no"/>
        <link rel="stylesheet" href="{app_site_domain}{css_path}company_resume.css"  type="text/css" />
    </head>
    <body>
        <div style="background:#f4f4f6;padding: 50px 0;">
            <style>{css_content}</style>
            <div class="box">
                <h1 style="height:81px;text-indent:-9999px;background:#6babe5 url({app_site_domain}{image_path}logo.jpg) no-repeat 0 0;">继也招聘</h1>

                <div class="main">

                    <div class="top">
                        <h2>{companyName}，您好</h2>
                        <p> 为您推荐 {jobDesc}，共{jobCnt}个岗位。</p>
                        <p>如有您认为合适的人才，请回复邮件，告知编号，我们将及时回复详细简历。</p>
                        <p>PS，请尽快联系中意的候选人，根据经验，优秀的候选人在一周左右就会确定目标企业。</p>
                    </div>

                    <div class="listbox">
                       {resumeDesc}
                    </div>
                </div>

                <div class="links">
                    <a href="#">关于我们</a>
                    <a href="#">付款操作</a>
                    <a href="#">联系客服</a>
                    <a href="#" class="on last">订阅职位</a>
                </div>
                <img src="{app_site_domain}/email/company_{companyId}.jpg" style="display:none;">
            </div>
        </div>
    </body>
</html>