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
}