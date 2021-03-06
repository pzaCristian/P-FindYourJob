<?php
    namespace DAO;
    use Models\User as User;
    use DAO\Connection as Connection;
    use DAO\UserTypeDAO as UserTypeDAO;
    use DAO\IUserDAO as IUserDAO;
    use FFI\Exception;

    class UserDAO implements IUserDAO {

        private $connection;
        private $nameTable;
        private $userTypeDao;
   
        public function __construct(){
            $this->connection = Connection::GetInstance();
            $this->nameTable = "user";
            $this->userTypeDao = new UserTypeDAO();
        }

        public function Add(User $user){
            $query = " INSERT INTO ". $this->nameTable . " (email , pass , idUserType) value (:email , :password , :idUserType)";
    
            $parameters['email'] = $user->getEmail();
            $parameters['password'] = $user->getPassword();
            $parameters['idUserType'] = $user->getUserType()->getId();

            try {
                $result = $this->connection->ExecuteNonQuery($query, $parameters);
    
            } catch (\PDOException $ex) {
                throw $ex;
            }
            return $result;
        }

        public function GetAll() {
            $listUsers = [];

            $query = "SELECT * FROM  " . $this->nameTable;

            try {
                $result = $this->connection->Execute($query);
            } catch (\PDOException $ex){
                throw $ex;
            }

            if(!empty($result)) { 
                foreach($result as $value) {
                    $user = new User();

                    $user->setEmail($value['email']);
                    $user->setPassword($value['pass']);
                    $user->setUserType($this->userTypeDao->GetUserTypeXid($value['idUserType']));
                    $user->setId($value['id_User']);
                    
                    array_push($listUsers, $user);
                }
            }
            return $listUsers;
        }

        public function GetAllStudentType() {
            $listUsersStudent = [];

            $query = "SELECT * FROM  " . $this->nameTable;

            try {
                $result = $this->connection->Execute($query);
            } catch (\PDOException $ex){
                throw $ex;
            }

            if(!empty($result)) { 
                foreach($result as $value) {
                    if($value['idUserType']==2){
                        $user = new User();

                        $user->setEmail($value['email']);
                        $user->setPassword($value['pass']);
                        $user->setUserType($this->userTypeDao->GetUserTypeXid($value['idUserType']));
                        $user->setId($value['id_User']);
                        
                        array_push($listUsersStudent, $user);
                    }
                }
            }
            return $listUsersStudent;
        }

        public function DeleteUser($idUser)
        {
            $sql = "DELETE FROM user WHERE id_User = :id_User";
            $parameters['id_User'] = $idUser;
     
            try {
                $this->connection = Connection::getInstance();
                $result = $this->connection->ExecuteNonQuery($sql, $parameters);
    
            }catch (\PDOException $exception) {
                throw $exception;
            }
        }

        public function getUserByEmail($email){
            $userExist = NULL;
            $users = $this->GetAll();

            foreach($users as $user){
                if($user->getEmail() == $email){
                    $userExist = $user;
                }
            }
            return $userExist;
        }

        public function getUserByLog($email,$pass){
            $query = "SELECT * FROM " . $this->nameTable . " WHERE email = :email AND pass = :pass";

            $parameters ['email'] = $email;
            $parameters ['pass'] = $pass;

            try{
                $result = $this->connection->Execute($query,$parameters);
            } catch (Exception $ex){
                throw $ex;
            }

            $user = null; 
            if(!empty($result)){
                foreach($result as $value){
                    $user = new User();
                    $user->setId($value['id_User']);
                    $user->setEmail($value['email']);
                    $user->setPassword($value['pass']);
                    $user->setUserType($this->userTypeDao->GetUserTypeXid($value['idUserType']));
                }
            }
            return $user;
        }

        public function GetUserXid($idUser) {

            $query = " SELECT * FROM " . $this->nameTable . " WHERE id_User = (:id_User)";
    
            $parameters['id_User'] = $idUser;
    
            try {
                $result = $this->connection->Execute($query, $parameters);
    
            } catch (Exception $ex) {
                throw $ex;
            }
    
            $user = null;
            if(!empty($result)){
                foreach($result as $value){
                    $user = new User();

                    $user->setEmail($value['email']);
                    $user->setPassword($value['pass']);
                    $user->setUserType($this->userTypeDao->GetUserTypeXid($value['idUserType']));
                    $user->setId($value['id_User']);
                }
            }
            return $user;
        }

        public function getUserFromDB($email){
            $query = " SELECT * FROM " . $this->nameTable . " WHERE email = (:email)";
            $parameters['email'] = $email;
            try{
                $result = $this->connection->Execute($query, $parameters);
            }catch (Exception $ex){
                throw $ex;
            }
            return $result;
        }



    }

?>