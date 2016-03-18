<?php if (!defined('THINK_PATH')) exit();?><div class="listBox clfx">
    <div class="menuManage">
        <form target="baocms_frm" action="<?php echo U('business/edit',array('business_id'=>$detail['business_id'],'area_id'=>$area_id));?>" method="post">
            <div class="mainScAdd">
                <div class="tableBox">
                    <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                        <tr>
                            <td class="lfTdBt">商圈名称：</td>
                            <td class="rgTdBt"><input type="text" name="data[business_name]" value="<?php echo (($detail["business_name"])?($detail["business_name"]):''); ?>" class="manageInput" />
                            </td>
                        </tr>   
                        <tr>
                            <td class="lfTdBt">排序：</td>
                            <td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="manageInput" />
                                <code>数字越小排序越高</code>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="smtQr"><input type="submit" value="确定编辑" class="smtQrIpt" /></div>
            </div>
        </form>
    </div>
</div>