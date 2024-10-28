<?php 

class User {

    protected $db;
    protected $table;

    function __construct($s_user, $s_pass, $s_name, $s_host, $table = "users") {
        /**
         * Sınıf kurulum fonksiyonu
         * s_user: veritabanı kullanıcı adı
         * s_pass: veritabanı şifresi
         * s_name: veritabanı adı
         * s_host: veritabanı sunucusu
         * table: kullanıcı verilerinin tutulacağı kısım
         */
        try {
            $this->db = new PDO("mysql:host=".$s_host.";dbname=".$s_name, $s_user, $s_pass);
            $this->table = $table;

            try {
                $result = $this->db->query("SHOW TABLES LIKE '$this->table'");
                if($result->rowCount() < 1){
                    try {
                        $sql = "CREATE TABLE {$this->table} (
                            id INT(11) AUTO_INCREMENT PRIMARY KEY,
                            username VARCHAR(256) NOT NULL,
                            password VARCHAR(512) NOT NULL,
                            registerip VARCHAR(15) NOT NULL,
                            lastloginip VARCHAR(15) NOT NULL,
                            useragent VARCHAR(512) NOT NULL,
                            register TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            lastlogin TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )";
                        $this->db->exec($sql);
                        return true;
                    } catch(PDOException $e) {
                        return "Tablo oluşturma hatası";
                    }
                        
                }
            } catch (PDOException $e) {
                echo "Tablo doğrulama hatası: " . $e->getMessage();
                return false;
            }
            
        } catch (PDOException $e) {
            echo "Sunucu bağlantı hatası, bilgileri kontrol edin! Hata: " . $e->getMessage();
        }
    }

    function checkUsername($username){
        /**
         * Kullanıcı adının müsaitliğini kontrol eden fonksiyon
         */
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $query = $stmt->fetch(PDO::FETCH_ASSOC);
            return $query ? true : false;
        } catch (PDOException $e) {
            return "Kullanıcı adı sorgulama hatası Hata: " . $e->getMessage();
        }
    }

    function checkMulti($ip){
        /**
         * Ip adresinin birden fazla kullanılıp kullanılmadığını kontrol eden fonksiyon
         */
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE registerip = :ip");
            $stmt->bindParam(':ip', $ip);
            $stmt->execute();
            $query = $stmt->fetch(PDO::FETCH_ASSOC);
            return $query ? true : false;
            if(!$query){
                try {
                    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE lastloginip = :ip");
                    $stmt->bindParam(':ip', $ip);
                    $stmt->execute();
                    $query = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $query ? true : false;
                } catch (PDOException $e) {
                    return "Son giriş ip sorgulama hatası Hata: " . $e->getMessage();
                }
            }
        } catch (PDOException $e) {
            return "Kayıt ip sorgulama hatası Hata: " . $e->getMessage();
        }
    }

    function register($username, $password, $ip, $useragent) {        
        /**
         * Kayıt olma fonksiyonu
         * Kullanıcının kullanıcı adı ve ip adresleri kontrol edildikten sonra kayıt işlemi tamamlanır 
         * kullanıcı tarafından girilen şifre password_hash ile benzersiz şekilde şifrelenerek veritabanına saklanır
         * 
         * Durum kodları:
         * -1: kullanıcı adı mevcut
         * -2: Ip adresi kullanılmış
         *  0: Veritabanı hatası
         */
        if($this->checkUsername($username)){
            return -1;
        }
        if($this->checkMulti($ip)){
            return -2;
        }
        try {
            $npassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (username, password, registerip, lastloginip, useragent) VALUES (:username, :password, :registerip, :lastlogin, :useragent)");
        
            // Parametreleri bağla
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $npassword);
            $stmt->bindParam(':registerip', $ip);
            $stmt->bindParam(':lastlogin', $ip);
            $stmt->bindParam(':useragent', $useragent);
            
            return $stmt->execute() ? true : false;
        } catch (PDOException $e) {
            return 0;
        }   
    }

    function login($username, $password, $ip){
        /**
         * Kullanıcı giriş fonksiyonu
         * Giriş işlemi doğrulandıktan sonra kullanıcının son giriş tarihi güncellenir
         * 
         * Durum kodları:
         *  0: Kullanıcı adı ve şifre doğru fakat son giriş tarihi güncellenirken bi hata oluştu
         * -1: Şifre yanlış
         * -2: Kullanıcı bulunamadı
         */
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $query = $stmt->fetch(PDO::FETCH_ASSOC);
            if($query){
                if(password_verify($password,$query['password'])){
                    $sstmt = $this->db->prepare("UPDATE {$this->table} SET lastlogin = CURRENT_TIMESTAMP, lastloginip = :ip WHERE id = :userid");
                    $sstmt->bindParam(':ip', $ip);
                    $sstmt->bindParam(':userid', $query['id']);
                    if($sstmt->execute()){
                        return true;
                    }else{
                        return 0;
                    }
                }else{
                    return -1;
                }
            }else{
                return -2;
            }
        } catch (PDOException $e) {
            return "Kullanıcı doğrulama hatası Hata: " . $e->getMessage();
        }
    }
}

?>
