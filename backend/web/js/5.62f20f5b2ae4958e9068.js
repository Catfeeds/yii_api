webpackJsonp([5],{66:function(a,o,e){"use strict";var t=e(1);a.exports=t.module("account",[e(71).name,e(72).name,e(73).name,e(74).name,e(68).name,e(75).name,e(76).name,e(70).name,e(69).name,e(78).name,e(77).name])},68:function(a,o,e){"use strict";function t(a,o,e,t,n,c,i){i.top_menu_tpl="empty",a.email={value:t.email,disabled:!0,local:!0},a.key={value:t.key};var l=o.activate_email({email:a.email.value,key:a.key.value});l.then(function(){n.success().send("恭喜，邮箱激活成功"),e.path("/login"),c.current(function(a){i.USER=a.data||!1})}),l["catch"](function(a){setTimeout(function(){e.path("/register/resend")},3e3)}),l["finally"](function(){a.loading=!1})}var n=e(1);a.exports=n.module("account.activate.email",[]).controller("ActivateEmailController",t),t.$inject=["$scope","Account","location","$routeParams","Flash","User","$rootScope"]},69:function(a,o,e){"use strict";function t(a,o,e,t,n,c){c.top_menu_tpl="empty",a.email={value:o.email,disabled:!0,local:!0},a.captcha={update:!0,type:"fixed"},a.valid=function(){return a.email.valid&&a.captcha.valid},a.submit=function(){if(a.valid()){a.loading=!0;var o=e.forget_password({account:a.email.value,j_captcha:a.captcha.value});o.then(function(a){t.success().send("已经发送邮件"),n.path("/login")}),o["catch"](function(){a.captcha.update=!0}),o["finally"](function(){a.loading=!1})}}}var n=e(1);a.exports=n.module("account.forget-password.email",[]).controller("ForgetPasswordEmailController",t),t.$inject=["$scope","$routeParams","Account","Flash","location","$rootScope"]},70:function(a,o,e){"use strict";function t(a,o,e,t,n){e.top_menu_tpl="empty",a.account={},a.valid=function(){return a.account.valid},a.submit=function(){a.valid()&&(a.loading=!0,"phone"===a.account.type?t.get_phone_iso_code({phone:a.account.value},function(e){null!=e.data&&""!=e.data?e.data.phone_country_code?o.path("/password/reset/phone/"+a.account.value+"/code/"+e.data.phone_country_code.replace("+","")):(a.loading=!1,n.fail().send("无法识别手机地区")):(a.loading=!1,n.fail().send("用户不存在"))}):"email"===a.account.type&&o.path("/password/forget/email/"+a.account.value))}}var n=e(1);a.exports=n.module("account.forget-password",[]).controller("ForgetPasswordController",t),t.$inject=["$scope","location","$rootScope","Account","Flash"]},71:function(a,o,e){"use strict";function t(a,o,e,t,n,c){o.top_menu_tpl="empty";var i=o.$watch("USER",function(a){o.USER&&(n.loginRedirect(),i())});a.account={},a.password={},a.captcha={type:"login",update:!0},a.valid=function(){return a.captcha.needed?a.account.valid&&a.password.valid&&a.captcha.valid:a.account.valid&&a.password.valid},a.submit=function(){if(a.valid()){var n={account:a.account.value,password:SHA1(a.password.value),remember_me:a.remember_me.value};a.captcha.needed&&(n.j_captcha=a.captcha.value),a.loading=!0;var i=e.login(n);i.then(function(a){t.success().send("Welcome! "+a.data.name),o.USER=a.data}),i["catch"](function(o,e){return 3205===e.code?(c.path("/twofa"),!1):void(a.captcha.update=!0)}),i["finally"](function(){a.loading=!1})}}}var n=e(1);a.exports=n.module("account.login",[]).controller("LoginController",t),t.$inject=["$scope","$rootScope","Account","Flash","Common","location"]},72:function(a,o,e){"use strict";function t(a,o,e,t,n){e.top_menu_tpl="empty";var c=e.$watch("USER",function(a){e.USER&&(t.loginRedirect(),c())});a.gk={value:n.search().gk},a.email={},a.password_confirm={},a.captcha={type:"register",update:!0,valid:!0},a.valid=function(){return a.captcha.needed?a.gk.valid&&a.email.valid&&a.password_confirm.valid&&a.captcha.valid:a.gk.valid&&a.email.valid&&a.password_confirm.valid},a.submit=function(){if(a.valid()){var t={global_key:a.gk.value,email:a.email.value,password:SHA1(a.password_confirm.password),confirm:SHA1(a.password_confirm.confirm),invite:n.search().key||void 0};a.captcha.needed&&(t.j_captcha=a.captcha.value),a.loading=!0;var c=o.register(t);c.then(function(a){e.USER=a.data}),c["catch"](function(){a.captcha.update=!0}),c["finally"](function(){a.loading=!1})}},a.registerByPhone=function(){n.path("/register/phone").search("key",n.search().key)}}var n=e(1);a.exports=n.module("account.register.email",[]).controller("RegisterEmailController",t),t.$inject=["$scope","Account","$rootScope","Common","location"]},73:function(a,o,e){"use strict";function t(a,o,e,t,n){e.top_menu_tpl="empty";var c=e.$watch("USER",function(a){e.USER&&(n.loginRedirect(),c())});a.gk={},a.phone={},a.password_confirm={},a.code={type:"register"},a.captcha={type:"register",update:!0,valid:!0},a.valid=function(){return a.captcha.needed?a.gk.valid&&a.phone.valid&&a.password_confirm.valid&&a.code.valid&&a.captcha.valid:a.gk.valid&&a.phone.valid&&a.password_confirm.valid&&a.code.valid},a.submit=function(){if(a.valid()){var n={global_key:a.gk.value,phone:a.phone.value,phoneCountryCode:"+"+a.phone.country_code,country:a.phone.iso_code,password:SHA1(a.password_confirm.password),confirm:SHA1(a.password_confirm.confirm),code:a.code.value,invite:t.search().key||void 0};a.captcha.needed&&(n.j_captcha=a.captcha.value),a.loading=!0;var c=o.register(n);c.then(function(a){e.USER=a.data}),c["catch"](function(o){a.captcha.update=!0}),c["finally"](function(){a.loading=!1})}},a.registerByEmail=function(){t.path("/register/email").search("key",t.search().key)}}var n=e(1);a.exports=n.module("account.register.phone",[]).controller("RegisterPhoneController",t),t.$inject=["$scope","Account","$rootScope","$location","Common"]},74:function(a,o,e){"use strict";function t(a,o,e,t,n,c){o.top_menu_tpl="empty";var i=o.$watch("USER",function(a){o.USER&&(n.loginRedirect(),i())});a.email={local:!0},a.captcha={type:"login",update:!0},a.loading=!1,a.valid=function(){return a.captcha.needed?a.email.valid&&a.captcha.valid:a.email.valid},a.submit=function(){if(a.valid()){var o={email:a.email.value};a.captcha.needed&&(o.j_captcha=a.captcha.value),a.loading=!0;var n=e.send_register_email(o);n.then(function(a){t.success().send("已重新发送激活邮件")}),n["finally"](function(){a.loading=!1,a.captcha.update=!0})}}}var n=e(1);a.exports=n.module("account.register.resend",[]).controller("ResendEmailController",t),t.$inject=["$scope","$rootScope","User","Flash","Common","location"]},75:function(a,o,e){"use strict";function t(a,o,e,t,n,c){c.top_menu_tpl="empty",o.email={value:n.email,disabled:!0,local:!0},o.password_confirm={},o.captcha={update:!0,type:"fixed"},o.key={value:n.key,valid:!1};var i=a.before_reset_password({email:o.email.value,key:o.key.value});i.then(function(){o.key.valid=!0}),i["catch"](function(a,e){o.key.valid=!1,o.password_confirm.disabled=!0,o.captcha.disabled=!0}),o.valid=function(){return o.key.valid&&o.email.valid&&o.password_confirm.valid&&o.captcha.valid},o.submit=function(){if(o.valid()){o.loading=!0;var n=a.reset_password({account:o.email.value,password:SHA1(o.password_confirm.password),confirm:SHA1(o.password_confirm.confirm),key:o.key.value,j_captcha:o.captcha.value});n.then(function(a){e.success().send("重置密码成功"),t.path("/login")}),n["catch"](function(){o.captcha.update=!0}),n["finally"](function(){o.loading=!1})}}}var n=e(1);a.exports=n.module("account.reset-password.email",[]).controller("ResetPasswordEmailController",t),t.$inject=["Account","$scope","Flash","location","$routeParams","$rootScope"]},76:function(a,o,e){"use strict";function t(a,o,e,t,n,c,i){c.top_menu_tpl="empty",a.phone={value:o.phone,disabled:!0,local:!0,valid:i.isPhone(o.phone,"global"),country_code:o.code},a.password_confirm={},a.code={type:"forget"},a.valid=function(){return a.phone.valid&&a.password_confirm.valid&&a.code.valid},a.submit=function(){if(a.valid()){a.loading=!0;var o=e.reset_password({account:a.phone.value,password:SHA1(a.password_confirm.password),confirm:SHA1(a.password_confirm.confirm),code:a.code.value});o.then(function(a){n.success().send("修改成功"),t.path("/login")}),o["finally"](function(){a.loading=!1})}}}var n=e(1);a.exports=n.module("account.reset-password.phone",[]).controller("ResetPasswordPhoneController",t),t.$inject=["$scope","$routeParams","Account","location","Flash","$rootScope","Common"]},77:function(a,o,e){"use strict";function t(a,o,e,t){t.top_menu_tpl="empty",a.phone={local:!0},a.code={type:"twofa"},a.valid=function(){return a.phone.valid&&a.code.valid},a.submit=function(){if(a.valid()){a.loading=!0;var t=o.close_twofa({phone:a.phone.value,code:a.code.value});t.then(function(a){e.path("/login")}),t["finally"](function(){a.loading=!1})}}}var n=e(1);a.exports=n.module("account.twofa.close",[]).controller("TwofaCloseController",t),t.$inject=["$scope","Account","location","$rootScope"]},78:function(a,o,e){"use strict";function t(a,o,e,t,n,c){t.top_menu_tpl="empty";var i=t.$watch("USER",function(a){t.USER&&(n.loginRedirect(),i())});a.code={},a.submit=function(){if(a.code.valid){a.loading=!0;var n=o.twofa({code:a.code.value},function(a){e.success().send("Welcome! "+a.data.name),t.USER=a.data,c.path("/user")});n["finally"](function(){a.loading=!1})}}}var n=e(1);a.exports=n.module("account.twofa",[]).controller("TwofaController",t),t.$inject=["$scope","Account","Flash","$rootScope","Common","$location"]}});