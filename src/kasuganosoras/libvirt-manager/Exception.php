<?php
namespace kasuganosoras\libvirt-manager;
class HostUndefineException extends Exception {
	
	public function __toString() {
	  return "Error: You must set a host before use method connect() in " . __FILE__ . ":" . __LINE__;
	}
}

class LoginFailedException extends Exception {
	
	public function __toString() {
	  return "Error: Failed login to ssh server in " . __FILE__ . ":" . __LINE__;
	}
}

class NoConnectionException extends Exception {
	
	public function __toString() {
	  return "Error: You must connect a host before use this method in " . __FILE__ . ":" . __LINE__;
	}
}