#变量定义  
sqlname="pokermate.sql"  
dir="/home/lansif7z/public_html/pokermate/resource/data"  
host="127.0.0.1"  
user="lansif7z_root"  
passwd="root_Lansiwei123!"  
dbname="lansif7z_pokermate"  
  
    
#导入sql文件到指定数据库  
    mysql -h$host -u$user -p$passwd --default-character-set=utf8 $dbname < $dir/$sqlname
