<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2018031002347311",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEA8KX98NubT6JpRPL7MRp3FWbNHUQDcrrwuwfRhf30/Dv9RDpdzgq+RaF7MMuqFSIHILjtNKHuaB7ehIc7spmfSmetBhbVecP2t5Yixf6CTQomHTN8j0zprsChlMWMr443ZjNww2cTySy6z5eyX4dBJyLbqA8mfrF0zgOh32terOod2MzytJ6DmadaZoS///vYop3FfxAfGg982Smy96K6thfbMoC2zvw3rElTZCt5aq90+DuL5wJSj6MWkG4io7JDlcZyF7DGUCa4i93PP4Y7bRZn7Gd5c0F6cDe3gbgLTowDNAhZtq7gIvrZWstjsMkjYxgkoFDh0DflXOonVIQ4qwIDAQABAoIBAQCCD3R7pHUASSrT1O7lzwPlUTYyRvwGGfrZWpRm8qZhXJq5MUJZhXzobSoDaU93NHjdupSZCZusMmLZBa3CDT0GHZpp3BVsRfklp8MLd049DOskhRsg59S0We/6U/qBNz/BwLOFKESNSdt9LuN8tcEEwdYtsmJ/vrD+VwA5m/IDSUijz3dzi8p0BuTswYlHnrYtN4xJpuZWfvmPjMmhtodzonBIBXATIuFrVu1o7ywKBiPVLisg3pO7CRHR3uyfMRAEzqKtooFP5worYs5qcevxnraoJOHUspM7CjsB+o2PWm59hLLYkCvO/vzvzU5e2IS7kBPmIr8ICy3CadCsFyIhAoGBAPnHtI1VWXhUlUHbHzhbPUPj0WAmb8fU32fm9E4fdOst6nXcpTWo3BW3U0gXZKPbVt8sp1sBZ6AyCdfz63sykOQ85osldg7PcxU/8HVEnRD7n/Aty3OTgZmXjrHiW2whM/EWq79Ovnd0G7HD/K2IZaXyn83PqFyrJIaN28u3whiFAoGBAPakEvdY1AT/Yyc7wpO/oYrM+xqax16pQ/i9i8xLyi15ei6XLp7ktZDIWaJqJTCtIwBGx5672Hk6NOuAjH6TlERyeEqn1bZMzimYj6Pzy+Qx11CjJekRahLDOipWQ/b2tCpXgYw7G0BNMVjNk3zbSwj1n+vNZrZFpDn3NygjbGtvAoGAcXBla5rLm36ums4ti6bEWETrHkPWmGbxX3rgkWpv6y9bQFQJJCmgaqxrwl39cv12orzg9M/ahEK1fcJlu/nnHAEcoy1MRMWqeogjaN7UhpYAuU/TCpZ/UYYnZFptMtqRgHjPX38fuZidy0o7Q4SixY8/THV2/T8sw39Bx2+ZUxECgYEApPbU4K9/D+CpNwKXgC76I1Y61W6UztGz7tkIehpvHb9UY3km64vZjP8URLduLIKBGJw+xAKsJVkzMBkI31hiIO3XW0eqf4YblmK+IFDeHMDhXMPihWLqaaY+bzbHupUDqBZjRaH69iUrTlQhw68BHvr6fcMiYeNuln6tW6Fl0O0CgYAgNHiDbRbD0ZlXAXTnKLuGm3Q8Bv+AUn9aT6Hlx6xvj7xoePe0molxoRlhrtMUduowXblBdCUGvyQvAS9q5p6jKwOP8qwVgoNuhYrUAwPi03oGi6przqW7zW+x35UFR+1bnthUAEkFJ19Uj7mTvtH1Iq5x5iT1iGFCJuylFCj1jg==",
		
		//异步通知地址
		'notify_url' => "http://xn.ixn100.com/alipay.trade.page.pay-PHP-UTF-8/notify_url.php",
		
		//同步跳转
		'return_url' => "http://xn.ixn100.com/alipay.trade.page.pay-PHP-UTF-8/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA8KX98NubT6JpRPL7MRp3FWbNHUQDcrrwuwfRhf30/Dv9RDpdzgq+RaF7MMuqFSIHILjtNKHuaB7ehIc7spmfSmetBhbVecP2t5Yixf6CTQomHTN8j0zprsChlMWMr443ZjNww2cTySy6z5eyX4dBJyLbqA8mfrF0zgOh32terOod2MzytJ6DmadaZoS///vYop3FfxAfGg982Smy96K6thfbMoC2zvw3rElTZCt5aq90+DuL5wJSj6MWkG4io7JDlcZyF7DGUCa4i93PP4Y7bRZn7Gd5c0F6cDe3gbgLTowDNAhZtq7gIvrZWstjsMkjYxgkoFDh0DflXOonVIQ4qwIDAQAB",

);