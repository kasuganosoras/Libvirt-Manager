# Libvirt-Manager

> Note: 这是我第一次研究 Composer，所以这个项目目前无法使用 Composer 导入！等我修改好了以后我会去掉这句话

这是一个简单的 Libvirt 虚拟机管理器，使用 PHP 开发。

它可以启动、关闭、强制结束或读取虚拟机信息，列出虚拟机列表等。

这个项目是为了让开发者管理虚拟机更简单，更轻松。

你需要安装 PHP_SSH2 模块来使用此管理器。

唔...第一次写英文 ReadMe，英文很蹩脚，请不要在意...嘤嘤嘤

### 连接到服务器的示例代码

```php
require_once __DIR__ . '/vendor/autoload.php';
use libvirt_manager\Libvirt;
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
createVMXML 方法一共有 13 个参数
```php
void createVMXML ( Name, vCPU, Ram, Disk, ISO, Boot Device, Network type, Network name, MAC Address, Network bridge, Bandwidth in, Bandwidth out, VNC Port )
```
#### 将虚拟机 XML 配置文件注册到系统
```php
String define ( XML File Path )
```
#### 设置可执行权限
```php
void setPermission ( Name )
```
#### 启动虚拟机
```php
String start ( Name )
```
#### 正常停止虚拟机
```php
String shutdown ( Name )
```
#### 强制停止虚拟机
如果你的虚拟机出现了问题导致不能使用 shutdown 方法停止时，可以使用此方法强制结束虚拟机，但是可能会丢失数据。
```php
String destroy ( Name )
```
#### 获得虚拟机列表
你可以使用此方法获得所有已注册的虚拟机列表，它将会返回一个数组。
```php
String getList ()
```
#### 获得虚拟机信息
你可以使用此方法获得任何已注册的虚拟机信息，它将会返回一个数组。
```php
String getInfo ( Name )
```
#### 导出虚拟机的 XML 配置文件
此方法可以读取虚拟机的 XML 配置文件并返回
```php
String dumpxml ( Name )
```
#### 克隆现有的虚拟机
你可以使用此方法复制一个现有的虚拟机

此方法也许需要消耗很长时间，具体视磁盘性能而定，建议加一行代码 `set_time_limit(120)` 以防止脚本超时。
```php
String cloneVM ( Name, New name, New disk path )
```
#### 设置虚拟机网卡
此方法可以设置虚拟机的网卡

第三个参数是布尔型的，如果赋值是 true，将会启用网卡，如果赋值是 false，将会禁用网卡。
```php
String setNetwork ( Server, Network name, Status )
```
你可以在 `libvirt/libvirt.php` 找到更多信息
