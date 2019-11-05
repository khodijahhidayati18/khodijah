<?php
set_time_limit(0);
ini_set("display_errors",1); 

class DB
{	private $host="localhost";	
	private $dbname="mq7";
	private $conn;
	
	// koneksi ke database mysql
	private $driver="mysql";
	private $user="root";
	private $password="";
	private $port="3306";
	

	// diload pertama kali
	public function __construct()
	{	try
		{	if ($this->driver == 'mysql')
			{	$this->conn = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=utf8",$this->user,$this->password);	
			} elseif ($this->driver == 'pgsql')
			{	$this->conn = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->dbname;user=$this->user;password=$this->password");	
			}	
		} catch (PDOException $e)
		{	echo "Koneksi gagal";			
		}
	}	

	public function simpan_db($ppm)
	{	$query = $this->conn->prepare("INSERT INTO gas (ppm) VALUES (:ppm)");	
		$query->execute(array(':ppm'=>$ppm));
		$query = null;	 
		unset($ppm);
	}
	
	public function __destruct()
	{	unset($this->host,$this->dbname,$this->driver,$this->user,$this->password,$this->port);
	}
}

$aa = new DB();

?>
