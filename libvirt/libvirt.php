<?php
include("Exception.php");
class Libvirt {
	
	public $hostname;
	public $port;
	public $libpath;
	public $conn;
	
	/**
	 *
	 *	setHost 设置服务器信息
	 *
	 *	@param $hostname 	SSH 主机名
	 *	@param $port		服务器端口
	 *	@param $libpath		Libvirt 镜像储存位置
	 *
	 */
	public function setHost($hostname = "", $port = 0, $libpath = "") {
		$this->hostname = $hostname;
		$this->libpath = $libpath;
		$this->port = intval($port);
	}
	
	/**
	 *
	 *	connect 连接服务器
	 *
	 *	@param $username	SSH 用户名
	 *	@param $password	SSH 密码
	 *
	 */
	public function connect($username = "", $password = "") {
		if(!$this->hostname || !$this->port || !$this->libpath) {
			throw new HostUndefineException();
		} else {
			try {
				$this->conn = ssh2_connect($this->hostname, $this->port);
				ssh2_auth_password($this->conn, $username, $password);
				if($this->runCommand("whoami") == "") {
					throw new LoginFailedException();
				}
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
	}
	
	/**
	 *
	 *	runCommand 执行 Shell 命令
	 *
	 *	@param $data	需要执行的命令
	 *
	 */
	public function runCommand($data) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$stream = ssh2_exec($this->conn, $data);
		stream_set_blocking($stream, true);
		$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
		return stream_get_contents($stream_out);
	}
	
	/**
	 *
	 *	getList 获取当前主机上的所有虚拟机
	 *
	 *	@return Array 	虚拟机列表
	 *
	 */
	public function getList() {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$list = $this->runCommand("virsh list --all --name");
		$list = explode("\n", $list);
		$data = array();
		foreach($list as $item) {
			if($item !== "") {
				$data[count($data)] = $item;
			}
		}
		return $data;
	}
	
	/**
	 *
	 *	start 启动指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function start($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh start {$server}");
	}
	
	/**
	 *
	 *	destroy 强制关闭指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function destroy($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh destroy {$server}");
	}
	
	/**
	 *
	 *	shutdown 关闭指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function shutdown($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh shutdown {$server}");
	}
	
	/**
	 *
	 *	define 载入指定的虚拟机配置文件
	 *
	 *	@param $xmlfile	XML 文件路径和名称
	 *	@return String	执行结果
	 *
	 */
	public function define($xmlfile) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh define {$xmlfile}");
	}
	
	/**
	 *
	 *	undefine 移除指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function undefine($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh undefine {$server}");
	}
	
	/**
	 *
	 *	dumpxml 输出指定的虚拟机的配置文件
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	虚拟机配置文件
	 *
	 */
	public function dumpxml($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh dumpxml {$server}");
	}
	
	/**
	 *
	 *	getInfo 获取指定的虚拟机的配置信息
	 *
	 *	@param $server	虚拟机名称
	 *	@return Array	虚拟机配置信息数组
	 *
	 */
	public function getInfo($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$info = $this->runCommand("virsh dominfo {$server}");
		$info = explode("\n", $info);
		$data = array();
		foreach($info as $item) {
			if($item !== "") {
				$exp = explode(": ", $item);
				$key = trim($exp[0]);
				$value = trim($exp[1]);
				$data[$key] = $value;
			}
		}
		return $data;
	}
	
	/**
	 *
	 *	createVMXML 创建新的虚拟机配置并上传至服务器
	 *
	 *	@param $server			名称
	 *	@param $vcpu			CPU 数量
	 *	@param $memory			运行内存
	 *	@param $disk			磁盘镜像路径
	 *	@param $iso				ISO 镜像路径
	 *	@param $boot			首选启动设备
	 *	@param $network_type	网卡类型
	 *	@param $network_name	网卡名称
	 *	@param $network_mac		网卡 MAC 地址
	 *	@param $network_bridge	网卡桥接名称
	 *	@param $bandwidth_in	限制下行最大速率（0为不限制）
	 *	@param $bandwidth_out	限制上行最大速率（0为不限制）
	 *	@param $vnc_port		VNC 远程连接端口
	 *
	 */
	public function createVMXML($server, $vcpu, $memory, $disk = "", $iso = "", $boot = "hd", $network_type = "network", $network_name = "default", $network_mac = "", $network_bridge = "", $bandwidth_in = 0, $bandwidth_out = 0, $vnc_port = 5900) {
		// 这一段写的非常骚气请不要在意
		$template = "<domain type='kvm' id='22'>
    <name>{$server}</name>
    <uuid>{$uuid}</uuid>
    <memory unit='KiB'>{$memory}</memory>
    <currentMemory unit='KiB'>{$memory}</currentMemory>
    <vcpu placement='static'>{$vcpu}</vcpu>
    <resource>
        <partition>/machine</partition>
    </resource>
    <os>
        <type arch='x86_64' machine='pc-i440fx-rhel7.0.0'>hvm</type>
        <boot dev='{$boot}'/>
    </os>
    <features>
        <acpi/>
        <apic/>
        <pae/>
    </features>
    <cpu mode='host-passthrough' check='none'/>
    <clock offset='localtime'/>
    <on_poweroff>destroy</on_poweroff>
    <on_reboot>restart</on_reboot>
    <on_crash>destroy</on_crash>
    <devices>
        <emulator>/usr/libexec/qemu-kvm</emulator>
";
		// 如果设置了磁盘文件
		if($disk !== "") {
			$template .=
"        <disk type='file' device='disk'>
            <driver name='qemu' type='qcow2'/>
            <source file='{$disk}'/>
            <backingStore/>
            <target dev='hda' bus='ide'/>
            <alias name='ide0-0-0'/>
            <address type='drive' controller='0' bus='0' target='0' unit='0'/>
        </disk>
";
		}
		
		// 如果设置了 ISO 镜像
		if($iso !== "") {
			$template .=
"        <disk type='file' device='cdrom'>
            <driver name='qemu' type='raw'/>
            <source file='{$iso}'/>
            <backingStore/>
            <target dev='hdb' bus='ide'/>
            <readonly/>
            <alias name='ide0-0-1'/>
            <address type='drive' controller='0' bus='0' target='0' unit='1'/>
        </disk>
";
		}
		
		// 判断网络类型，选择不同的标签
		$tag_name = "network";
		if($network_type !== "network") {
			$tag_name = "name";
		}
		
		$template .=
"        <controller type='usb' index='0' model='piix3-uhci'>
            <alias name='usb'/>
            <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x2'/>
        </controller>
        <controller type='pci' index='0' model='pci-root'>
            <alias name='pci.0'/>
        </controller>
        <controller type='ide' index='0'>
            <alias name='ide'/>
            <address type='pci' domain='0x0000' bus='0x00' slot='0x01' function='0x1'/>
        </controller>
        <interface type='{$network_type}'>
            <mac address='{$network_mac}'/>
            <source {$tag_name}='{$network_name}' bridge='{$network_bridge}'/>
";
		// 如果设置了最大宽带速率
		if($bandwidth_in !== 0 && $bandwidth_out !== 0) {
			$template .=
"            <bandwidth>
                <inbound average='{$bandwidth_in}'/>
                <outbound average='{$bandwidth_out}'/>
            </bandwidth>
";
		}
		$template .=
"            <target dev='vnet0'/>
            <model type='virtio'/>
            <alias name='net0'/>
            <address type='pci' domain='0x0000' bus='0x00' slot='0x03' function='0x0'/>
        </interface>
        <input type='mouse' bus='ps2'>
            <alias name='input0'/>
        </input>
        <input type='keyboard' bus='ps2'>
            <alias name='input1'/>
        </input>
        <graphics type='vnc' port='{$vnc_port}' autoport='yes' listen='0.0.0.0' keymap='en-us'>
            <listen type='address' address='0.0.0.0'/>
        </graphics>
        <video>
            <model type='cirrus' vram='16384' heads='1' primary='yes'/>
            <alias name='video0'/>
            <address type='pci' domain='0x0000' bus='0x00' slot='0x02' function='0x0'/>
        </video>
        <memballoon model='virtio'>
            <alias name='balloon0'/>
            <address type='pci' domain='0x0000' bus='0x00' slot='0x04' function='0x0'/>
        </memballoon>
    </devices>
    <seclabel type='dynamic' model='dac' relabel='yes'>
        <label>+9869:+9869</label>
        <imagelabel>+9869:+9869</imagelabel>
    </seclabel>
</domain>";
		@file_put_contents(__DIR__ . "/{$server}.xml", $template);
		$this->uploadFile(__DIR__ . "/{$server}.xml", $this->libpath . "/{$server}.xml");
		@unlink(__DIR__ . "/{$server}.xml");
	}
	
	public function createDisk($name, $type, $size) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$this->runCommand("mkdir " . $this->libpath . "images/{$name}/");
		return $this->runCommand("qemu-img create -f {$type} " . $this->libpath . "images/{$name}/{$name}.{$type} {$size}");// $this->runCommand("qemu-img create -f {$type} {$name} {$size}");
	}
	
	public function attach_disk($server, $name) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh attach-disk {$server} " . $this->libpath . "images/{$name} vdb --cache none");
	}
	
	public function setPermission($name) {
		$data = $this->runCommand("chmod -R 777 " . $this->libpath . "images/{$name}/");
		$data = $this->runCommand("chmod -R 777 " . $this->libpath . "{$name}.xml");
		return $data;
	}
	
	public function randomMac() {
		return "0e:37:6a:" . implode(':', str_split(substr(md5(mt_rand()), 0, 6), 2));
	}
	
	/**
	 *
	 *	uploadFile 将本地文件上传到服务器
	 *
	 *	@param $local	本地文件和路径
	 *	@param $remote	远程文件和路径
	 *
	 */
	public function uploadFile($local, $remote) {
        $sftp = ssh2_sftp($this->conn);
        $stream = @fopen("ssh2.sftp://{$sftp}{$remote}", 'w');

        if (!$stream) {
            throw new Exception("Could not open file: {$remote}");
		}
        $data = @file_get_contents($local);
        if ($data === false) {
            throw new Exception("Could not open local file: {$local}");
		}
        if (@fwrite($stream, $data) === false) {
            throw new Exception("Could not send data from file: {$local}");
		}
        @fclose($stream);
    }
}