<?php
Header("Content-type: text/plain");
include("libvirt/libvirt.php");
$Libvirt = new Libvirt();
$Libvirt->setHost("192.168.3.181", 22, "/data/libvirt/");
$Libvirt->connect("root", "123456");
print_r($Libvirt->dumpxml("centos"));