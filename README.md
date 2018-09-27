# Libvirt-Manager

[中文 ReadMe](README_ZH.md) | [ZeroDream](https://www.zerodream.net/)

This is a simple php libvirt manager

It can start, shutdown, destory, get the virtual machines list or get the information for any virtual machine.

This project is to make it easier for developers to manage virtual machines.

You need install php_ssh2 module to use this manager.

### Example code for connect a server

```php
include("libvirt/libvirt.php");
$Libvirt = new Libvirt();
$Libvirt->setHost("192.168.3.181", 22, "/data/libvirt/");
$Libvirt->connect("root", "123456");
```
This code will connect to your server using SSH, make sure your server sshd service is running.

The `192.168.3.181` is your server hostname, `22` is your server port, and `/data/libvirt/` is your libvirt data save path.

For authenticate, use username and password, the username and password in example code is `root` and `123456`.

### Example code for create a virtual machine

```php
$Libvirt->createDisk("Test", "qcow2", "30G");
$Libvirt->createVMXML("Test", 2, 2048576, "/data/libvirt/images/Test/Test.qcow2", "/data/iso/CentOS-7-x86_64-Minimal-1804.iso", "cdrom", "network", "default", $Libvirt->randomMac(), "virbr0", 0, 0, 5902);
$Libvirt->define("/data/libvirt/Test.xml");
$Libvirt->setPermission("Test");
$Libvirt->start("Test");
```
#### Create a virtual disk
```php
String createDisk ( Name, Format, Size )
```
#### create a Virtual Machine xml config file
The method of createVMXML have 13 args.
```php
void createVMXL ( Name, vCPU, Ram, Disk, ISO, Boot Device, Network type, Network name, MAC Address, Network bridge, Bandwidth in, Bandwidth out, VNC Port )
```
#### Register the xml config file to system
```php
String define ( XML File Path )
```
#### Set execute permission
```php
void setPermission ( Name )
```
#### Start the virtual machine
```php
String start ( Name )
```
#### Stop the virtual machine
```php
String shutdown ( Name )
```
#### Force stop the virtual machine
if your virtual machine in trouble and can't use shutdown to make if off, you can use this method to force stop it.
```php
String destroy ( Name )
```
#### Get the virtual machine list
You can use this method to get the virtual machine list, It will return an array.
```php
String getList ()
```
#### Get the virtual machine information
You can use this method to get any registed virtual machine information, It will return an array.
```php
String getInfo ( Name )
```
#### Dump the virtual machine xml config file
This method can get any registered virtual machine xml config file and return.
```php
String dumpxml ( Name )
```
You can find out more info in `libvirt/libvirt.php`
