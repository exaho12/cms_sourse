<?php if (!defined('THINK_PATH')) exit();?><div class="listBox clfx">
    <div class="menuManage">
        <form target="baocms_frm" action="<?php echo U('shopcate/edit',array('cate_id'=>$detail['cate_id']));?>" method="post">
            <div class="mainScAdd">
                <div class="tableBox">
                    <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >

                        <tr>
                            <td class="lfTdBt">分类：</td>
                            <td class="rgTdBt"><input type="text" name="data[cate_name]" value="<?php echo (($detail["cate_name"])?($detail["cate_name"]):''); ?>" class="scAddTextName w150" />

                            </td>
                        </tr>
                        <tr>
                            <td class="lfTdBt">排序：</td>
                            <td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="scAddTextName w150" />
                                <code>数字越小越高</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="lfTdBt">点评项1：</td>
                            <td class="rgTdBt"><input type="text" name="data[d1]" value="<?php echo (($detail["d1"])?($detail["d1"]):''); ?>" class="scAddTextName w150" />
                                <code>主要用着商家点评，默认预设了3个点评项</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="lfTdBt">点评项2：</td>
                            <td class="rgTdBt"><input type="text" name="data[d2]" value="<?php echo (($detail["d2"])?($detail["d2"]):''); ?>" class="scAddTextName w150" />
                                <code>主要用着商家点评，默认预设了3个点评项</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="lfTdBt">点评项3：</td>
                            <td class="rgTdBt"><input type="text" name="data[d3]" value="<?php echo (($detail["d3"])?($detail["d3"]):''); ?>" class="scAddTextName w150" />
                                <code>主要用着商家点评，默认预设了3个点评项</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="lfTdBt">分类描述：</td>
                            <td class="rgTdBt"><input type="text" name="data[title]" value="<?php echo (($detail["title"])?($detail["title"]):''); ?>" class="scAddTextName w150" />
                                <code>可以简单的说一下分类</code>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="smtQr"><input type="submit" value="确定编辑" class="smtQrIpt" /></div>
            </div>
        </form>
    </div>
</div>