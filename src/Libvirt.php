<?php
namespace libvirt_manager;
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
		if(!$this->conn || $this->conn === null) {
			throw new NoConnectionException();
		}
		try {
			$stream = ssh2_exec($this->conn, $data);
			stream_set_blocking($stream, true);
			$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
			$stream_out = stream_get_contents($stream_out);
		}  catch (Exception $e) {
			print($e->getMessage());
			$stream_out = null;
		}
		return $stream_out;
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
	 *	reboot 重启指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function reboot($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh reboot {$server}");
	}
	
	/**
	 *
	 *	suspend 暂停指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function suspend($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh suspend {$server}");
	}
	
	/**
	 *
	 *	resume 恢复指定的虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function resume($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh resume {$server}");
	}
	
	/**
	 *
	 *	save 将指定的虚拟机状态转储到文件
	 *
	 *	@param $server	虚拟机名称
	 *	@param $name	文件名
	 *	@return String	执行结果
	 *
	 */
	public function save($server, $name) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh save {$server} {$name}");
	}
	
	/**
	 *
	 *	restore 从文件中恢复虚拟机的状态
	 *
	 *	@param $name	文件名
	 *	@return String	执行结果
	 *
	 */
	public function restore($name) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh restore {$name}");
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
		$tag_name = ($network_type !== "network") ? "name" : "network";
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
	
	/**
	 *
	 *	changeMac 修改指定虚拟机的网卡 MAC
	 *
	 *	@param $server	虚拟机名称
	 *	@param $newMac	新的网卡 MAC
	 *
	 */
	public function changeMac($server, $newMac = "") {
		$data = $this->dumpxml($server);
		if($newMac == "") {
			$newMac = $this->randomMac();
		}
		$data = preg_replace("/address='([A-Za-z0-9\:]+)'/", "address='{$newMac}'", $data);
		@file_put_contents(__DIR__ . "/{$server}.xml", $data);
		$this->uploadFile(__DIR__ . "/{$server}.xml", $this->libpath . "/{$server}.xml");
		@unlink(__DIR__ . "/{$server}.xml");
	}
	
	/**
	 *
	 *	createDisk 创建新的虚拟磁盘
	 *
	 *	@param $name	虚拟磁盘名称
	 *	@param $type	虚拟磁盘类型（raw / qcow2 ...）
	 *	@param $size	虚拟磁盘容量（GB）
	 *	@return String	执行结果
	 *
	 */
	public function createDisk($name, $type, $size) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$this->runCommand("mkdir " . $this->libpath . "images/{$name}/");
		return $this->runCommand("qemu-img create -f {$type} " . $this->libpath . "images/{$name}/{$name}.{$type} {$size}");// $this->runCommand("qemu-img create -f {$type} {$name} {$size}");
	}
	
	/**
	 *
	 *	cloneVM 克隆指定的虚拟机
	 *
	 *	@param $sname	源虚拟机名称
	 *	@param $dname	目标虚拟机名称
	 *	@param $ddisk	目标虚拟机磁盘文件
	 *	@return String	执行结果
	 *
	 */
	public function cloneVM($sname, $dname, $ddisk = "") {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		if($ddisk === "") {
			$ddisk = $this->libpath . "images/{$dname}/{$dname}.qcow2";
			$this->runCommand("mkdir " . $this->libpath . "images/{$dname}/");
		}
		return $this->runCommand("virt-clone -o {$sname} -n {$dname} --file {$ddisk}");
	}
	
	/**
	 *
	 *	attach_disk 临时挂载磁盘到虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@param $name	虚拟磁盘文件名
	 *	@param $target	虚拟磁盘标签
	 *	@return String	执行结果
	 *
	 */
	public function attach_disk($server, $name, $target) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh attach-disk {$server} " . $this->libpath . "images/{$name} {$target} --cache none");
	}
	
	/**
	 *
	 *	detach_disk 临时卸载虚拟机的磁盘
	 *
	 *	@param $server	虚拟机名称
	 *	@param $target	虚拟磁盘标签
	 *	@return String	执行结果
	 *
	 */
	public function detach_disk($server, $target) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh detach-disk {$server} --target {$target}");
	}
	
	/**
	 *
	 *	attach_iso 挂载 ISO 到虚拟机
	 *
	 *	@param $server	虚拟机名称
	 *	@param $name	ISO 文件名
	 *	@return String	执行结果
	 *
	 */
	public function attach_iso($server, $name) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh attach-disk {$server} {$name} hdb --type cdrom --mode readonly");
	}
	
	/**
	 *
	 *	detach_iso 卸载虚拟机 ISO
	 *
	 *	@param $server	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function detach_iso($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		return $this->runCommand("virsh attach-disk {$server} \"\" hdb --type cdrom --mode readonly");
	}
	
	/**
	 *
	 *	setNetwork 控制虚拟机网卡
	 *
	 *	@param $server	虚拟机名称
	 *	@param $name	网卡名称
	 *	@param $status	启用或禁用网卡 true / false
	 *	@return String	执行结果
	 *
	 */
	public function setNetwork($server, $name, $status = true) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$status = $status === true ? 'up' : 'down';
		return $this->runCommand("virsh domif-setlink {$server} {$name} {$status}");
	}
	
	/**
	 *
	 *	getNetwork 获取虚拟机网卡列表
	 *
	 *	@param $server	虚拟机名称
	 *	@return Array	虚拟机网卡列表数组
	 *
	 */
	public function getNetwork($server) {
		if(!$this->conn) {
			throw new NoConnectionException();
		}
		$list = $this->runCommand("virsh domiflist {$server}");
		$line = explode("\n", $list);
		$data = Array();
		$s = 0;
		for($i = 2; $i < count($line); $i++) {
			$exp = explode(" ", $line[$i]);
			$t = 0;
			foreach($exp as $item) {
				if($item !== "") {
					switch($t) {
						case 0:
							$data[$s]['Interface'] = $item;
							break;
						case 1:
							$data[$s]['Type'] = $item;
							break;
						case 2:
							$data[$s]['Source'] = $item;
							break;
						case 3:
							$data[$s]['Model'] = $item;
							break;
						case 4:
							$data[$s]['MAC'] = $item;
							break;
					}
					$t++;
				}
			}
			$s++;
		}
		return $data;
	}
	
	/**
	 *
	 *	setPermission 设置虚拟机配置文件以及镜像权限
	 *
	 *	@param $name	虚拟机名称
	 *	@return String	执行结果
	 *
	 */
	public function setPermission($name) {
		$data = $this->runCommand("chmod -R 777 " . $this->libpath . "images/{$name}/");
		$data = $this->runCommand("chmod -R 777 " . $this->libpath . "{$name}.xml");
		return $data;
	}
	
	/**
	 *
	 *	randomMac 生成随机网卡 MAC 地址
	 *
	 *	@return String	网卡 MAC 地址
	 *
	 */
	public function randomMac() {
		return "0e:37:6a:" . implode(':', str_split(substr(md5(mt_rand()), 0, 6), 2));
	}
	
	/**
	 *
	 *	changeBoot 修改指定虚拟机的启动设备
	 *
	 *	@param $server	虚拟机名称
	 *	@param $device	设备类型
	 *
	 */
	public function changeBoot($server, $device = "") {
		$data = @file_get_contents($this->libpath . "/{$server}.xml");
		if(empty($data)) {
			return "";
		}
		if($device == "") {
			$device = "hd";
		}
		$dom = new DOMDocument();
		$dom->loadXML($data);
		$domain = $dom->documentElement;
		$os = $domain->getElementsByTagName('os')->item(0);
		$oldboot = $os->getElementsByTagName("boot");
		foreach($oldboot as $item) {
			$os->removeChild($item);
		}
		$boot = $dom->createElement("boot");
		$boot->setAttribute('dev', $device);
		$os->appendChild($boot);
		$data = $dom->saveXML($dom->documentElement);
		// 不知道为啥要两次
		$dom->loadXML($data);
		$domain = $dom->documentElement;
		$os = $domain->getElementsByTagName('os')->item(0);
		$oldboot = $os->getElementsByTagName("boot");
		foreach($oldboot as $item) {
			$os->removeChild($item);
		}
		$boot = $dom->createElement("boot");
		$boot->setAttribute('dev', $device);
		$os->appendChild($boot);
		$data = $dom->saveXML($dom->documentElement);
		@file_put_contents(__DIR__ . "/{$server}.xml", $data);
		$this->uploadFile(__DIR__ . "/{$server}.xml", $this->libpath . "/{$server}.xml");
		@unlink(__DIR__ . "/{$server}.xml");
	}
	
	/**
	 *
	 *	changeBandwidth 修改指定虚拟机的最大带宽速率
	 *
	 *	@param $server	 虚拟机名称
	 *	@param $inbound	 最大下行速率
	 *  @param $outbound 最大上行速率
	 *
	 */
	public function changeBandwidth($server, $in = 0, $out = 0) {
		$data = @file_get_contents($this->libpath . "/{$server}.xml");
		if(empty($data)) {
			return "";
		}
		$dom = new DOMDocument();
		$dom->loadXML($data);
		$domain = $dom->documentElement;
		$devices = $domain->getElementsByTagName('devices')->item(0);
		$interface = $devices->getElementsByTagName('interface')->item(0);
		$bandwidth = $interface->getElementsByTagName('bandwidth')->item(0);
		$inbound = $bandwidth->getElementsByTagName('inbound')->item(0);
		$outbound = $bandwidth->getElementsByTagName('outbound')->item(0);
		$inbound->setAttribute("average", $in);
		$outbound->setAttribute("average", $out);
		$data = $dom->saveXML($dom->documentElement);
		@file_put_contents(__DIR__ . "/{$server}.xml", $data);
		$this->uploadFile(__DIR__ . "/{$server}.xml", $this->libpath . "/{$server}.xml");
		@unlink(__DIR__ . "/{$server}.xml");
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
	
	/**
	 *
	 *	downloadFile 将远程文件下传到本地
	 *
	 *	@param $remote	远程文件和路径
	 *	@param $local	本地文件和路径
	 *
	 */
	public function downloadFile($remote, $local) {
		$sftp = ssh2_sftp($this->conn);
		copy("ssh2.sftp://{$sftp}{$remote}", $local);
	}
}

class HostUndefineException extends \Exception {
	
	public function __toString() {
		return "Error: You must set a host before use method connect() in " . __FILE__ . ":" . __LINE__;
	}
}

class LoginFailedException extends \Exception {
	
	public function __toString() {
		return "Error: Failed login to ssh server in " . __FILE__ . ":" . __LINE__;
	}
}

class NoConnectionException extends \Exception {
	
	public function __toString() {
		return "Error: You must connect a host before use this method in " . __FILE__ . ":" . __LINE__;
	}
}
