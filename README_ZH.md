# Libvirt-Manager
这是一个简单的 Libvirt 虚拟机管理器，使用 PHP 开发。

它可以启动、关闭、强制结束或读取虚拟机信息，列出虚拟机列表等。

这个项目是为了让开发者管理虚拟机更简单，更轻松。

你需要安装 PHP_SSH2 模块来使用此管理器。

### 连接到服务器的示例代码

```php
include("libvirt/libvirt.php");
$Libvirt = new Libvirt();
$Libvirt->setHost("192.168.3.181", 22, "/data/libvirt/");
$Libvirt->connect("root", "123456");
```
Libvirt Manager 需要通过 SSH 连接到服务器进行操作，请确认你的服务器已启用 sshd 服务。

在示例代码中，`192.168.3.181` 是服务器地址，`22` 是服务器端口，`/data/libvirt/` 是你希望 Libvirt 使用的数据储存目录。

在登录时，请使用用户名和密码（目前暂不支持证书登录），示例代码中的用户名和密码分别是 `root` 和 `123456`

### 创建虚拟机示例代码

```php
$Libvirt->createDisk("Test", "qcow2", "30G");
$Libvirt->createVMXML("Test", 2, 2048576, "/data/libvirt/images/Test/Test.qcow2", "/data/iso/CentOS-7-x86_64-Minimal-1804.iso", "cdrom", "network", "default", $Libvirt->randomMac(), "virbr0", 0, 0, 5902);
$Libvirt->define("/data/libvirt/Test.xml");
$Libvirt->setPermission("Test");
$Libvirt->start("Test");
```
#### 创建一个虚拟磁盘
```php
String createDisk ( Name, Format, Size )
```
#### 创建一个虚拟机 XML 配置文件
The method of createVMXML have 13 args.
```php
void createVMXL ( Name, vCPU, Ram, Disk, ISO, Boot Device, Network type, Network name, MAC Address, Network bridge, Bandwidth in, Bandwidth out, VNC Port )
```
#### 将虚拟机 XML 配置文件注册到系统
```php
String defind ( XML File Path )
```
#### 设置可执行权限
```php
void setPermission ( Name )
```
#### 启动虚拟机
```php
String start ( Name )
```
你可以在 `libvirt/libvirt.php` 找到更多信息
