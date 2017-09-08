# DFOXA-WordPressAPI
WordPress API 扩展插件,允许使用 WordPres 开发标准的API接口,为基于 WordPress 的前后端分离项目实现便捷轻快的后端开发体验.


> 接口文档会在 1-3个月内完善,英文文档会在中文文档完善后的1个月内发布,插件用户请按照组内教程或组内提问.
> Interface documents will be improved within 1-3 months, the English document will be released within 1 month after the improvement of the Chinese document and plug-in users could ask questions in the group.

## 了解 DFOXA-WordPressAPI


>DFOXA 项目是为了快速开发基于 WordPress API接口所开发的 WordPress 插件。
>它适用于所有的前后端分离项目,例如使用 [Vue.js](https://vuejs.org)、AngularJS、[Electron](http://electron.atom.io)、[微信小程序](https://mp.weixin.qq.com/debug/wxadoc/introduction/)、支付宝小程序等框架开发的项目。
>DFOXA 提供了 WordPress 的所有基本功能接口,包括用户授权、注册、登录、文章、分类、评论等...。
>DFOXA 额外提供了一些优秀的功能,例如可使用SMS接口实现用户账号的注册,登录的短信验证码等功能。
>DFOXA 提供的插件模式,弥补了接口的的不足,你可以基于 基础功能 或 WordPress（PHP） 的所有能力独立开发 API接口,例如 开发商城系统 等...。
>DFOXA 为您解决了数据交互的安全性问题(使用RSA加密),以及跨域、数据缓存、日志记录问题。
>DFOXA 配置使用了最新的 Composer ,你可以通过 Composer 进行丰富的功能扩展。

## 准备工作
* 如果您的项目是一个完全独立的前后端分离项目,
    或您的项目完全用不到WordPress自带的主题功能(不需要前端展示主题),
    建议您移除WordPress的主题功能,只访问WordPress后台进行开发.
 
 ```php
 // 要完全移除 主题功能 请将 WordPress 程序根目录下的 index.php 文件中的
 define('WP_USE_THEMES', true);
 // 改为
 define('WP_USE_THEMES', false);
 ```
* 为你的接口配置一个合格的域名,例如(api.domain.com) 本文档所有用到的示例域名,都将使用 api.domain.com 作为演示域名,请留意
* 在微信小程序等相关项目开发时,你需要配置 [HTTPS](https://www.vpser.net/build/letsencrypt-certbot.html)
* 固定链接,虽然可能你不需要对外展示你的 WordPress 主题相关内容,但是为了接口的正常使用,你还是得配置一个合适的固定链接,对于固定链接的格式并没有做要求,只要不是默认的<b>?p=123</b>即可,推荐使用的是<b>/%post_id%.html</b>


## 配置环境
> 安装完插件后,您需要在后台先配置你的插件,配置的步骤非常容易。

#### UniqueCode
用于数据加密的加密密钥,建议您直接点击后方的 随机生成 来创建一个符合安全标准的密钥内容
#### RSA加密 公钥/密钥
请访问 [Rand RSA](http://web.chacuo.net/netrsakeypair) 生成RSA加密公钥私钥对 1024位(BIT) 和 PKCS#8 并粘帖至此处
#### API 网关格式
推荐使用默认设置,当然你也可以自定义它们 [ gateway.do => http://api.domain.com/gateway.do ]
网关是一门"大学问" , 访问 [API 网关](https://github.com/hoythan/DFOXA-WordPressAPI/blob/master/md/api.md) 详细了解它
#### 缓存系统设置
暂时只支持 WordPress 自带缓存 ,它基于 Memcache（d）。
未来将扩展开发基于 文件缓存、Redis 的缓存功能,但现阶段你只能这么选。
你必须配置 Memcache（d）,并安装相关的 WordPress 内存缓存插件,因为接口将大量使用缓存系统,如果你不做这一步,你将无法继续使用该插件

## 接口调试

你的接口必须经过详细调试后才能发布于线上,我们推荐您使用下列工具进行调试,并使用Chrome的相关json格式化插件

工具: [POSTMAN](https://www.getpostman.com/apps)、[PAW](https://paw.cloud)、[JSONViewer 插件](https://github.com/tulios/json-viewer)

## 准备完毕

访问你的接口测试地址,开始你的第一步

https://api.domain.com/gateway.do?method=Tests.dfoxaState

* gateway.do 是您在后台设置页面配置的 API 网关,所有的接口都通过它进行调用,所以你的其他页面不会收到接口影响。
* 小窍门: DFOXA网关请求格式还有另外一种方式,去除 ?method= 并将接口内容中的 . 改为 /

> https://api.domain.com/gateway.do/Tests/dfoxaState</br>
> 同等于</br>
> https://api.domain.com/gateway.do?method=Tests.dfoxaState

* 直接通过浏览器访问你的这个接口,你会看到一条 JSON 格式的 返回值,他的内容如下

```json
{
  "code": 10000,
  "msg": "接口调用成功",
  "sub_msg": "接口调用成功",
  "solution": "",
  "sub_code": "10000",
  "hello": "看起来这个接口已经准备就绪了.",
  "request": null
}

// 错误网关的请求示例

{
  "code": 10002,
  "msg": "错误的请求地址",
  "sub_msg": "当前的接口请求地址有误,请检查后再试",
  "solution": "重新检查你的请求地址是否有误",
  "sub_code": "gateway.empty-method",
  "request": null
}
```
> 所有的接口都会返回类似格式的信息,</br>
> <b>code:</b></br>当接口的 code === 10000 表示这个接口已经调用成功,在使用任意接口的时候,你应当使用 code 值 是否等于 10000 来判断这个接口是否执行成功。</br>
> <b>msg / submsg:</b></br>接口的提示信息,如果接口请求错误,它们会返回请求错误的相关信息, msg 稍短一些 ,submsg 则更加详细一些,这些信息适合暴露给用户,例如账号密码错误等信息。</br>
> <b>solution:</b></br>接口错误的解决办法,通常这个信息不暴露给用户,在你调试的时候使用,返回一些接口错误时的解决方案。</br>
> <b>sub_code:</b></br>这个接口返回一些字符串格式的错误编号,和 code 是一一对应的。例如 code 10002 对应 gateway.empty-method</br>
> <b>request:</b></br>这个接口返回一些用户的请求内容,方便前端进行开发调试。


> 详细的接口使用请参考本页页脚相关链接