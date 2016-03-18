<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo ($CONFIG["site"]["title"]); ?>管理后台</title>
        <meta name="description" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <meta name="keywords" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <!-- <link href="__TMPL__statics/css/index.css" rel="stylesheet" type="text/css" /> -->
        <link href="__TMPL__statics/css/style.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/land.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/pub.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/main.css" rel="stylesheet" type="text/css" />
        <link href="__PUBLIC__/js/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script> var BAO_PUBLIC = '__PUBLIC__'; var BAO_ROOT = '__ROOT__'; </script>
        <script src="__PUBLIC__/js/jquery.js"></script>
        <script src="__PUBLIC__/js/jquery-ui.min.js"></script>
        <script src="__PUBLIC__/js/my97/WdatePicker.js"></script>
        <script src="/Public/js/layer/layer.js"></script>
        <script src="__PUBLIC__/js/admin.js?v=20150409"></script>
    </head>
    <body>
         <iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
   <div class="main">

<div class="mainBt">

    <ul>

        <li class="li1">设置</li>

        <li class="li2">基本设置</li>

        <li class="li2 li3">站点设置</li>

    </ul>

</div>

<p class="attention"><span>注意：</span>这个设置和全局有关系</p>

<form  target="baocms_frm" action="<?php echo U('setting/site');?>" method="post">

    <div class="mainScAdd">

        <div class="tableBox">

            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >

                <tr>

                    <td class="lfTdBt">站点名称：</td>

                    <td class="rgTdBt"><input type="text" name="data[sitename]" value="<?php echo ($CONFIG["site"]["sitename"]); ?>" class="scAddTextName " />

                        <code>注意这个不是网站的Title，一般建议是网站的品牌名</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">站点网址：</td>

                    <td class="rgTdBt"><input type="text" name="data[host]" value="<?php echo ($CONFIG["site"]["host"]); ?>" class="scAddTextName " />

                        <code>例如：http://www.baocms.cn 如果你在二级目录下面就需要带上二级目录</code></td>

                </tr>

                <tr>

                    <td class="lfTdBt">图片域名：</td>

                    <td class="rgTdBt"><input type="text" name="data[imgurl]" value="<?php echo ($CONFIG["site"]["imgurl"]); ?>" class="scAddTextName " />

                        <code>例如：http://www.baocms.cn 一般情况下是和站点网址一样，如果做了CDN加速一般就可能是其他的域名</code></td>

                </tr>

                <tr>

                    <td class="lfTdBt">android下载地址：</td>

                    <td class="rgTdBt"><input type="text" name="data[android]" value="<?php echo ($CONFIG["site"]["android"]); ?>" class="scAddTextName " />

                        <code>android下载地址</code></td>

                </tr>

                <tr>

                    <td class="lfTdBt">IOS下载地址：</td>

                    <td class="rgTdBt"><input type="text" name="data[ios]" value="<?php echo ($CONFIG["site"]["ios"]); ?>" class="scAddTextName " />

                        <code>IOS下载地址</code></td>

                </tr>



                <tr>

                    <td class="lfTdBt">LOGO：</td>

                    <td class="rgTdBt"><div style="width: 300px; height: 100px; float: left;">

                            <input type="hidden" name="data[logo]" value="<?php echo ($CONFIG["site"]["logo"]); ?>" id="data_photo" />

                            <input id="photo_file" name="photo_file" type="file" multiple="true" value="" />

                        </div>

                        <div style="width: 300px; height: 100px; float: left;">

                            <img id="photo_img" width="200" height="80"  src="__ROOT__/attachs/<?php echo (($CONFIG["site"]["logo"])?($CONFIG["site"]["logo"]):'default.jpg'); ?>" />

                        </div>

                        <script type="text/javascript" src="__PUBLIC__/js/uploadify/jquery.uploadify.min.js?t=<?php echo ($nowtime); ?>"></script>



                        <link rel="stylesheet" href="__PUBLIC__/js/uploadify/uploadify.css">

                        <script>

                            $("#photo_file").uploadify({

                                'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',

                                'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"setting"));?>',

                                'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',

                                'buttonText': '上传网站LOGO',

                                'fileTypeExts': '*.gif;*.jpg;*.png',

                                'queueSizeLimit': 1,

                                'onUploadSuccess': function (file, data, response) {

                                    $("#data_photo").val(data);

                                    $("#photo_img").attr('src', '__ROOT__/attachs/' + data).show();

                                }

                            });



                        </script></td>

                </tr>

                <tr>

                    <td class="lfTdBt">客服QQ：</td>

                    <td class="rgTdBt"><input type="text" name="data[qq]" value="<?php echo ($CONFIG["site"]["qq"]); ?>" class="scAddTextName " />

                    <code>前台模板显示调用！</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">电话：</td>

                    <td class="rgTdBt"><input type="text" name="data[tel]" value="<?php echo ($CONFIG["site"]["tel"]); ?>" class="scAddTextName " />

                    <code>前台模板显示调用！</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">邮件：</td>

                    <td class="rgTdBt"><input type="text" name="data[email]" value="<?php echo ($CONFIG["site"]["email"]); ?>" class="scAddTextName " />

                    <code>前台模板显示调用！</code>

                    </td>

                </tr>

               

                <tr>

                    <td class="lfTdBt">ICP备案：</td>

                    <td class="rgTdBt"><input type="text" name="data[icp]" value="<?php echo ($CONFIG["site"]["icp"]); ?>" class="scAddTextName " />

                    <code>前台模板显示调用！</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">站点标题：</td>

                    <td class="rgTdBt"><input type="text" name="data[title]" value="<?php echo ($CONFIG["site"]["title"]); ?>" class="scAddTextName " />

                    <code>seo设置中调用</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">站点关键字：</td>

                    <td class="rgTdBt"><textarea name="data[keyword]" cols="50" rows="5"><?php echo ($CONFIG["site"]["keyword"]); ?></textarea>

                    <code>seo设置中调用，建议认真填写！</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">站点描述：</td>

                    <td class="rgTdBt"><textarea name="data[description]" cols="50" rows="5"><?php echo ($CONFIG["site"]["description"]); ?></textarea>

                    <code>seo设置中调用</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt">网站头部信息：</td>

                    <td class="rgTdBt"><textarea name="data[headinfo]" cols="150" rows="8"><?php echo ($CONFIG["site"]["headinfo"]); ?></textarea>

                    <code>首页顶部的信息在这里删除！一般情况下无需填写！</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt">统计代码：</td>

                    <td class="rgTdBt"><textarea name="data[tongji]" cols="50" rows="5"><?php echo ($CONFIG["site"]["tongji"]); ?></textarea>

                    <code>模板中调用，有统计代码的填写在这里。</code>

                    </td>

                </tr>



                <tr>

                    <td class="lfTdBt">默认城市：</td>

                    <td class="rgTdBt">

                        <select name="data[city_id]" class="selectOption">

                            <?php  foreach($citys as $val){?>

                            <option <?php if($val['city_id'] == $CONFIG['site']['city_id']) echo 'selected="selected"' ;?> value="<?php echo ($val["city_id"]); ?>"><?php echo ($val["name"]); ?></option>

                            <?php }?>

                        </select>

                        <code>请填写您的默认城市。</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt" style="padding-right: 0px;">城市中心地图坐标：</td>

                    <td class="rgTdBt">

                        经度  <input type="text" name="data[lng]" value="<?php echo ($CONFIG["site"]["lng"]); ?>" class="scAddTextName " />

                        纬度 <input type="text" name="data[lat]" value="<?php echo ($CONFIG["site"]["lat"]); ?>" class="scAddTextName " />

                        <code>关系到全局默认，请认真填写！</code>

                        </td>

                </tr>

                <tr>

                    <td class="lfTdBt">自动收货时间：</td>

                    <td class="rgTdBt">

                        商城<input type="text" name="data[goods]" value='<?php echo ($CONFIG["site"]["goods"]); ?>' style="width: 50px;"  class="scAddTextName " />（天）

                        外卖<input type="text" name="data[ele]" value='<?php echo ($CONFIG["site"]["ele"]); ?>' style="width: 50px;"  class="scAddTextName " />（小时）

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt">整合UCENTER：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[ucenter]" <?php if(($CONFIG["site"]["ucenter"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[ucenter]"  <?php if(($CONFIG["site"]["ucenter"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启这个需要先在UCENTER接口配置先配置好！</code>

                    </td>

                </tr>

                <tr>

                    <td class="lfTdBt">贴吧发帖免审核：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[postaudit]" <?php if(($CONFIG["site"]["postaudit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[postaudit]"  <?php if(($CONFIG["site"]["postaudit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启之后帖子发布免审核！</code>

                    </td>

                </tr>

                 <tr>

                    <td class="lfTdBt">贴吧回帖免审核：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[replyaudit]" <?php if(($CONFIG["site"]["replyaudit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[replyaudit]"  <?php if(($CONFIG["site"]["replyaudit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启之后帖子发布免审核！</code>

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt">小区发帖免审核：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[xiaoqu_post_audit]" <?php if(($CONFIG["site"]["xiaoqu_post_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[xiaoqu_post_audit]"  <?php if(($CONFIG["site"]["xiaoqu_post_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启之后小区帖子发布免审核！</code>

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt">小区回帖免审核：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[xiaoqu_reply_audit]" <?php if(($CONFIG["site"]["xiaoqu_reply_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[xiaoqu_reply_audit]"  <?php if(($CONFIG["site"]["xiaoqu_reply_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启之后小区回帖发布免审核！</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt">新闻评论免审核：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[article_reply_audit]" <?php if(($CONFIG["site"]["article_reply_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[article_reply_audit]"  <?php if(($CONFIG["site"]["article_reply_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启后新闻评论免审核！小心使用！</code>

                    </td>

                </tr>
                
                
                 <tr>
                    <td class="lfTdBt">物业发送通知免审核：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[xiaoqu_news_audit]" <?php if(($CONFIG["site"]["xiaoqu_news_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[xiaoqu_news_audit]"  <?php if(($CONFIG["site"]["xiaoqu_news_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>
                        <code>开启之后物业发送通知免审核，自己看吧，怕就不要开启自动！</code>
                    </td>
                </tr>

                

                 <tr>

                    <td class="lfTdBt">手机端发布约会免审核：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[hdmobile_hdfabu_audit]" <?php if(($CONFIG["site"]["hdmobile_hdfabu_audit"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[hdmobile_hdfabu_audit]"  <?php if(($CONFIG["site"]["hdmobile_hdfabu_audit"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启手机端发布的约会会员发布后即可显示无需审核！小心使用！</code>

                    </td>

                </tr>

                

                

                

                <tr>

                    <td class="lfTdBt">微信自动绑定：</td>

                    <td class="rgTdBt">

                        <label><input type="radio" name="data[weixin]" <?php if(($CONFIG["site"]["weixin"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                        <label><input type="radio" name="data[weixin]"  <?php if(($CONFIG["site"]["weixin"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />不开启</label>

                        <code>开启后微信端会自动跳转到绑定账号，如果已经绑定账号将自动登录！</code>

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt">城市编号：</td>

                    <td class="rgTdBt">

                 <input type="text" name="data[citycode]" value="<?php echo ($CONFIG["site"]["citycode"]); ?>" class="scAddTextName " />

                        <code>联系www.fengmiyuanma.com获取，自动采集商家用的！</code>

                    </td>

                </tr>

                

                <!--分销开始-->

                <tr>

                    <td class="lfTdBt">拥有分销权限用户最低等级：</td>

                    <td class="rgTdBt">

						<select name="data[profit_min_rank_id]" class="seleFl w200">

							<option value="0">不限制</option>

							<?php if(is_array($ranks)): foreach($ranks as $key=>$item): ?><option <?php if(($item["rank_id"]) == $CONFIG["site"]["profit_min_rank_id"]): ?>selected="selected"<?php endif; ?> value="<?php echo ($item["rank_id"]); ?>"><?php echo ($item["rank_name"]); ?></option><?php endforeach; endif; ?>

						</select>

					</td>

                </tr>

                <tr>

                    <td class="lfTdBt">推荐时效：</td>

                    <td class="rgTdBt"><input type="number" min="1" max="1000" name="data[profit_expire]" value='<?php echo ($CONFIG["site"]["profit_expire"]); ?>' class="scAddTextName " />小时<code>访问者点击某推荐人的网址后，在此时间段内注册、下单，均认为是该推荐人所介绍的</code></td>

                </tr>

                <tr>

                    <td class="lfTdBt">一级会员注册分成积分数：</td>

                    <td class="rgTdBt"><input type="number" min="0" max="1000" name="data[profit_integral1]" value='<?php echo ($CONFIG["site"]["profit_integral1"]); ?>' class="scAddTextName " /></td>

                </tr>

                <tr>

                    <td class="lfTdBt">二级会员注册分成积分数：</td>

                    <td class="rgTdBt"><input type="number" min="0" max="1000" name="data[profit_integral2]" value='<?php echo ($CONFIG["site"]["profit_integral2"]); ?>' class="scAddTextName " /></td>

                </tr>

                <tr>

                    <td class="lfTdBt">三级会员注册分成积分数：</td>

                    <td class="rgTdBt"><input type="number" min="0" max="1000" name="data[profit_integral3]" value='<?php echo ($CONFIG["site"]["profit_integral3"]); ?>' class="scAddTextName " /></td>

                </tr>

                <tr>

                    <td class="lfTdBt">一级会员订单分成比例：</td>

                    <td class="rgTdBt"><input type="number" min="0" max="100" name="data[profit_rate1]" value='<?php echo ($CONFIG["site"]["profit_rate1"]); ?>' class="scAddTextName " />%</td>

                </tr>

                <tr>

                    <td class="lfTdBt">二级会员订单分成比例：</td>

                    <td class="rgTdBt"><input type="number" min="0" max="100" name="data[profit_rate2]" value='<?php echo ($CONFIG["site"]["profit_rate2"]); ?>' class="scAddTextName " />%</td>

                </tr>

                <tr>

                    <td class="lfTdBt">三级会员订单分成比例：</td>

                    <td class="rgTdBt"><input type="number" min="0" max="100" name="data[profit_rate3]" value='<?php echo ($CONFIG["site"]["profit_rate3"]); ?>' class="scAddTextName " />%</td>

                </tr>

                

                <!--分销结束-->

                

                <tr>

                    <td class="lfTdBt">全局通知手机号码</td>

                    <td class="rgTdBt">

                 <input type="text" name="data[config_mobile]" value="<?php echo ($CONFIG["site"]["config_mobile"]); ?>" class="scAddTextName " />

                        <code>填写有有的场景需要通知给管理员的手机号！</code>

                    </td>

                </tr>

                

                <tr>

                    <td class="lfTdBt">全局通知邮箱</td>

                    <td class="rgTdBt">

                 <input type="text" name="data[config_email]" value="<?php echo ($CONFIG["site"]["config_email"]); ?>" class="scAddTextName " />

                        <code>这里是在必要情况下，给站长发邮箱的时候的接受信箱。</code>

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt">是否关闭网站：</td>

                    <td class="rgTdBt">

                 <label><input type="radio" name="data[web_close]" <?php if(($CONFIG["site"]["web_close"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>

                 <label><input type="radio" name="data[web_close]"  <?php if(($CONFIG["site"]["web_close"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />关闭</label>

                        <code style="color:#F00">关闭之后前台打不开哦，突发情况以及备案的时候可以关闭，其他时候不要去动！关闭后不影响后台跟商家后台！</code>

                    </td>

                </tr>

                

                 <tr>

                    <td class="lfTdBt">关闭网站原因：</td>

                    <td class="rgTdBt"><textarea name="data[web_close_title]" cols="50" rows="4"><?php echo ($CONFIG["site"]["web_close_title"]); ?></textarea>

                    <code>这里填写关站原因，将会显示到前台首页！</code>

                    </td>

                </tr>

                

                

            </table>

        </div>

        <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>

    </div>

</form>


</div>
</body>
</html>