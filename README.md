# 阿里巴巴SDK For PHP

## 概述

本PHP SDK适用于调用阿里巴巴旗下各开放平台的API。目前已经支持：

* 淘宝开放平台API

本SDK完全支持[淘宝开放平台API](http://open.taobao.com/api/api_list.htm?spm=a219a.7386653.1.30.MYVxfa)。

对应Client类名：\AlibabaSDK\Taobao\TaobaoClient

备注：阿里云安全API，即[云盾魔方](http://csc.aliyun.com/)属于这个体系。

* 淘宝开放平台OAuth登录

本SDK完全支持[淘宝开放平台OAuth登录](http://open.taobao.com/doc/detail.htm?id=102635&spm=a219a.7386781.1998342838.19.ryTNmv)。

对应Client类名：\AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient

* 阿里云API

本SDK部分支持[阿里云API](http://develop.aliyun.com/api/?spm=5176.100054.201.108.UyKD0b)。

目前支持ECS和RDS，其余支持尚待进一步扩展开发。

对应Client类名：\AlibabaSDK\Aliyun\AliyunClient


## 特性

* 轻量级胶水层、紧凑型设计，新手进阶均相宜
* 完整的单元测试代码
* 符合PSR-4载入方式
* 内置的依赖注入（Service Locator实现）可快速调用相关Client
* 支持Composer接入：```composer require horseluke/alibaba-sdk```

## 协议

使用Apache License, Version 2.0协议。


```

Copyright 2015 Horse Luke

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

```

## 系统要求

* PHP 5.4或以上
* PHP启用Curl扩展、且安装了OpenSSL（因为要调用HTTPS）

## 使用方法

以下目录有使用方法：

* demo目录：是最原始的使用方法，不依赖任何载入方式

* tests目录下的所有“Example2”开头的目录：根据不同接口和Client进行的测试

建议按照需要，按参数注册不同的Client类单例（比如不同Region来注册不同的Client单例、不同的appkey注册不同的Client单例）。

方法有：

* 使用工厂模式 + 单例模式。

* 使用依赖注入（Dependency Injection）中的Service Locator + 单例模式。

  - 有关Service Locator介绍，可以看 [Github silexphp/Pimple](https://github.com/silexphp/Pimple ) README.md中“Defining Services“部分。
  
  - 如果自己的框架没有实现，可使用SDK已经实现的简单Service Locator：\AlibabaSDK\Integrate\ServiceLocator。
  
    详细用法见目录/demo/Integrate/ServiceLocatorBasicUsage.php


## 文档

（正在撰写中）

## 其它

如果存在使用上的问题、发现bug或者想提建议，请在此发issue、或发邮件到horseluke@126.com。

如果发现安全漏洞，请直接发邮件到horseluke@126.com。

以上反馈信息本人会详细评估，并进行适合的沟通和处理。

![觉得好用，用支付宝支持作者](https://horseluke.github.io/Assets/img/zfb-hb.png)


## 参赛说明

本作品为2015"云朵之上，编码未来"[阿里云开源编程马拉松](http://bbs.aliyun.com/read/256663.html?spm=5176.100131.1.6.urYu37)参赛作品之一。[根据比赛规则](http://www.oschina.net/2015-ali-hackathon#item-rule)，本作品托管在[Git@OSC](http://git.oschina.net/)上。

本作品属于项目选题“基于阿里云安全接口的内容安全微服务”的作品系列之一：SDK部分。

本选题作品系列之二，即基于内容安全检测微服务（暨SDK的工业化（虚构）实际应用集成）开源参赛作品，请访问[horseluke / content-guard-microsrv-aliyun](http://git.oschina.net/horseluke/content-guard-microsrv-aliyun)。

本SDK作品主要服务阿里云安全API，即云盾魔方。由于云盾魔方接口属于淘宝开放平台API体系，故该SDK主要支持淘宝开放平台。

本SDK作品应看作半学术半工程化作品。该含义是：

* 半学术：由于SDK的特殊性质，若不进行实际应用集成，则可用性不高，仅能作为API架构规范和相关实现的研究。

* 半工程化：本SDK实际可用，能集成在符合运行环境的任何项目中，并且在编码过程中以工业界的工程形式进行了完整的编码和测试。



