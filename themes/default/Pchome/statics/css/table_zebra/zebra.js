/**
 * Created by Administrator on 2016/3/12.
 */
$(document).ready(function () {

    // assuming the controls you want to attach the plugin to
    // have the "datepicker" class set
//        $('#datepicker-example1').Zebra_DatePicker();//1
    $('#datepicker-example1').Zebra_DatePicker({
//            direction: 1 ,   // 2.boolean true would've made the date picker future only
//                            // but starting from today, rather than tomorrow
//
//            direction: true,
//            disabled_dates: ['* * * 0,6'] ,  //3. all days, all monts, all years as long
//                                            // as the weekday is 0 or 6 (Sunday or Saturday)
//
//
//
//            direction: [1, 10],//4.he selectable dates are in the interval starting tomorrow and ending 10 days from tomorrow.
//            //5.Dates can be selected between 2 specific dates
//            // remember that the way you write down dates
//            // depends on the value of the "format" property!
//            direction: ['2012-08-01', '2012-08-12'],
//
//            //6. Dates can be selected in the future, starting with a specific date
//            // remember that the way you write down dates
//            // depends on the value of the "format" property!
//            direction: ['2012-08-01', false],
//
        format: 'Ymd',
        header_captions: {
            'days': 'Y年m月',
            'months': 'Y年',
            'years': 'Y1年 - Y2年'
        },
//            inside:false,
        // 'show_icon':false,
        'lang_clear_date': '清空',
        'months_abbr': true,
//                    ['一','二','三','四','五','六','日'],
        'months': ['一月', '二月', '三月', '四月', '五月', '六月', '七月',
            '八月', '九月', '十月', '十一月', '十二月'],
        'show_select_today': '今天',
//            'offset':[-5,5],
//            'readonly_element':false,
        'show_week_number': '周数',

//            always_visible: $('#container'),
    });


    $('#datepicker-example2-start').Zebra_DatePicker({
//            direction: true,
        format: 'Ymd',
        header_captions: {
            'days': 'Y年m月',
            'months': 'Y年',
            'years': 'Y1年 - Y2年'
        },
//            inside:false,
        // 'show_icon':false,
        'lang_clear_date': '清空',
        'months_abbr': true,
//                    ['一','二','三','四','五','六','日'],
        'months': ['一月', '二月', '三月', '四月', '五月', '六月', '七月',
            '八月', '九月', '十月', '十一月', '十二月'],
        'show_select_today': '今天',
//            'offset':[-5,5],
//            'readonly_element':false,
        'show_week_number': true,
        pair: $('#datepicker-example2-end')
    });

    $('#datepicker-example2-end').Zebra_DatePicker({
        format: 'Ymd',
        header_captions: {
            'days': 'Y年m月',
            'months': 'Y年',
            'years': 'Y1年 - Y2年'
        },
//            inside:false,
        // 'show_icon':false,
        'lang_clear_date': '清空',
        'months': ['一月', '二月', '三月', '四月', '五月', '六月', '七月',
            '八月', '九月', '十月', '十一月', '十二月'],
        'show_select_today': '今天',
//            direction: 1
    });

    function method() {

        var curTbl = document.getElementById('zebras');
        var oXL = new ActiveXObject("Excel.Application");
        var oWB = oXL.Workbooks.Add();
        var oSheet = oWB.ActiveSheet;
        var sel = document.body.createTextRange();
        sel.moveToElementText(curTbl);
        sel.select();
        sel.execCommand("Copy");
        oSheet.Paste();
        oXL.Visible = true;

    }
});
