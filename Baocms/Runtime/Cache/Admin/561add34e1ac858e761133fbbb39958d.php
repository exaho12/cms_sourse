<?php if (!defined('THINK_PATH')) exit();?><div class="mainScAdd ">
    <div class="tableBox">
        <form target="baocms_frm" action="<?php echo U('goodscate/edit',array('cate_id'=>$detail['cate_id']));?>" method="post">
            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">分类：</td>
                    <td class="rgTdBt"><input type="text" name="data[cate_name]" value="<?php echo (($detail["cate_name"])?($detail["cate_name"]):''); ?>" class="manageInput" />

                    </td>
                </tr>
               
                <tr>
                    <td class="lfTdBt">排序：</td>
                    <td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="manageInput" />
                        <code>数字越小越高</code>
                    </td>
                </tr>
            </table>
            <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>
        </form>
    </div>
</div>