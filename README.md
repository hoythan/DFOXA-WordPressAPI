# DFOXA-WordPressAPI
WordPress API 扩展插件,允许使用 WordPres 开发标准的API接口,为基于 WordPress 的前后端分离项目实现便捷轻快的后端开发体验.

## 了解 DFOXA-WordPressAPI


>DFOXA 项目是为了快速开发基于 WordPress API接口所开发的 WordPress 插件,
>它适用于所有的前后端分离项目,例如使用 [Vue.js](https://vuejs.org)、AngularJS、Electron、微信小程序、支付宝小程序等框架开发的项目,
>DFOXA 提供了 WordPress 的所有基本功能接口,包括用户授权、注册、登录、文章、分类、评论等...,
>DFOXA 额外提供了一些优秀的功能,例如可使用SMS接口实现用户账号的注册,登录等功能
>DFOXA 提供的插件模式,弥补了接口的的不足,你可以基于 基础功能 或 WordPress（PHP） 的所有能力独立开发 API接口,例如 开发商城系统 等
>DFOXA 为您解决了数据交互的安全性问题(使用RSA加密),以及跨域、数据缓存、日志记录问题

## 准备工作
* 如果您的项目是一个完全独立的前后端分离项目,
    或您的项目完全用不到WordPress自带的主题功能(不需要前端展示主题),
    建议您移除WordPress的主题功能,只访问WordPress后台进行开发.
 
 ```php
 要完全移除 主题功能 请将 WordPress 程序根目录下的 index.php 文件中的
 define('WP_USE_THEMES', true);
 改为
 define('WP_USE_THEMES', false);
 ```
* 为你的接口配置一个合格的域名,例如(api.domain.com)
* 在微信小程序等相关项目开发时,你需要配置 [https](https://www.vpser.net/build/letsencrypt-certbot.html)


## 配置环境

