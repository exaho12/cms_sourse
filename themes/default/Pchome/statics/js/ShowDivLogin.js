var Fi = {};
(function(){
    var MD5 = function(string) {
        function RotateLeft(lValue, iShiftBits) {
            return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
        }

        function AddUnsigned(lX, lY) {
            var lX4, lY4, lX8, lY8, lResult;
            lX8 = (lX & 0x80000000);
            lY8 = (lY & 0x80000000);
            lX4 = (lX & 0x40000000);
            lY4 = (lY & 0x40000000);
            lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
            if (lX4 & lY4) {
                return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
            }
            if (lX4 | lY4) {
                if (lResult & 0x40000000) {
                    return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                }
                else {
                    return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                }
            }
            else {
                return (lResult ^ lX8 ^ lY8);
            }
        }

        function F(x, y, z) {
            return (x & y) | ((~x) & z);
        }
        function G(x, y, z) {
            return (x & z) | (y & (~z));
        }
        function H(x, y, z) {
            return (x ^ y ^ z);
        }
        function I(x, y, z) {
            return (y ^ (x | (~z)));
        }

        function FF(a, b, c, d, x, s, ac) {
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };

        function GG(a, b, c, d, x, s, ac) {
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };

        function HH(a, b, c, d, x, s, ac) {
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };

        function II(a, b, c, d, x, s, ac) {
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };
        function ConvertToWordArray(string) {
            var lWordCount;
            var lMessageLength = string.length;
            var lNumberOfWords_temp1 = lMessageLength + 8;
            var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
            var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
            var lWordArray = Array(lNumberOfWords - 1);
            var lBytePosition = 0;
            var lByteCount = 0;
            while (lByteCount < lMessageLength) {
                lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                lBytePosition = (lByteCount % 4) * 8;
                lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
                lByteCount++;
            }
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
            lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
            lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
            return lWordArray;
        };

        function WordToHex(lValue) {
            var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
            for (lCount = 0; lCount <= 3; lCount++) {
                lByte = (lValue >>> (lCount * 8)) & 255;
                WordToHexValue_temp = "0" + lByte.toString(16);
                WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
            }
            return WordToHexValue;
        };

        function Utf8Encode(string) {
            string = string.replace(/\r\n/g, "\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        };

        var x = Array();
        var k, AA, BB, CC, DD, a, b, c, d;
        var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
        var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
        var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
        var S41 = 6, S42 = 10, S43 = 15, S44 = 21;

        string = Utf8Encode(string);

        x = ConvertToWordArray(string);

        a = 0x67452301;
        b = 0xEFCDAB89;
        c = 0x98BADCFE;
        d = 0x10325476;

        for (k = 0; k < x.length; k += 16) {
            AA = a;
            BB = b;
            CC = c;
            DD = d;
            a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
            d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
            c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
            b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
            a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
            d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
            c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
            b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
            a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
            d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
            c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
            b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
            a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
            d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
            c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
            b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
            a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
            d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
            c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
            b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
            a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
            d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
            c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
            b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
            a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
            d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
            c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
            b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
            a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
            d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
            c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
            b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
            a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
            d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
            c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
            b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
            a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
            d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
            c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
            b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
            a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
            d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
            c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
            b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
            a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
            d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
            c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
            b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
            a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
            d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
            c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
            b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
            a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
            d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
            c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
            b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
            a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
            d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
            c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
            b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
            a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
            d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
            c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
            b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
            a = AddUnsigned(a, AA);
            b = AddUnsigned(b, BB);
            c = AddUnsigned(c, CC);
            d = AddUnsigned(d, DD);
        }

        var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);

        return temp.toLowerCase();
    }
    Fi.MD5 =  MD5
})();

$(document).ready(function () {
    WriteLoginHtml();
});

function WriteLoginHtml() {
    $("body:eq(0)").append("<input type='hidden' id='cktype' /><input type='hidden' id='sendval' /><input type='hidden' id='sendval2' /><input type='hidden' id='sendval3' />"
					+ "<div id='BgDiv'></div><div class='dise none'></div>"
					+ "<div class='loginBoxs none'>"
						+ "<div class='loginHead'><h3>登 录</h3><a href='#a' id='closeLoginBoxs'><img src='http://img.17u.com/com/img/public/login_close.png' /></a></div>"
						+ "<div class='loginBody'>"
							+ "<div class='loginLeft'>"
							+ "<span class='withHerHe'>和她、和他、和Ta，一起游</span><p class='lifeTrive'>人的一生，就是一场旅行，<br />从这开始，共同探索美好的世界</p><a href='https://passport.ly.com/register/index/?pageurl="+ encodeURIComponent(window.location.href) +"' class='RegisterNow' target='_blank'>立即注册</a>"
							+ "<p class='registerType'>您也可使用以下方式直接登录：</p>"
							+ "<span class='registerInput'><a title='QQ登录' href=\"http://www.17u.com/my17u/member/gotoqq/\"><img src=\"http://img.17u.com/com/img/public/logIn/qqLogin.png\"/></a><a title='豆瓣登录' href=\"/my17u/member/gotodouban\"><img src=\"http://img.17u.com/com/img/public/logIn/doubanLogin.png\"/></a><a title='人人登录' href=\"/my17u/member/renrenlogin\"><img src=\"http://img.17u.com/com/img/public/logIn/renrenLogin.png\"></a><a title='新浪登录' href=\"http://www.17u.com/my17u/member/gotosina/\"><img src=\"http://img.17u.com/com/img/public/logIn/sinaLogin.png\"/></a></span>"
							+ "</div>"
							+ "<div class='loginRight'><span class='beginPlay'>会员登录</span>"
							+ "<p class='logInTxtP'>请输入您的用户名</p>"
							+ "<label class='loginLabel' for='userName'>用户名：</label><input type='text' class='shuruBox' id='txtUserName' value='手机号/邮箱' />"
							+ "<label class='loginLabel' for='passWord'>密码：</label><input type='password' class='shuruBox' id='txtPassWord' value='' />"
							+ "<div id='getcode' style='padding-left: 27px; clear: both;'></div>"
							+ "<div class='zidongBox'><input type='checkbox' class='zidongLogin' name='xczddl' id='xczddl' /><label for='xczddl'>两周内自动登录</label></div>"
							+ "<a href='javascript:void(0)' class='loginNow'>登&nbsp;&nbsp;&nbsp;&nbsp;录</a> <span class='forgetPassWord'><a href='http://passport.17u.cn/Passport/GetPassword/' target='_blank'>忘记密码啦？果断找回</a></span>"
							+ "</div>"
						+ "</div>"
					+ "</div>");
popup = $("#getcode")[0];
                        var width = document.body.getBoundingClientRect().width * 0.8;
                        var s = document.createElement("script");
                        s.src = "http://api.geetest.com/get.php?gt=ba0422d0fdca808ac82b4cbdca7ae64d" + "&width=" + parseInt(width);
                        s.async = true;
                        popup.appendChild(s);


}

//显示登录弹出框
function ShowDIV() {
    //WriteLoginHtml();
    $('#txtPassWord').val(''); //清空输入密码
    $("#BgDiv").css('display', 'block');
    $("#BgDiv").css('background', '#666');
    $("#BgDiv").css({
        display: "block",
        height: $(document).height()
    });
    var yscroll = document.documentElement.scrollTop;
    var h1 = $(document).height();
    var h2 = $(document).scrollTop();
    var h3 = $(window).height();
    var h4 = h2 + h3 / 2;
    $(".loginBoxs").css("top", h4 - 125);
    $(".loginBoxs").removeClass("none");
    $(".dise").removeClass("none");
    $('.dise').css("top", h4 - 135);
    $(".loginBoxs").removeClass("none animationOut").addClass('animationLogin');
    //统计弹出登录层的次数
    var urlHost = window.location.host;
    var proJectName = "destination";
    if (urlHost.indexOf("go.ly.com/") > -1||urlHost.indexOf("go.ly.com") > -1) {
        proJectName = "gonglve";

    }
    $.post("/" + proJectName + "/ajax/insertclickstat",
            { objectId: "0", typeId: "2" },
            function (data) { }
        );
}
$('#txtUserName').live('focusin', function () {
    var nameVal = $('#txtUserName').val();
    if (nameVal == '手机号/邮箱') {
        $('#txtUserName').val("");
    }
    $(this).css({ borderColor: '#3b85ee', color: '#000' });
});
$('#txtUserName').live('focusout', function () {
    var nameVal = $('#txtUserName').val();
    if (nameVal == '') {
        $('#txtUserName').val('手机号/邮箱');
        $(this).css({ borderColor: '#ccc', color: '#d0d0d0' });
        return false;
    }
    $(this).css({ borderColor: '#ccc', color: '#000' });
})
$('#txtPassWord').live('focusin', function () {
    $(this).css({ borderColor: '#3b85ee', color: '#000' });
});
$('#txtPassWord').live('focusout', function () {
    $(this).css({ borderColor: '#ccc', color: '#000' });
});

function SetUserInfomation() {
    var userId = document.getElementById("txtUserName").value;
    var userIdPassword = document.getElementById("txtPassWord").value;
    if (userId == "" || userId == "手机号/邮箱") {
        $('.logInTxtP')[0].innerHTML = "请输入您的用户名";
        $('.logInTxtP').css('visibility', 'visible').addClass('sharkClass');
        setTimeout("$('.logInTxtP').removeClass('sharkClass')", 1500)

        $('#txtUserName').css('border-color', '#ff8080');
        $('#txtUserName')[0].focus();
    }
    else
        if (userIdPassword == "") {
            $('.logInTxtP')[0].innerHTML = "请输入密码";
            $('.logInTxtP').css('visibility', 'visible').addClass('sharkClass');
            setTimeout("$('.logInTxtP').removeClass('sharkClass')", 1500)            
            $('#txtPassWord').css('border-color', '#ff8080');
            $('#txtPassWord')[0].focus();
        }
        else
            if ((userId != "") && (userIdPassword != "")) {
                $('.loginNow').html("正在登录...");
                var istype = 0;
                if ($("#xczddl").attr("checked") == "checked") {
                    istype = 1
                }
                var loginUrl = "/my17u/member/showdivlogin";
                $.ajax({
                    type: "POST",
                    url: loginUrl,
                    async: false,
                    data: "loginName=" + userId + "&password=" + Fi.MD5(userIdPassword) + "&type=" + istype + "&rt=mt&geetest_challenge="+$(".geetest_challenge").val()+"&geetest_validate="+$(".geetest_validate").val()+"&geetest_seccode="+$(".geetest_seccode").val(),
                    success: function (pass) {
                        if (pass.user.State == "200") {
                            $(".zc_dl").addClass("none");
                            $(".loginInChange ").removeClass("none");
                            $.post("/my17u/member/FreshCount", { memberId: pass.user.UserId }, function (data) {
                                if (data == 0) {
                                    $(".Hasemail").addClass("none");
                                }
                                if (data > 0 && data <= 10) {
                                    $(".Hasemail").html(data);
                                }
                                if (data > 10) {
                                    $(".Hasemail").html("+10");
                                }
                            })

                            //统计登录成功的次数
                            var urlHost = window.location.host;
                            var proJectName = "destination";
                            if (urlHost.indexOf("go.ly.com/") > -1 || urlHost.indexOf("go.ly.com") > -1) {
                                proJectName = "gonglve";

                            }
                            $.post("/" + proJectName + "/ajax/insertclickstat",
									{ objectId: "0", typeId: "3" },
									function (data) {
									    $('#loginTl').hide(200);
									}
								);
                            userLogin();
                            CloseLoginDiv();
                            var cktype = $("#cktype").val();
                            var sendval = $("#sendval").val();
                            var sendval2 = $("#sendval2").val();
                            var sendval3 = $("#sendval3").val();
                            var pagetype = $("#pagetype").val();

                            if (pagetype == "xieCommet") {

                                var tagIdList = "";
                                $(".xuanzhong").each(function () {
                                    tagIdList += $(this).attr("rel") + ",";
                                });
                                var desitemid = $("#desitemid").val();
                                var desitemkind = $("#desitemkind").val();
                                var oh = $("#oh").val();
                                var score = $("#star-score").val();

                                $.ajax({
                                    type: "POST",
                                    url: "/" + proJectName + "/newdestinationcommentcommon/newwritecomment",
                                    data: "desitemId=" + desitemid + "&desitemKind=" + desitemkind + "&content=" + oh + "&appraisal=" + score + "&tagIdList=" + tagIdList,
                                    success: function (msg) {
                                        if (msg == "1") {
                                            ShowChenggong(desitemid, desitemkind);
                                        }
                                    }
                                });
                            } else {
                                if (cktype == "1") {
                                    wantGo("false");
                                }
                                else if (cktype == "2") {
                                    gone("false")
                                }
                                else if (cktype == "3") {
                                    $.post("/" + proJectName + "/ajax/destoperation",
										{
										    destItemId: $("#sendval").val(),
										    op: "yjtj",
										    opFlag: "false"
										},
									function (data) {
									    if (data == "1") {
									        var n = $("span[rel=" + $("#sendval").val() + "]").children('em').text();
									        $("span[rel=" + $("#sendval").val() + "]").children('em').text(parseInt(n) + 1);
									        alert("推荐成功!");
									    }
									    else if (data == "3") {
									        ShowDIV();
									    }
									    else {
									        alert("您已经推荐过该游记了！");
									    }
									});
                                }
                                else if (cktype == "4" || cktype == "5" || cktype == "6" || cktype == "8") {
                                    location = location;
                                }
                                else if (cktype == "7") {
                                    userLogin();
                                    var op;
                                    if (sendval3 == "2") {
                                        op = "pdl";
                                    } else if (sendval3 == "3") {
                                        op = "cdl";
                                    } else if (sendval3 == "20") {
                                        op = "sdl";
                                    } else if (sendval3 == "10") {
                                        op = "zdl";
                                    } else if (sendval3 == "11") {
                                        op = "jwgdl";
                                    } else if (sendval3 == "12") {
                                        op = "jwpdl";
                                    } else if (sendval3 == "13") {
                                        op = "jwcdl";
                                    }
                                    $.post("/" + proJectName + "/ajax/destoperation",
                                    {
                                        destItemId: sendval2,
                                        op: op,
                                        opFlag: "false"
                                    },
                                    function (data) {
                                        if (data == "1") {
                                            if (sendval.indexOf("http://") == -1) {
                                                document.location = "http://down.17u.com/2010/pdf/index.asp?pdfaddr=" + sendval;
                                            } else {
                                                document.location = sendval;
                                            }
                                        }
                                        if (data == "0") {
                                            alert("对不起，下载失败！");
                                        }
                                    });
                                }
                                else if (cktype == "-1") {
                                    var fid = $("#followId").val();
                                    $("#uid").val(pass.user.UserId)
                                    var str = $(".guanzhuButton");
                                    if (uid == fid) {
                                        alert("自己不能添加自己");
                                    }
                                    else {
                                        $.post("/my17u/myseventyu/AddFriend", { "memberId": pass.user.UserId, "friend": fid }, function (data) {
                                            if (data == 1) {
                                                $.post("/my17u/myseventyu/GetUserRelation", { "memberId": pass.user.UserId, "friend": fid }, function (res) {
                                                    str.removeClass("meiguanzhu");
                                                    if (res == 1) {
                                                        str.addClass("yiguanzhu");
                                                        str.find("a").html('已关注');
                                                        str.after("<span class=\"quxiaoguanzhulo\"><a href=\"javascript:unFollow(0)\">取消关注</a></span>");
                                                    }
                                                    if (res == 2) {
                                                        str.addClass("xianghuguanzhu");
                                                        str.find("a").html('相互关注')
                                                        str.after("<span class=\"quxiaoguanzhulo\"><a href=\"javascript:unFollow(0)\">取消关注</a></span>");
                                                    }
                                                    $(".GFNum").css("margin-top", "0px");
                                                });
                                                //window.location.reload();
                                            }
                                            else if (data == 2) {
                                                $.post("/my17u/myseventyu/GetUserRelation", { "memberId": pass.user.UserId, "friend": fid }, function (res) {
                                                    str.removeClass("meiguanzhu");
                                                    if (res == 1) {
                                                        str.addClass("yiguanzhu");
                                                        str.find("a").html('已关注');
                                                        str.after("<span class=\"quxiaoguanzhulo\"><a href=\"javascript:unFollow(0)\">取消关注</a></span>");
                                                    }
                                                    if (res == 2) {
                                                        str.addClass("xianghuguanzhu");
                                                        str.find("a").html('相互关注')
                                                        str.after("<span class=\"quxiaoguanzhulo\"><a href=\"javascript:unFollow(0)\">取消关注</a></span>");
                                                    }
                                                    $(".GFNum").css("margin-top", "0px");
                                                });
                                            }
                                            else if (data == 3) {
                                                $.post("/my17u/myseventyu/GetUserRelation", { "memberId": pass.user.UserId, "friend": fid }, function (res) {
                                                    str.removeClass("meiguanzhu");
                                                    if (res == 1) {
                                                        str.addClass("yiguanzhu");
                                                        str.find("a").html('已关注');
                                                        str.after("<span class=\"quxiaoguanzhulo\"><a href=\"javascript:unFollow(0)\">取消关注</a></span>");
                                                    }
                                                    if (res == 2) {
                                                        str.addClass("xianghuguanzhu");
                                                        str.find("a").html('相互关注')
                                                        str.after("<span class=\"quxiaoguanzhulo\"><a href=\"javascript:unFollow(0)\">取消关注</a></span>");
                                                    }
                                                    $(".GFNum").css("margin-top", "0px");
                                                });
                                            }
                                            location.reload();
                                        })
                                    }
                                }
                                else if (cktype == "-2") {
                                    var pageIndex = parseInt($("#pageIndex").val()) + 1;
                                    var totalPage = parseInt($("#totalPage").val());
                                    $.ajax({
                                        type: "POST",
                                        url: "/my17u/myseventyu/UserFreshPage",
                                        data: "pageIndex=" + pageIndex + "&pageSize=10",
                                        success: function (data) {
                                            $('.newAdd').hide(200);
                                            $('.newsMode').append(data);
                                            if (pageIndex == totalPage) {
                                                $(".moreNewsButton").addClass("none");
                                            }
                                        }
                                    });
                                }
                                else if (cktype == "-3") {
                                    location.reload();
                                }
                                else if (cktype == "-5") {
                                    CloseLoginDiv();
                                    if ($("#hiduserid") != undefined) {
                                        $("#hiduserid").val(pass.user.UserId); //保存当前登录者的ID
                                    }
                                    raiderjsnext();//游记终页的接下来的js操作
                                    return;
                                }
                                else if (cktype == "-4") {
                                    $.post("/my17u/myseventyu/AddFriend", { "memberId": uid, "friend": fid }, function (data) {
                                        if (data == 1) {
                                            $.post("/my17u/myseventyu/GetUserRelation", { "memberId": uid, "friend": fid }, function (res) {
                                                str.removeClass("Sjia");
                                                if (res == 1) {
                                                    str.addClass("Syj");
                                                }
                                                if (res == 2) {
                                                    str.addClass("Sxh");
                                                }
                                            });
                                            //window.location.reload();
                                        }
                                        else if (data == 2) {
                                            alert("对不起，您已经喜欢过此人，但被对方阻止！");
                                        }
                                        else if (data == 3) {
                                            alert("TA已经在你喜欢的人名单里啦！");
                                        }
                                    });
                                }
                            }
                            var urlStr = $("#urlStr").val();
                            if (urlStr == "blog") {

                                if (proJectName == "gonglve") {
                                    window.location = "/youji/edit";
                                } else {
                                    window.location = "/" + proJectName + "/youji/edit";
                                }
                            } else if (urlStr == "17uHomePage") {//一起游个人主页
                                window.location = "http://www.17u.com/my17u/index/";
                            }
                            else if (pagetype != "xieCommet" && cktype != "7") {
                                location.reload();
                            }
                        }
					    else if(pass.user.State=="403"){
                            $('.logInTxtP:eq(0)').html("请滑动验证模块");
                            $('.logInTxtP').css('visibility', 'visible').addClass('sharkClass');
                            setTimeout("$('.logInTxtP').removeClass('sharkClass')", 1500)
                            $('.loginNow').html("登&nbsp;&nbsp;&nbsp;&nbsp;录");
                        }
                        else if (pass.user.State == "14203") {
                            $('.logInTxtP:eq(0)').html("您的账户存在异常，建议您立即<a href='https://passport.ly.com/Passport/GetPassword/'>修改登录密码</a>");
                            $('.logInTxtP').css('visibility', 'visible');
                            setTimeout("$('.logInTxtP').removeClass('sharkClass')", 1500)
                            $('.loginNow').html("登&nbsp;&nbsp;&nbsp;&nbsp;录");
                        }
                        else if (pass.user.State == "14204") {
                            $('.logInTxtP:eq(0)').html("您的密码错误次数过多,请稍微再试");
                            $('.logInTxtP').css('visibility', 'visible');
                            setTimeout("$('.logInTxtP').removeClass('sharkClass')", 1500)
                            $('.loginNow').html("登&nbsp;&nbsp;&nbsp;&nbsp;录");
                        }
                        else {
                            $('.logInTxtP:eq(0)').html("用户名或密码错误，请重新输入");
                            $('.logInTxtP').css('visibility', 'visible').addClass('sharkClass');
                            setTimeout("$('.logInTxtP').removeClass('sharkClass')", 1500)
                            $('.loginNow').html("登&nbsp;&nbsp;&nbsp;&nbsp;录");
                        }
                    }
                });
            }
}

//登录事件
$('.loginNow').live('click', SetUserInfomation);

//关闭登录弹出框
$('#closeLoginBoxs').live('click', CloseLoginDiv);

//关闭登录弹出框
function CloseLoginDiv() {
    $('.dise').addClass('none');
    $('.loginBoxs').addClass('animationOut none');
    $('#BgDiv').hide();
    //初始化各控件的值
    $('.logInTxtP').css('visibility', 'hidden');
    $('#txtUserName').val("手机号/邮箱");
    $('#txtPassWord').val("");
    $("#xczddl").attr("checked", false);
    $('.loginNow').html("登&nbsp;&nbsp;&nbsp;&nbsp;录");
}

//回车事件
$('.loginRight input').live('keyup', function (e) {
    if (e.keyCode == 13) {
        SetUserInfomation();
    }
});

//关闭右下角浮动快速登录
$('.closeLoginTl a').live('click', function () { $('#loginTl').hide() });
