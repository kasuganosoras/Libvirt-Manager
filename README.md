# Libvirt-Manager
This is a simple php libvirt manager

It can start, shutdown, destory, get the virtual machines list or get the information for any virtual machine.

This project is to make it easier for developers to manage virtual machines.

### 中文 ReadMe

这是一个简单的 Libvirt 虚拟机管理器，使用 PHP 开发。

它可以启动、关闭、强制结束或读取虚拟机信息，列出虚拟机列表等。

这个项目是为了让开发者管理虚拟机更简单，更轻松。

### Example code for connect a server / 连接到服务器的示例代码

```php
include("libvirt/libvirt.php");
$Libvirt = new Libvirt();
$Libvirt->setHost("192.168.3.181", 22, "/data/libvirt/");
$Libvirt->connect("root", "123456");
```
This code will connect to your server using SSH, make sure your server sshd service is running.

The `192.168.3.181` is your server hostname, `22` is your server port, and `/data/libvirt/` is your libvirt images save path.

For authenticate, use username and password, the username and password in example code is `root` and `123456`.

You can find out more info in `libvirt/libvirt.php`
