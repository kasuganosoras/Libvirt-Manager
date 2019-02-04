<?php
Header("Content-type: text/plain");
require_once __DIR__ . '/vendor/autoload.php';
use libvirt_manager\Libvirt;
// Instantiation Libvirt
$Libvirt = new Libvirt();
// Connect to 192.168.3.181:22 and set libvirt root at /data/libvirt/
$Libvirt->setHost("192.168.3.181", 22, "/data/libvirt/");
// Use the username 'root' and the password '123456' login to server.
$Libvirt->connect("root", "123456");
// Print the virtual machine centos's config file.
echo $Libvirt->dumpxml("centos");
