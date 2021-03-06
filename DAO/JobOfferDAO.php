<?php
    namespace DAO;

    use Models\JobOffer as JobOffer;
    use DAO\IJobOfferDAO as IJobOfferDAO;
    use DAO\Connection as Connection;
    use DAO\StudentDAO as StudentDAO;
    use DAO\JobPositionDAO as JobPositionDAO;
    use Models\User as User;
    use Models\Mail as Mail;
    use FFI\Exception;

    class JobOfferDAO implements IJobOfferDAO {

        private $connection;
        private $nameTable;
        private $jobPositionDAO;
        private $userDAO;
        private $companyDAO;
        private $studentList;
        private $studentDAO;

        public function __construct(){
            $this->connection = Connection::GetInstance();
            $this->nameTable = "joboffer";
            $this->jobPositionDAO = new JobPositionDAO();
            $this->userDAO = new UserDAO();
            $this->companyDAO = new CompanyDAO();
            $this->studentList = array();
            $this->studentDAO = new StudentDAO();
        }

        public function Add(JobOffer $jobOffer) {
            $query = "INSERT INTO joboffer (description, dateTime, limitDate, timeState, idJobPosition, idCompany, flyer, notified) value (:description, :dateTime, :limitDate, :timeState, :idJobPosition, :idCompany, :flyer, :notified);";
            
            $parameters['description'] = $jobOffer->getDescription();
            $parameters['dateTime'] = $jobOffer->getDateTime();
            $parameters['limitDate'] = $jobOffer->getLimitDate();
            $parameters['timeState'] = $jobOffer->getTimeState();
            $parameters['idJobPosition'] = $jobOffer->getJobPosition()->getId();
            $parameters['idCompany'] = $jobOffer->getCompany()->getIdCompany();
            $parameters["flyer"] = $jobOffer->getFlyer();
            $parameters['notified'] = $jobOffer->getNotified(); 
        
            try {
                $result = $this->connection->ExecuteNonQuery($query, $parameters);
    
            } catch (Exception $ex) {
                throw $ex;
            }
            return $result;
        }


        public function GetAll() {

            $listJobOffers = [];
    
            $query = " SELECT * FROM " . $this->nameTable ;
    
            try {
                $result = $this->connection->Execute($query);
    
            } catch (Exception $ex) {
                throw $ex;
            }
    
            if(!empty($result)) {
    
                foreach($result as $value){

                    $jobOffer = new JobOffer();
                    
                    $jobOffer->setIdJobOffer($value['id_JobOffer']);
                    $jobOffer->setDescription($value['description']);
                    $jobOffer->setDateTime($value['dateTime']);
                    $jobOffer->setLimitDate($value['limitDate']);
                    $jobOffer->setTimeState($value['timeState']);
                    $jobOffer->setCompany($this->companyDAO->GetCompanyXid($value['idCompany']));
                    $jobOffer->setJobPosition($this->jobPositionDAO->GetJobPositionXid($value['idJobPosition']));
                    $jobOffer->setFlyer($value['flyer']);
                    $jobOffer->setNotified(0);
                    
                    array_push($listJobOffers, $jobOffer);
                }
            }
            return  $listJobOffers;
        }

        public function getStudentList(){
            $studentList = array();

            $query = "SELECT * FROM user_x_joboffer" ;

            try {
                $result = $this->connection->Execute($query);
    
            } catch (Exception $ex) {
                throw $ex;
            }

            if(!empty($result)) {
    
                foreach($result as $value){

                    $user = new User();
                    $user = $this->userDAO->GetUserXid($value['idUser']);

                    array_push( $studentList, $user);
                }
            }
            return $studentList;
        }
        
        public function getPostulationList(){ //devuelve cada id de postulacion con sus respectivos id's(el de student y el de la job offer)

            $postulationsList = array();

            $query = "SELECT * FROM user_x_joboffer" ;

            try {
                $result = $this->connection->Execute($query);
    
            } catch (Exception $ex) {
                throw $ex;
            }

            if(!empty($result)) {
                foreach($result as $value){

                    $postulation['idUserXjoboffer'] = $value['idUserXjoboffer'];
                    $postulation['idJobOffer'] = $value['idJobOffer'];
                    $postulation['idUser'] = $value['idUser'];

                    array_push($postulationsList, $postulation);
                }
            }
            return $postulationsList;
        }

        public function postulationsListForSpecificJobOffer($idJobOffer){

            $postulationsList = array();

            $query = "SELECT * FROM user_x_joboffer" ;

            try {
                $result = $this->connection->Execute($query);
    
            } catch (Exception $ex) {
                throw $ex;
            }

            if(!empty($result)) {
                foreach($result as $value){

                    if($value['idJobOffer'] == $idJobOffer){
                        
                        $studentId = $value['idUser'];
                        array_push($postulationsList, $studentId);
                    }
                }
            }
            return $postulationsList;
        }

        public function DeleteJobOffer($id_jobOffer)
        {
            $sql = "DELETE FROM joboffer WHERE id_JobOffer = :id_JobOffer";
            $parameters['id_JobOffer'] = $id_jobOffer;
     
            try {
                $result = $this->connection->ExecuteNonQuery($sql, $parameters);
    
            }  catch (Exception $exception) {
                throw $exception;
            }
        }
     

        public function updateJobOffer(JobOffer $jobOffer)
        {
            $query = "UPDATE joboffer SET name=:name, description=:description, dateTime=:dateTime, limitDate=:limitDate, timeState=:timeState, idJobPosition=:idJobPosition, idCompany=:idCompany, flyer=:flyer, notified=:notified WHERE  id_JobOffer = :id_JobOffer" ;

            $parameters['description'] = $jobOffer->getDescription();
            $parameters['dateTime'] = $jobOffer->getDateTime();
            $parameters['limitDate'] = $jobOffer->getLimitDate();
            $parameters['timeState'] = $jobOffer->getTimeState();
            $parameters['idJobPosition'] = $jobOffer->getJobPosition()->getId();
            $parameters['idCompany'] = $jobOffer->getCompany()->getIdCompany();
            $parameters['flyer'] = $jobOffer->getFlyer();
            $parameters['notified'] = $jobOffer->getNotified();
            
            try {
                $this->connection = Connection::getInstance();
                return $this->connection->executeNonQuery($query, $parameters);
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        public function searchJobOffer($id_jobOffer)
        {
            $sql = "SELECT * FROM joboffer WHERE id_JobOffer=:id_JobOffer";
            $parameters['id_JobOffer'] = $id_jobOffer;
    
            try {
                $this->connection = Connection::getInstance();
                $this->jobOfferList = $this->connection->execute($sql, $parameters);
            } catch (Exception $exception) {
                throw $exception;
            }
           
            if (!empty($jobOfferList)) {
                return $this->retrieveDataJobOffer();
            } else {
                return false;
            }
        }

        private function retrieveDataJobOffer()
        {
            $listToReturn = array();
    
            foreach ($this->jobOfferList as $value) {
                $jobOffer = new JobOffer();
                $jobOffer->setDescription($value['description']);
                $jobOffer->setDateTime($value['dateTime']);
                $jobOffer->setLimitDate($value['limitDate']);
                $jobOffer->setTimeState($value['timeState']);
                $jobOffer->setCompany($this->companyDAO->GetCompanyXid($value['idCompany']));
                $jobOffer->setJobPosition($this->jobPositionDAO->GetJobPositionXid($value['idJobPosition']));
                $jobOffer->setFlyer($value['flyer']);
                $jobOffer->setNotified($value['notified']);
    
                array_push($listToReturn, $jobOffer);
            }
            return  $listToReturn;
        }

        public function GetJobOfferXid($idJobOffer){

            $query = " SELECT * FROM joboffer WHERE id_JobOffer = (:idJobOffer)"; //company WHERE id_Company = :idCompany";
    
            $parameters['idJobOffer'] = $idJobOffer;
    
            try {
                $result = $this->connection->Execute($query, $parameters);
    
            } catch (Exception $ex) {
                throw $ex;
            }
    
            $jobOffer = null;
    
            if(!empty($result)){
    
                foreach($result as $value){

                    $jobOffer = new JobOffer();

                    $jobOffer->setIdJobOffer($value['id_JobOffer']);
                    $jobOffer->setDescription($value['description']);
                    $jobOffer->setDateTime($value['dateTime']);
                    $jobOffer->setLimitDate($value['limitDate']);
                    $jobOffer->setTimeState($value['timeState']);
                    $jobOffer->setCompany($this->companyDAO->GetCompanyXid($value['idCompany']));
                    $jobOffer->setJobPosition($this->jobPositionDAO->GetJobPositionXid($value['idJobPosition']));
                    $jobOffer->setFlyer($value['flyer']);
                    $jobOffer->setNotified($value['notified']);
                }
            }
            return $jobOffer;
        }


        public function modifyJobOffer($limitDate, $description, $idJobOffer) {

            $sql = "UPDATE joboffer SET description = :description , limitDate = :limitDate WHERE (id_JobOffer = :idJobOffer);" ;
            
            $parameters['limitDate'] = $limitDate;
            $parameters['description'] = $description;
            $parameters['idJobOffer'] = $idJobOffer;
            
            try {
                $result = $this->connection->ExecuteNonQuery($sql, $parameters);
            } catch (Exception $ex) {
                throw $ex;
            }
            return $result; //si retorna 1 actualiz?? si retorna 0 no actualiz??
        }

        public function applyToJobOffer($idUser, $idJobOffer)
        {
            $query =  "INSERT INTO user_x_joboffer (idUser, idJobOffer) value (:idUser, :idJobOffer);";

            $parameters['idUser'] = $idUser;
            $parameters['idJobOffer'] = $idJobOffer;
            
            try {
                $this->connection = Connection::getInstance();
                return $this->connection->executeNonQuery($query, $parameters);
            } catch (Exception $exception) {
                throw $exception;
            } 
        } //si retorna 1 insert?? bien si no retorna 0 


        public function cancelAplicationJobOffer($idJobOffer, $idUser){
            $query = "DELETE FROM user_x_joboffer WHERE idUser = :idUser AND idJobOffer = :idJobOffer;";

            $parameters['idUser'] = $idUser;
            $parameters['idJobOffer'] = $idJobOffer;

            try {
                $this->connection = Connection::getInstance();
                return $this->connection->executeNonQuery($query, $parameters);
            } catch (Exception $exception) {
                throw $exception;
            }
        } //si retorna 1 elimino, si no retorna 0

        public function generateDeclinedApplicationEmail($student, $jobOffer){
            $jobP = $this->jobPositionDAO->GetJobPositionXid($jobOffer->getJobPosition()->getId());
            if(!empty($student)){
                
                $mail = new Mail();
                $mail->sendEmailDeclinatedApplication($student, $jobP); 
            }
        }


        public function checkAlreadyAppliedToSpecificJobOffer($idUser, $idJobOffer){

            $query = "SELECT * FROM user_x_joboffer WHERE idJobOffer = :idJobOffer"; 
    
            $parameters['idJobOffer'] = $idJobOffer;
    
            try {
                $result = $this->connection->Execute($query, $parameters);
    
            } catch (Exception $ex) {
                throw $ex;
            }
    
            $applied = null;
    
            if(!empty($result)){
    
                foreach($result as $value){

                    if($value['idJobOffer']==$idJobOffer && $value['idUser']==$idUser){
                        $applied=true;
                    }
                }
            }
            return $applied;
        }


        public function checkIfCompanyIsAsociated($idCompany){
            $associated=false;
            $jobOfferList = $this->GetAll();

            foreach($jobOfferList as $jobOff){
                
                if($jobOff->getCompany()->getIdCompany() == $idCompany){
                    $associated=true;
                }
            }
            return $associated;
        }

        public function notifyEndedJobOffers(){
            $jobOffList = $this->GetAll();

            foreach ($jobOffList as $jobOff){
                if($jobOff->getLimitDate() < date("Y-m-d")){ //filtro las que corresponden a fechas menores al dia de hoy 

                    if($jobOff->getNotified() != 1){ //checkeo si la finalizacion de la job offer fue notificada
                        $jobOffPostulations = $this->postulationsListForSpecificJobOffer($jobOff->getIdJobOffer());

                        foreach ($jobOffPostulations as $studentId) {

                            $student = $this->userDAO->GetUserXid($studentId);
                            if($student!=null) {
                                $this->generateEndedJobOfferEmail($student, $jobOff);
                            }
                        }
                        $notified = 1;
                        $this->updateNotificatedJobOffer($notified, $jobOff->getIdJobOffer());
                    }
                    
                }
            } 
        }

        public function generateEndedJobOfferEmail($student, $jobOffer){
            $jobP = $this->jobPositionDAO->GetJobPositionXid($jobOffer->getJobPosition()->getId());
            if(!empty($student)){
                
                $mail = new Mail();
                $mail->sendEmailEndedJobOffer($student, $jobP); 
            }
        }

        public function updateNotificatedJobOffer($notified, $idJobOffer) {

            $sql = "UPDATE joboffer SET notified = :notified WHERE (id_JobOffer = :idJobOffer);" ;
            
            $parameters['notified'] = $notified;
            $parameters['idJobOffer'] = $idJobOffer;
            
            try {
                $result = $this->connection->ExecuteNonQuery($sql, $parameters);
            } catch (Exception $ex) {
                throw $ex;
            }
            return $result; //si retorna 1 actualiz?? si retorna 0 no actualiz??
        }

    }

?>